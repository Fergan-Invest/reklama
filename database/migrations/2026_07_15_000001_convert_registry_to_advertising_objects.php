<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('registry_requests', function (Blueprint $table) {
            $table->string('advertising_type')->nullable()->index();
            $table->unsignedTinyInteger('advertising_sides')->nullable();
            $table->boolean('has_passport')->default(false);
            $table->text('passport_details')->nullable();
            $table->string('contract_number')->nullable();
            $table->decimal('contract_amount', 15, 2)->nullable();
        });
    }
    public function down(): void
    {
        Schema::table('registry_requests', fn (Blueprint $table) => $table->dropColumn(['advertising_type', 'advertising_sides', 'has_passport', 'passport_details', 'contract_number', 'contract_amount']));
    }
};
