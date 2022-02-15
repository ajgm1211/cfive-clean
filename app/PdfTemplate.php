<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PdfTemplate extends Model
{
    protected $table = 'pdf_templates';
    protected $fillable = ['id', 'name', 'description'];
}
