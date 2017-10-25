<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');    // cache for 1 day
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: X-Requested-With");
date_default_timezone_set('Asia/Jakarta');

require 'vendor/autoload.php';
$client = new Predis\Client($_SERVER['REDIS_URL']);

if(isset($_GET['set'])){

  //ambil data $key-> & $value->
  $data = json_decode(file_get_contents('php://input'));
  $value = $data->value;
  /*
  if(is_array($value)){
    $value = json_encode($value);
  }
  */
  $r = $client->set($data->key, $value);
  if(isset($r)){
    $res = [
      'status' => 'success',
      'msg'   => $data->key
    ];
  }
}elseif(isset($_GET['get'])){

  $r = $client->get($_GET['get']);
  $res = $r;

}elseif(isset($_GET['del'])){

  $r = $client->del($_GET['del']);
  $res = $r;

}elseif(isset($_GET['all'])){

  $r = $client->keys('*');
  $res = $r;

}else{
  $res = [
    'status' => 'good',
    'msg'   => 'api urip'
  ];
}

echo json_encode($res);
