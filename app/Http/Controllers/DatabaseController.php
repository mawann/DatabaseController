<?php

namespace App\Http\Controllers;

Class DatabaseController extends Controller {

  public $pdo;
  private $stmt;

  public function __construct() {
    $dbhost = env('DB_HOST');
    $dbname = env('DB_DATABASE');
    $dbuser = env('DB_USERNAME');
    $dbpass = env('DB_PASSWORD');
    $chrset = env('DB_CHARSET');

    if (strtolower(env('DB_CONNECTION')) == 'mysql') {
      if (empty($chrset)) $chrset = 'utf8mb4';
      $dsn = "mysql:host=$dbhost;dbname=$dbname;charset=$chrset";
    }
    elseif (strtolower(env('DB_CONNECTION')) == 'pgsql') {
      if (empty($chrset)) $chrset = 'utf8';
      $dsn = "pgsql:host=$dbhost;dbname=$dbname;options='--client_encoding=$chrset'";
    }
    else {
      echo 'Sorry, this class supports only MySQL/MariaDB and PostgreSQL.';
      die();
    };

    $options = [
      \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
      \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
      \PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $this->pdo = new \PDO($dsn, $dbuser, $dbpass, $options);
  }

  private function bind($query, $parameter) {
    if (!is_array($parameter)) $parameter = array($parameter);
    $this->stmt = $this->pdo->prepare($query);
    foreach($parameter as $key => $value) {
      $nomor = is_integer($key) ? $key + 1 : $key;
      if (is_numeric($value))  $this->stmt->bindValue($nomor, $value, \PDO::PARAM_INT);
      elseif (is_bool($value)) $this->stmt->bindValue($nomor, $value, \PDO::PARAM_BOOL);
      elseif (is_null($value)) $this->stmt->bindValue($nomor, $value, \PDO::PARAM_NULL);
      else                     $this->stmt->bindValue($nomor, $value, \PDO::PARAM_STR);
    };
  }

  public function exists($query, $parameter = []) {
    $this->bind($query, $parameter);
    if ($this->stmt->execute()) {
      $buff = $this->stmt->fetch(\PDO::FETCH_OBJ);
      if (is_object($buff)) return true; else return false;
    }
    else return false;
  }

  public function fetch($query, $parameter = []) {
    $this->bind($query, $parameter);
    if ($this->stmt->execute()) return $this->stmt->fetch(\PDO::FETCH_OBJ);
    else return false;
  }

  public function fetchAll($query, $parameter = []) {
    $this->bind($query, $parameter);
    if ($this->stmt->execute()) return $this->stmt->fetchAll(\PDO::FETCH_OBJ);
    else return false;
  }

  public function execute($query, $parameter = []) {
    $this->bind($query, $parameter);
    return $this->stmt->execute();
  }

  // +----------+
  // | Aliases. |
  // +----------+

  public function first($query, $parameter = []) {
    return $this->fetch($query, $parameter);
  }

  public function get($query, $parameter = []) {
    return $this->fetchAll($query, $parameter);
  }

  public function getAll($query, $parameter = []) {
    return $this->fetchAll($query, $parameter);
  }

}
