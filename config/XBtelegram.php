<?php

return [
    'bot-url'=>'https://api.telegram.org/bot',
    'bot-token'=>'447848342:AAHi150t8E6HgqejcDxv10UtKOYAb6SSw_Q',
    'bot-username'=>'botesterbot',
    'meeting-storage'=>'file',
    'callbackQueryJsonFormat'=>true,


    ///> tenant area
    'tenant'=>[
        // 'mysql database name'=>[                                ///> [Optional] target
        //     'message'=>'insert mysql username',                 ///> question or message for get a value
        //     'field'=>'database.connections.mysql.username',     ///> path of config
        //     'default'=>'root',                                  ///> [Optional] default value
        // ],

        'database name'=>[
            'message'=>'insert token of new bot',
            'field'=>'XBtelegram.bot-token',
            'default'=>'215119625:AAGEbfmB-YDedqPIBGdcs6euPU6ro3eSlmc',
        ],
    ],
];
