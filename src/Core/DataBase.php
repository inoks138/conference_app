<?php

namespace Core;

use PDO, PDOException;

/**
 * PDO Database Class
 */
class Database
{
  private $host = DB_HOST;
  private $user = DB_USER;
  private $pass = DB_PASSWORD;
  private $dbname = DB_NAME;

  private $pdo;
  private $statement;
  private $error;

  public function __construct()
  {
    // Set DSN
    $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
    $options = array(
      PDO::ATTR_PERSISTENT => true,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );

    // Create PDO instance
    try {
      $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
    } catch (PDOException $e) {
      $this->error = $e->getMessage();
      echo $this->error;
    }
  }

  // Prepare statement with query
  public function query($sql)
  {
    $this->statement = $this->pdo->prepare($sql);
  }

  // Bind values
  public function bind($param, $value, $type = null)
  {
    if (is_null($type)) {
      switch (true) {
        case is_int($value):
          $type = PDO::PARAM_INT;
          break;
        case is_bool($value):
          $type = PDO::PARAM_BOOL;
          break;
        case is_null($value):
          $type = PDO::PARAM_NULL;
          break;
        default:
          $type = PDO::PARAM_STR;
      }
    }

    $this->statement->bindValue($param, $value, $type);
  }

  public function execute()
  {
    return $this->statement->execute();
  }

  public function fetchAll($mode=PDO::FETCH_ASSOC)
  {
    $this->execute();
    return $this->statement->fetchAll($mode);
  }

  public function fetch($mode=PDO::FETCH_ASSOC)
  {
    $this->execute();
    return $this->statement->fetch($mode);
  }

  public function rowCount()
  {
    return $this->statement->rowCount();
  }
}
