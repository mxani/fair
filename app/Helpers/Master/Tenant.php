<?php

namespace App\Helpers\Master;

class Tenant {
    public static function Load($token){
        config(\File::getrequire("bot/tenants/$token/configs.php"));
        \DB::purge('mysql');
    }
}