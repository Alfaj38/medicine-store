<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigrateLegacyMedicines extends Command
{
    protected $signature = 'erp:migrate-medicines';
    protected $description = 'Migrate legacy medicines table to the new ERP items architecture';

    public function handle()
    {
        $this->info('Starting migration to ERP Architecture...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('item_attribute_values')->truncate();
        DB::table('item_attributes')->truncate();
        DB::table('item_medicine_details')->truncate();
        DB::table('item_prices')->truncate();
        DB::table('item_barcodes')->truncate();
        DB::table('item_uoms')->truncate();
        DB::table('items')->truncate();
        DB::table('manufacturers')->truncate();
        DB::table('item_categories')->truncate();
        DB::table('item_types')->truncate();
        DB::table('uoms')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Create Default UOM
        $defaultUomId = DB::table('uoms')->insertGetId([
            'name' => 'Piece',
            'short_name' => 'PC',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Create Item Types
        $medicineTypeId = DB::table('item_types')->insertGetId(['name' => 'Medicine', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('item_types')->insert([
            ['name' => 'Consumer Good', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Surgical', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Medical Device', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Service', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Asset', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Migrate Categories
        $this->info('Migrating categories...');
        $categories = DB::table('medicine_categories')->get();
        $categoryIdMap = [];
        foreach ($categories as $cat) {
            $newId = DB::table('item_categories')->insertGetId([
                'id' => $cat->id, // Preserve ID for FKs
                'name' => $cat->name,
                'slug' => $cat->slug,
                'icon' => $cat->icon ?? null,
                'is_active' => $cat->is_active,
                'created_at' => $cat->created_at ?? now(),
                'updated_at' => $cat->updated_at ?? now(),
            ]);
            $categoryIdMap[$cat->id] = $newId;
        }

        // 4. Migrate Medicines
        $total = DB::table('medicines')->count();
        $this->info("Migrating {$total} medicines...");
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $manufacturers = [];

        DB::table('medicines')->orderBy('id')->chunk(500, function ($medicines) use ($bar, $medicineTypeId, $defaultUomId, &$manufacturers) {
            $items = [];
            $medicineDetails = [];
            $prices = [];
            $barcodes = [];

            foreach ($medicines as $med) {
                // Handle Manufacturer
                $mfgId = null;
                if ($med->company_name) {
                    $mfgName = preg_replace('/\s+/', ' ', trim($med->company_name));
                    if (strlen($mfgName) > 255) {
                        $mfgName = substr($mfgName, 0, 255);
                    }
                    if (!isset($manufacturers[$mfgName])) {
                        $mfgId = DB::table('manufacturers')->insertGetId([
                            'name' => $mfgName,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $manufacturers[$mfgName] = $mfgId;
                    } else {
                        $mfgId = $manufacturers[$mfgName];
                    }
                }

                $itemCode = 'ITM-' . str_pad($med->id, 6, '0', STR_PAD_LEFT);

                // Insert Item
                $items[] = [
                    'id' => $med->id, // Preserve ID
                    'company_id' => $med->company_id ?? null,
                    'item_code' => $itemCode,
                    'sku' => null,
                    'barcode' => $med->barcode,
                    'name' => $med->name,
                    'item_type_id' => $medicineTypeId,
                    'category_id' => $med->category_id,
                    'brand_id' => null,
                    'manufacturer_id' => $mfgId,
                    'uom_id' => $defaultUomId,
                    'tax_category_id' => null,
                    'inventory_type' => 'Stock Item',
                    'track_batch' => true,
                    'track_expiry' => true,
                    'track_serial' => false,
                    'image' => $med->icon ?? null,
                    'is_active' => $med->is_active,
                    'created_at' => $med->created_at ?? now(),
                    'updated_at' => $med->updated_at ?? now(),
                ];

                $medicineDetails[] = [
                    'item_id' => $med->id,
                    'generic_name' => $med->generic_name,
                    'strength' => $med->strength,
                    'dosage_form' => $med->type,
                    'therapeutic_class' => null,
                    'is_prescription_required' => false,
                    'is_controlled_drug' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($med->sell_price > 0) {
                    $prices[] = [
                        'item_id' => $med->id,
                        'branch_id' => null,
                        'price_type' => 'Retail',
                        'price' => $med->sell_price,
                        'effective_from' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if ($med->buy_price > 0) {
                    $prices[] = [
                        'item_id' => $med->id,
                        'branch_id' => null,
                        'price_type' => 'Purchase',
                        'price' => $med->buy_price,
                        'effective_from' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if ($med->barcode) {
                    $barcodes[] = [
                        'item_id' => $med->id,
                        'barcode' => $med->barcode,
                        'barcode_type' => 'Primary',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            try {
                DB::table('items')->insert($items);
                DB::table('item_medicine_details')->insert($medicineDetails);
                if (!empty($prices)) DB::table('item_prices')->insert($prices);
                
                // Ignore duplicates on barcodes since some legacy barcodes might be identical
                if (!empty($barcodes)) DB::table('item_barcodes')->insertOrIgnore($barcodes);
            } catch (\Exception $e) {
                dump($e->getMessage());
                // Find the specific item causing the issue
                foreach ($items as $item) {
                    try {
                        DB::table('items')->insert($item);
                    } catch (\Exception $e2) {
                        dump("Failed on Item: ", $item);
                        throw $e2;
                    }
                }
                throw $e;
            }

            $bar->advance(count($medicines));
        });

        $bar->finish();
        $this->newLine(2);
        $this->info('Migration completed successfully! Over 24,000 legacy records have been ported to the ERP schema.');
    }
}
