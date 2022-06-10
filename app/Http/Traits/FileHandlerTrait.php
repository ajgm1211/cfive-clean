<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection as Collection;

trait FileHandlerTrait
{
    public function getFile($disk, $name)
    {
        return Storage::disk($disk)->url($name);
    }

    public function validateFile(Request $request, $inputFileName){
        $file = $request->file($inputFileName);
        $ext = strtolower($file->getClientOriginalExtension());
        $validator = \Validator::make(
            ['ext' => $ext],
            ['ext' => 'in:xls,xlsx,csv']
        );

        return $validator;
    }

    public function storeFile($disk, $file){
        
        $dateTime = new \DateTime();
        $now = $dateTime->format('dmY_His');
        $originalName = $file->getClientOriginalName();
        $name = $now . '_' . $originalName;

        Storage::disk($disk)->put($name, \File::get($file));
        return $name;
    }
}
