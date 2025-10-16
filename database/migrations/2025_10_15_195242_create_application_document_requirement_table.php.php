<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationDocumentRequirementTable extends Migration
{
   public function up()
{
    if (!Schema::hasTable('application_document_requirement')) {
        Schema::create('application_document_requirement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_requirement_id')->constrained()->onDelete('cascade');
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->unique(
                ['application_id', 'document_requirement_id'],
                'app_doc_req_unique'
            );
        });
    }
}

    public function down()
    {
        Schema::dropIfExists('application_document_requirement');
    }
}
