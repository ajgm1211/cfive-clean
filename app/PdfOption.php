<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PdfOption extends Model
{
    public function duplicate($quote){

        $new_option = $this->replicate();
        $new_option->quote_id = $quote->id;
        $new_option->save();

        return $new_option;
    }
}
