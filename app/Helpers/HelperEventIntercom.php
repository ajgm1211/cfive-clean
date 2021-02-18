<?php

namespace App\Helpers;

use App\User;
use Intercom\IntercomClient;

class Intercom
{
    static $client;

    public function __construct()
    {
        self::$client = new IntercomClient('dG9rOmVmN2IwNzI1XzgwMmFfNDdlZl84NzUxX2JlOGY5NTg4NGIxYjoxOjA=', null, ['Intercom-Version' => '1.1']);

    }

    public static function isExist($email)
    {
        $obj = self::$client;

        try {

            $cliente = $obj->users->getUsers(["email" => $email]);

            if ($cliente->total_count != '0') {
                return true;
            } else {
                return false;
            }
        } catch (\Intercom\Exception\IntercomException $e) {
            \Log::error("Ocurrio un  error intercom con el siguiente usuario" . $email);
            return false;
        }

    }

    // EVENTOS PDF

    public static function event_pdfFcl()
    {
        $obj = self::$client;

        if (self::isExist(\Auth::user()->email)) {
            $obj->events->create([
                "event_name" => "PDF FCL",
                "created_at" => strtotime("now"),
                "email" => \Auth::user()->email,
                "metadata" => [
                    "order_date" => strtotime("now"),
                ],
            ]);
        }
    }

    public static function event_pdfLcl()
    {
        $obj = self::$client;

        if (self::isExist(\Auth::user()->email)) {
            $obj->events->create([
                "event_name" => "PDF LCL",
                "created_at" => strtotime("now"),
                "email" => \Auth::user()->email,
                "metadata" => [
                    "order_date" => strtotime("now"),
                ],
            ]);
        }
    }

    // EVENTOS RATES
    /*
    public static function event_selectRate()
    {
    $obj = self::$client;

    if (self::isExist(\Auth::user()->email)) {
    $obj->events->create([
    "event_name" => "SELECT RATE",
    "created_at" => strtotime("now"),
    "email" => \Auth::user()->email,
    "metadata" => [
    "order_date" => strtotime("now"),
    ],
    ]);

    }

    }*/

    public static function event_searchRate()
    {
        $obj = self::$client;

        if (self::isExist(\Auth::user()->email)) {
            $obj->events->create([
                "event_name" => "SEARCH RATE",
                "created_at" => strtotime("now"),
                "email" => \Auth::user()->email,
                "metadata" => [
                    "order_date" => strtotime("now"),
                ],
            ]);
        }
    }

    public static function event_quoteAutomaticFcl()
    {
        $obj = self::$client;

        if (self::isExist(\Auth::user()->email)) {
            $obj->events->create([
                "event_name" => "QUOTE AUTOMATIC FCL",
                "created_at" => strtotime("now"),
                "email" => \Auth::user()->email,
                "metadata" => [
                    "order_date" => strtotime("now"),
                ],
            ]);
        }
    }
    public static function event_quoteAutomaticLcl()
    {
        $obj = self::$client;

        if (self::isExist(\Auth::user()->email)) {
            $obj->events->create([
                "event_name" => "QUOTE AUTOMATIC LCL",
                "created_at" => strtotime("now"),
                "email" => \Auth::user()->email,
                "metadata" => [
                    "order_date" => strtotime("now"),
                ],
            ]);
        }

    }

    public static function event_quoteManualFcl()
    {
        $obj = self::$client;

        if (self::isExist(\Auth::user()->email)) {
            $obj->events->create([
                "event_name" => "QUOTE MANUAL FCL",
                "created_at" => strtotime("now"),
                "email" => \Auth::user()->email,
                "metadata" => [
                    "order_date" => strtotime("now"),
                ],
            ]);
        }
    }

    public static function event_quoteManualLcl()
    {
        $obj = self::$client;

        if (self::isExist(\Auth::user()->email)) {
            $obj->events->create([
                "event_name" => "QUOTE MANUAL LCL",
                "created_at" => strtotime("now"),
                "email" => \Auth::user()->email,
                "metadata" => [
                    "order_date" => strtotime("now"),
                ],
            ]);
        }
    }

    /* public static function event_quoteManualAir()
    {
    $obj = self::$client;

    if (self::isExist(\Auth::user()->email)) {
    $obj->events->create([
    "event_name" => "QUOTE MANUAL AIR",
    "created_at" => strtotime("now"),
    "email" => \Auth::user()->email,
    "metadata" => [
    "order_date" => strtotime("now"),
    ],
    ]);
    }
    }*/
    // EVENTOS CONTRACTS
    /*
    public static function event_contractFcl(){
    $obj  = self::$client;

    $users = User::all()->where('company_user_id','=', \Auth::user()->company_user_id);
    foreach ($users as $u) {
    if(self::isExist($u->email)){
    $obj->events->create([
    "event_name" => "CONTRACT FCL",
    "created_at" => strtotime("now"),
    "email" => $u->email,
    "metadata" => [
    "order_date" => strtotime("now")
    ]
    ]);
    }
    }

    }*/
    public static function event_addContractLcl()
    {
        $obj = self::$client;

        if (self::isExist(\Auth::user()->email)) {
            $obj->events->create([
                "event_name" => "ADD CONTRACT LCL",
                "created_at" => strtotime("now"),
                "email" => \Auth::user()->email,
                "metadata" => [
                    "order_date" => strtotime("now"),
                ],
            ]);
        }

    }
    // EVENTOS GLOBAL CHARGES
    /*
    public static function event_globalChargesFcl(){
    $obj  = self::$client;

    $users = User::all()->where('company_user_id','=', \Auth::user()->company_user_id);
    foreach ($users as $u) {
    if(self::isExist($u->email)){
    $obj->events->create([
    "event_name" => "GLOBALCHARGES FCL",
    "created_at" => strtotime("now"),
    "email" => $u->email,
    "metadata" => [
    "order_date" => strtotime("now")
    ]
    ]);
    }
    }
    }
    public static function event_globalChargesLcl(){
    $obj  = self::$client;

    $users = User::all()->where('company_user_id','=', \Auth::user()->company_user_id);
    foreach ($users as $u) {
    if(self::isExist($u->email)){
    $obj->events->create([
    "event_name" => "GLOBALCHARGES LCL",
    "created_at" => strtotime("now"),
    "email" => $u->email,
    "metadata" => [
    "order_date" => strtotime("now")
    ]
    ]);
    }
    }
    }*/
    // INLANDS
    /*
    public static function event_inlands(){
    $obj  = self::$client;

    $users = User::all()->where('company_user_id','=', \Auth::user()->company_user_id);
    foreach ($users as $u) {
    if(self::isExist($u->email)){
    $obj->events->create([
    "event_name" => "INLANDS",
    "created_at" => strtotime("now"),
    "email" => $u->email,
    "metadata" => [
    "order_date" => strtotime("now")
    ]
    ]);
    }
    }
    }*/

    // COMPAÑIAS y CONTACTOS
    /*
    public static function event_companies(){

    $obj  = self::$client;
    if(self::isExist(\Auth::user()->email)){
    $obj->events->create([
    "event_name" => "COMPANIES",
    "created_at" => strtotime("now"),
    "email" =>  \Auth::user()->email,
    "metadata" => [
    "order_date" => strtotime("now")
    ]
    ]);
    }
    }
    public static function event_contacts(){
    $obj  = self::$client;

    if(self::isExist(\Auth::user()->email)){
    $obj->events->create([
    "event_name" => "CONTACTS",
    "created_at" => strtotime("now"),
    "email" =>  \Auth::user()->email,
    "metadata" => [
    "order_date" => strtotime("now")
    ]
    ]);
    }
    }*/

    // PRICING

/*
public static function event_pricing(){
$obj  = self::$client;

if(self::isExist(\Auth::user()->email)){
$obj->events->create([
"event_name" => "PRICING",
"created_at" => strtotime("now"),
"email" =>  \Auth::user()->email,
"metadata" => [
"order_date" => strtotime("now")
]
]);
}
}
 */
    // REQUEST CONTRACT
    public static function event_add_contract_express()
    {
        $obj = self::$client;

        if (self::isExist(\Auth::user()->email)) {
            $obj->events->create([
                "event_name" => "ADD CONTRACT EXPRESS",
                "created_at" => strtotime("now"),
                "email" => \Auth::user()->email,
                "metadata" => [
                    "order_date" => strtotime("now"),
                ],
            ]);
        }
    }

    public static function event_newRequest()
    {
        $obj = self::$client;

        if (self::isExist(\Auth::user()->email)) {
            $obj->events->create([
                "event_name" => "NEW REQUEST FCL",
                "created_at" => strtotime("now"),
                "email" => \Auth::user()->email,
                "metadata" => [
                    "order_date" => strtotime("now"),
                ],
            ]);
        }
    }
    public static function event_newRequestLCL()
    {
        $obj = self::$client;

        if (self::isExist(\Auth::user()->email)) {
            $obj->events->create([
                "event_name" => "NEW REQUEST LCL",
                "created_at" => strtotime("now"),
                "email" => \Auth::user()->email,
                "metadata" => [
                    "order_date" => strtotime("now"),
                ],
            ]);
        }
    }

}
