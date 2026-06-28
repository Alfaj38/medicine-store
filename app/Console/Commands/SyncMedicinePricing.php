<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Medicine;
use App\Models\Unit;

class SyncMedicinePricing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:medicine-pricing {--csv= : Path to CSV file if available}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync MRP, Secondary Units, and Conversion Factors for medicines from an open dataset or fallback dictionary.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $csvPath = $this->option('csv');
        $dataset = [];

        if ($csvPath && file_exists(base_path($csvPath))) {
            $this->info("Loading data from CSV: $csvPath");
            $file = fopen(base_path($csvPath), 'r');
            $header = fgetcsv($file); // skip header: brand id,brand name,type,slug,dosage form,generic,strength,manufacturer,package container,Package Size

            while (($row = fgetcsv($file)) !== false) {
                if (count($row) >= 9) {
                    $name = trim($row[1]);
                    $dosage_form = trim($row[4]);
                    $strength = trim($row[6]);
                    $package_container = trim($row[8]);

                    $mrp = 0.0;
                    $secondary_unit = 'Piece';
                    $conversion_factor = 1;

                    // Determine primary unit from dosage form
                    $primary_unit = 'Piece';
                    if (stripos($dosage_form, 'tablet') !== false)
                        $primary_unit = 'Tablet';
                    elseif (stripos($dosage_form, 'capsule') !== false)
                        $primary_unit = 'Capsule';
                    elseif (stripos($dosage_form, 'syrup') !== false || stripos($dosage_form, 'suspension') !== false || stripos($dosage_form, 'solution') !== false)
                        $primary_unit = 'Bottle';
                    elseif (stripos($dosage_form, 'injection') !== false || stripos($dosage_form, 'iv') !== false)
                        $primary_unit = 'Vial';
                    elseif (stripos($dosage_form, 'cream') !== false || stripos($dosage_form, 'ointment') !== false)
                        $primary_unit = 'Tube';
                    elseif (stripos($dosage_form, 'sachet') !== false)
                        $primary_unit = 'Sachet';

                    // Parse package container string
                    if (preg_match('/Unit Price:\s*৳\s*([\d\.]+)/i', $package_container, $matches)) {
                        $mrp = (float) $matches[1];
                    } elseif (preg_match('/৳\s*([\d\.]+)/', $package_container, $matches)) {
                        $mrp = (float) $matches[1];
                    }

                    if (preg_match('/\((\d+)\'s\s+([a-zA-Z]+)/i', $package_container, $matches)) {
                        $conversion_factor = (int) $matches[1];
                        $secondary_unit = ucfirst(strtolower($matches[2])); // e.g. Pack, Box
                    } elseif (preg_match('/bottle/i', $package_container)) {
                        $secondary_unit = 'Bottle';
                    }

                    if ($mrp > 0) {
                        $dataset[] = [
                            'name' => $name,
                            'strength' => $strength,
                            'mrp' => $mrp,
                            'primary_unit' => $primary_unit,
                            'secondary_unit' => $secondary_unit,
                            'conversion_factor' => $conversion_factor
                        ];
                    }
                }
            }
            fclose($file);
        } else {
            $this->info("No CSV provided or file not found. Using built-in BD medicine fallback dataset...");
            $dataset = $this->getFallbackDataset();
        }

        $this->info("Loaded " . count($dataset) . " records. Beginning sync...");

        // Create O(1) lookup table by name
        $datasetLookup = [];
        foreach ($dataset as $data) {
            $datasetLookup[strtolower($data['name'])] = $data;
        }

        $medicines = Medicine::all();
        $matchedCount = 0;

        foreach ($medicines as $med) {
            $nameLower = strtolower($med->name);

            if (isset($datasetLookup[$nameLower])) {
                $matchedData = $datasetLookup[$nameLower];

                // Find or create primary unit
                $primaryUnit = Unit::firstOrCreate(
                    ['name' => $matchedData['primary_unit']],
                    ['short_name' => substr($matchedData['primary_unit'], 0, 3)]
                );

                // Find or create the secondary unit
                $secondaryUnit = Unit::firstOrCreate(
                    ['name' => $matchedData['secondary_unit']],
                    ['short_name' => substr($matchedData['secondary_unit'], 0, 3)]
                );

                $med->mrp = $matchedData['mrp'];
                $med->unit_id = $primaryUnit->id;
                $med->secondary_unit_id = $secondaryUnit->id;
                $med->conversion_factor = $matchedData['conversion_factor'];
                $med->save();

                $matchedCount++;
            }
        }

        $this->info("Sync complete! Successfully updated $matchedCount medicine records.");
    }

    private function getFallbackDataset()
    {
        return [
            ['name' => 'Napa', 'strength' => '500mg', 'mrp' => 1.20, 'primary_unit' => 'Tablet', 'secondary_unit' => 'Box', 'conversion_factor' => 500],
            ['name' => 'Napa Extra', 'strength' => '500mg+65mg', 'mrp' => 2.50, 'primary_unit' => 'Tablet', 'secondary_unit' => 'Box', 'conversion_factor' => 200],
            ['name' => 'Seclo', 'strength' => '20mg', 'mrp' => 5.00, 'primary_unit' => 'Capsule', 'secondary_unit' => 'Box', 'conversion_factor' => 120],
            ['name' => 'Maxpro', 'strength' => '20mg', 'mrp' => 5.00, 'primary_unit' => 'Tablet', 'secondary_unit' => 'Box', 'conversion_factor' => 120],
            ['name' => 'Sergel', 'strength' => '20mg', 'mrp' => 7.00, 'primary_unit' => 'Capsule', 'secondary_unit' => 'Box', 'conversion_factor' => 60],
            ['name' => 'Fexo', 'strength' => '120mg', 'mrp' => 8.00, 'primary_unit' => 'Tablet', 'secondary_unit' => 'Box', 'conversion_factor' => 50],
            ['name' => 'Alatrol', 'strength' => '10mg', 'mrp' => 3.00, 'primary_unit' => 'Tablet', 'secondary_unit' => 'Box', 'conversion_factor' => 100],
            ['name' => 'Monas', 'strength' => '10mg', 'mrp' => 15.00, 'primary_unit' => 'Tablet', 'secondary_unit' => 'Box', 'conversion_factor' => 30],
            ['name' => 'Preservin', 'strength' => '', 'mrp' => 200.00, 'primary_unit' => 'Piece', 'secondary_unit' => 'Piece', 'conversion_factor' => 1],
        ];
    }
}
