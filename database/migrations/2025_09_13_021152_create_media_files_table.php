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
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->string('filename'); // Original filename
            $table->string('path'); // Storage path
            $table->string('url'); // Public URL
            $table->string('mime_type');
            $table->bigInteger('file_size'); // In bytes
            $table->json('dimensions')->nullable(); // width, height for images
            
            // File info
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('alt_text')->nullable(); // For images
            $table->json('metadata')->nullable(); // EXIF, etc.
            
            // Organization
            $table->string('folder')->default('uploads'); // Folder organization
            $table->json('tags')->nullable(); // File tags
            
            // Usage tracking
            $table->integer('download_count')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            
            // Variants (thumbnails, sizes)
            $table->json('variants')->nullable(); // Different sizes/formats
            
            // Author
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['mime_type', 'created_at']);
            $table->index(['folder', 'created_at']);
            $table->index(['uploaded_by', 'created_at']);
            $table->index('download_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};
