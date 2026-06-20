<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tax Categories
        Schema::create('tax_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name');
            $table->decimal('rate', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Unit of Measures (UOMs)
        Schema::create('uoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name'); // e.g. Piece, Box, Carton
            $table->string('short_name')->nullable(); // e.g. PC, BOX, CTN
            $table->timestamps();
        });

        // 3. Item Types
        Schema::create('item_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Medicine, Consumer Good, Surgical, Medical Device, Service
            $table->timestamps();
        });

        // 4. Item Categories (Hierarchical)
        Schema::create('item_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('item_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Item Brands
        Schema::create('item_brands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        // 6. Manufacturers
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });

        // 7. ITEM MASTER
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('item_code')->unique();
            $table->string('sku')->nullable()->index();
            $table->string('barcode')->nullable()->index();
            $table->string('name');
            $table->foreignId('item_type_id')->constrained('item_types');
            $table->foreignId('category_id')->nullable()->constrained('item_categories');
            $table->foreignId('brand_id')->nullable()->constrained('item_brands');
            $table->foreignId('manufacturer_id')->nullable()->constrained('manufacturers');
            $table->foreignId('uom_id')->nullable()->constrained('uoms'); // Base UOM
            $table->foreignId('tax_category_id')->nullable()->constrained('tax_categories');
            
            // Inventory Behavior
            $table->enum('inventory_type', ['Stock Item', 'Non-Stock Item', 'Service', 'Asset'])->default('Stock Item');
            $table->boolean('track_batch')->default(false);
            $table->boolean('track_expiry')->default(false);
            $table->boolean('track_serial')->default(false);
            
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index(['company_id', 'is_active']);
        });

        // 8. Item UOMs (Multi-UOM Support)
        Schema::create('item_uoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('uom_id')->constrained('uoms')->onDelete('cascade');
            $table->decimal('conversion_factor', 10, 4); // E.g., 1 Box = 10 Pieces (Factor = 10)
            $table->boolean('is_purchase_default')->default(false);
            $table->boolean('is_sales_default')->default(false);
            $table->timestamps();
        });

        // 9. Item Barcodes (Multi-Barcode Support)
        Schema::create('item_barcodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->string('barcode')->unique();
            $table->string('barcode_type')->nullable(); // E.g., Primary, Supplier, Internal
            $table->timestamps();
        });

        // 10. Item Prices
        Schema::create('item_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade'); // Null means global for company
            $table->string('price_type'); // E.g., Retail, Wholesale, Corporate, Purchase
            $table->decimal('price', 15, 4);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->timestamps();
        });

        // 11. Item Medicine Details (Extension Table)
        Schema::create('item_medicine_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->string('generic_name')->nullable();
            $table->string('strength')->nullable();
            $table->string('dosage_form')->nullable();
            $table->string('therapeutic_class')->nullable();
            $table->boolean('is_prescription_required')->default(false);
            $table->boolean('is_controlled_drug')->default(false);
            $table->timestamps();
        });

        // 12. Item Attributes (EAV System)
        Schema::create('item_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('name'); // e.g. Color, Size, Lens Power, Warranty Period
            $table->string('type')->default('string'); // string, number, date, boolean
            $table->timestamps();
        });

        // 13. Item Attribute Values
        Schema::create('item_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('attribute_id')->constrained('item_attributes')->onDelete('cascade');
            $table->string('value');
            $table->timestamps();
            
            $table->unique(['item_id', 'attribute_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_attribute_values');
        Schema::dropIfExists('item_attributes');
        Schema::dropIfExists('item_medicine_details');
        Schema::dropIfExists('item_prices');
        Schema::dropIfExists('item_barcodes');
        Schema::dropIfExists('item_uoms');
        Schema::dropIfExists('items');
        Schema::dropIfExists('manufacturers');
        Schema::dropIfExists('item_brands');
        Schema::dropIfExists('item_categories');
        Schema::dropIfExists('item_types');
        Schema::dropIfExists('uoms');
        Schema::dropIfExists('tax_categories');
    }
};
