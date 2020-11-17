<?php

namespace App\Handlers;

class LfmConfigHandler extends \Unisharp\Laravelfilemanager\Handlers\ConfigHandler
{
    public function userField()
    {
        //parent::userField();
        return \Auth::user()->company_user_id;
        //return auth()->company_user_id();
    }
}
