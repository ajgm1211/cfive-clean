<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FileTmpGlobalcharge extends Model
{
    protected $table    = "files_tmp_globalchargers";
    protected $fillable = ['account_importation_globalcharge_id', 'name_file'];

}
