<?php

$person=\App\Model\Person::where('telegramID',$this->detect->from->id)->first();

if(empty($person)){
    $this->trigger('default','Master\Greet');
    return;
}elseif($person->type=='tenant'){

    return;
}elseif($person->type=='personnel'){

    return;
}

$this->trigger('default','Master\Greet@mainMenu');