<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Watson\Rememberable\Rememberable;

class StatusQuote extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use Rememberable;
    //
}
