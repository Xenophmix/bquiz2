<?php
date_default_timezone_set("Asia/Taipei");

session_start();


class DB
{

  protected $table;
  protected $pdo;
  protected $dsn = "mysql:host=localhost;charset=utf8;dbname=db10_2";
  public $type = [
    1=>'健康新知',
    2=>'菸害防治',
    3=>'癌症防治',
    4=>'慢性病防治'
  ];


  public function __construct($table)
  {
    $this->table = $table;
    $this->pdo = new PDO($this->dsn, 'root', '');
  }


  public function find($id)
  {
    $sql = "SELECT * FROM $this->table";
    if (is_array($id)) {
      $tmp = $this->arrayToSqlArray($id);

      $sql = $sql . " WHERE " . join(" && ", $tmp);
    } else
      $sql = $sql . " WHERE `id` = '$id'";
    return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
  }

  public function all(...$arg)
  {
    $sql = "SELECT * FROM $this->table";

    if (isset($arg[0])) {

      if (is_array($arg[0])) {
        $tmp = $this->arrayToSqlArray($arg[0]);

        $sql = $sql . " WHERE " . join(" && ", $tmp);
      } else
        $sql = $sql . $arg[0];
    }
    if (isset($arg[1])) {
      $sql = $sql . $arg[1];
    }

    return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  }

  public function save($array)
  {
    if (isset($array['id'])) {
      //更新update
      $id = $array['id'];
      unset($array['id']);
      $tmp = $this->arrayToSqlArray($array);
      $sql = "update $this->table set ";
      $sql .= join(",", $tmp);
      $sql .= " where `id`='$id'";
    } else {
      //新增insert
      $cols = array_keys($array);

      $sql = "insert into $this->table (`" . join("`,`", $cols) . "`) 
                                 values('" . join("','", $array) . "')";
    }
    // echo $sql;
    $this->pdo->exec($sql);
  }

  public function del($id)
  {
    $sql = "DELETE FROM $this->table";

    if (is_array($id)) {
      $tmp = $this->arrayToSqlArray($id);

      $sql = $sql . " WHERE " . join(" && ", $tmp);
    } else
      $sql = $sql . " WHERE `id` = '$id'";

    return $this->pdo->exec($sql);
  }

  public function count(...$arg)
  {
    return $this->math('count', ...$arg);
  }

  public function sum($col, ...$arg)
  {
    return $this->math('sum', $col, ...$arg);
  }

  public function max($col, ...$arg)
  {
    return $this->math('max', $col, ...$arg);
  }

  public function min($col, ...$arg)
  {
    return $this->math('min', $col, ...$arg);
  }

  public function avg($col, ...$arg)
  {
    return $this->math('avg', $col, ...$arg);
  }

  public function arrayTosqlArray($array)
  {
    foreach ($array as $key => $value) {
      $tmp[] = "`$key` = '$value'";
    }
    return $tmp;
  }

  private function math($math, ...$arg)
  {
    switch ($math) {
      case 'count':
        $sql = "SELECT COUNT(*) FROM $this->table";
        if (isset($arg[0]))
          $con = $arg[0];
        break;
      default:
        $col = $arg[0];
        if (isset($arg[1])) {
          $con = $arg[1];
        }
        $sql = "SELECT $math($col) FROM $this->table";
    }

    if (isset($con)) {
      if (is_array($con)) {
        $tmp = $this->arrayToSqlArray($con);
        $sql = $sql . " WHERE " . join(" && ", $tmp);
      } else {
        $sql = $sql . $con;
      }
    }
    // print_r($arg);
    // echo $sql;
    return $this->pdo->query($sql)->fetchColumn();
  }
}


function to($url)
{
  header("location:" . $url);
}

function dd($array)
{
  echo "<pre>";
  print_r($array);
  echo "</pre>";
}

function q($sql)
{
  $pdo = new PDO('mysql:host=localhost;charset=utf8;dbname=db10_2', 'root', '');
  return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}


$Total = new DB('total');
$User = new DB('user');
$News = new DB('news');
$Que = new DB('que');
$Log = new DB('log');

if (!isset($_SESSION['total'])) {
  $today = $Total->find(['date' => date("Y-m-d")]);
  if (empty($today)) {
    // 沒有今天的資料->新增
    $today = ['date' => date("Y-m-d"), 'total' => 1];
  } else {
    // 有今天的資料->更新
    $today['total']++;
  }
  $Total->save($today);
  $_SESSION['total'] = 1;
}
