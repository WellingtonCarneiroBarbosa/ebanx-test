<?php

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->enum('type', Transaction::TYPES)->index();

            $table->foreignIdFor(Account::class, 'origin_internal_account_id')
                ->nullable()
                ->constrained('accounts');

            $table->foreignIdFor(Account::class, 'destination_internal_account_id')
                ->nullable()
                ->constrained('accounts');

            $table->decimal('amount', 12, 9, true)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
