<?php

return [
    'host'=>'https://138.201.106.71',
    'bot-url'=>'https://api.telegram.org/bot',
    'bot-token'=>'448314040:AAEk7o1F_VMgQ3T2MQMeysio8UK6VFu9Q_Y',
    'bot-username'=>'skydt_test_bot',
    'meeting-storage'=>'file',
    'callbackQuery_autoTrigge'=>true,


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
