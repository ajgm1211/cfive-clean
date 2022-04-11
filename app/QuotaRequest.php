<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuotaRequest extends Model
{
    protected $fillable = ['type', 'payment_type', 'quota', 'remainging_quota', 'company_user_id', 'issued_date', 'due_date', 'status'];
}
