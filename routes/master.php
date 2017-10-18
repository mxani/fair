<?php

$person=\App\Model\Master\Person::where('telegramID',$this->detect->from->id)->first();

if(empty($person)){
    $this->trigger('default','mstGreet');
    return;
}elseif($person->type=='tenant'){

    return;
}elseif($person->type=='personnel'){

    return;
}


$this->trigger('default','mstGreet@mainMenu');