<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
date_default_timezone_set('Asia/Jakarta');

require 'vendor/autoload.php';
$_SERVER['REDIS_URL'] = 'redis://kopet1234:kopet1234@redis-12525.c1.us-east1-2.gce.cloud.redislabs.com:12525';
//$_SERVER['REDIS_URL'] = 'redis://rediscloud:j5UCD3tVTCkx29F2saIM4vowinbihf1T@redis-16492.c16.us-east-1-2.ec2.cloud.redislabs.com:16492';

$client = new Predis\Client($_SERVER['REDIS_URL']);
$data = json_decode(file_get_contents('php://input'), true);
$key = 'albaqarah_'.$_GET['domain'];

$ers = $client->lrange($key,95,-1);
$tt = [];
foreach($ers as $e){
  $tt[] = json_decode($e);
}
echo json_encode($tt);
die();
if(isset($data)){

  //set data
  $i = $client->rpush($key,json_encode($data));

  //count then delete
  $ll = $client->llen($key);
  if($ll>=10){
    $client->lpop($key);
  }
  echo json_encode(
    ['insert' => $i]
  );

}else{
  $ers = $client->lrange($key,0,-1);
  $tt = [];
  foreach($ers as $e){
    $tt[] = json_decode($e);
  }
  echo json_encode($tt);
}
