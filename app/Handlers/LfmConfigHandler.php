<?php

namespace App\Handlers;

class LfmConfigHandler extends \Unisharp\Laravelfilemanager\Handlers\ConfigHandler
{
    public function userField()
    {
        return \Auth::user()->company_user_id;
        //return parent::userField();
    }
}
