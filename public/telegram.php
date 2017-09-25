<?php 

$data=file_get_contents('php://input');

$json=json_decode($data,true);
$log=$json['update_id'].'.log';
`echo 'input:\n\n$data\n\noutput:\n\n'> ../bot/logs/$log`;
`nohup ../artisan bot '$data' >> ../bot/logs/$log &`;
echo "hi :)";
