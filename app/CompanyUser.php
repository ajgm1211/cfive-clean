<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CompanyUser extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['name','address','phone','currency_id', 'logo','pdf_language','type_pdf','pdf_ammounts','decimals'];

    public function user()
    {
        return $this->hasOne('App\User');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }
}
