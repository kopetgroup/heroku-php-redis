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
  $r = $client->set($data->key,json_encode($value));
  if(isset($r)){
    $res = [
      'status' => 'success',
      'msg'   => $data->key
    ];
  }
}elseif(isset($_GET['get'])){

  $r = $client->get($_GET['get']);
  $res = ['data'=>json_decode($r)];

}elseif(isset($_GET['del'])){

  $r = $client->del($_GET['del']);
  $res = $r;

}elseif(isset($_GET['all'])){

  $r = $client->keys('*');
  $res = $r;

}else{
  
  $r = shell_exec('free -m');
  $r = str_replace(array("\n","\r\n","\r","\t",'    ','   ','  '),' ',$r);

  $r = explode('cached',$r);
  $r = implode($r);
  $r = str_replace(array("\n","\r\n","\r","\t",'    ','   ','  '),' ',$r);
  $r = explode('Mem: ',$r);
  $r = $r[1];
  $r = explode(' ',$r);

  $total  = number_format($r[0]).'Mb';
  $usage  = number_format($r[1]).'Mb';
  $free   = number_format($r[2]).'Mb';

  $uptime = shell_exec("uptime");
  $uptime = str_replace(array("\n","\r\n","\r","\t",'    ','   ','  '),' ',$uptime);
  $uptime = explode(',',$uptime);
  $uptime = $uptime[0];

  $percent  = $r[1]/$r[0];

  if($_SERVER['HTTP_HOST']=='localhost'){
    $persen   = mt_rand(1,100);
  }else{
    $persen   = number_format( $percent * 100, 2 );
  }

  $res = [
    'status' => 'good',
    'dataset' => count($dataset),
    "versi"             => trim(file_get_contents('version')),
    "name"              => str_replace('.herokuapp.com','',$_SERVER['HTTP_HOST']),
    "node"              => gethostname(),
    "server_name"       => $_SERVER['HTTP_HOST'],
    "server_software"   => $_SERVER['SERVER_SOFTWARE'],
    "remote_addr"       => $_SERVER['REMOTE_ADDR'],
    "memtotal"          => $total,
    "memusage"          => $usage,
    "memfree"           => $free,
    "mempercent"        => $persen,
    "uptime"            => trim(str_replace('up  ','',$uptime))
  ];
}

echo json_encode($res);
