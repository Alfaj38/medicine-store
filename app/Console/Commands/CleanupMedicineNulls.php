<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Medicine;
use App\Models\Unit;

class CleanupMedicineNulls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medicine:cleanup-nulls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate missing unit_id, secondary_unit_id and conversion_factor for unmatched medicines.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $medicines = Medicine::whereNull('unit_id')->get();
        $this->info("Found " . $medicines->count() . " medicines with missing unit_id. Beginning cleanup...");

        $count = 0;
        foreach ($medicines as $med) {
            $dosage_form = $med->type ?? 'Piece'; // 'type' column holds dosage form like "Tablet"
            
            // Determine primary unit from dosage form
            $primary_unit = 'Piece';
            if (stripos($dosage_form, 'tablet') !== false) $primary_unit = 'Tablet';
            elseif (stripos($dosage_form, 'capsule') !== false) $primary_unit = 'Capsule';
            elseif (stripos($dosage_form, 'syrup') !== false || stripos($dosage_form, 'suspension') !== false || stripos($dosage_form, 'solution') !== false) $primary_unit = 'Bottle';
            elseif (stripos($dosage_form, 'injection') !== false || stripos($dosage_form, 'iv') !== false) $primary_unit = 'Vial';
            elseif (stripos($dosage_form, 'cream') !== false || stripos($dosage_form, 'ointment') !== false) $primary_unit = 'Tube';
            elseif (stripos($dosage_form, 'sachet') !== false) $primary_unit = 'Sachet';

            // Find or create primary unit
            $primaryUnitModel = Unit::firstOrCreate(
                ['name' => $primary_unit],
                ['short_name' => substr($primary_unit, 0, 3)]
            );

            // Default secondary unit is Piece/Box depending on primary unit
            $secondary_unit = ($primary_unit === 'Tablet' || $primary_unit === 'Capsule') ? 'Box' : 'Piece';
            
            $secondaryUnitModel = Unit::firstOrCreate(
                ['name' => $secondary_unit],
                ['short_name' => substr($secondary_unit, 0, 3)]
            );

            $med->unit_id = $primaryUnitModel->id;
            $med->secondary_unit_id = $secondaryUnitModel->id;
            $med->conversion_factor = 1;
            // mrp remains as it is (usually 0)
            $med->save();

            $count++;
        }

        $this->info("Cleanup complete! Successfully fixed $count medicine records.");
    }
}
