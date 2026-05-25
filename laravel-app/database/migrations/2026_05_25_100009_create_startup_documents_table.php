<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('startup_documents')) {
            Schema::create('startup_documents', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('startup_id')->constrained();
                $table->string('document_type');
                $table->string('document_name');
                $table->string('file_path');
                $table->unsignedInteger('file_size_kb')->nullable();
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->string('status')->default('Pending');
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('startup_documents');
    }
};
