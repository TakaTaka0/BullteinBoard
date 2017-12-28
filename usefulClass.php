<?php
//dumpの改行クラス
class chkValiable {

  public function dumpValiable ($getValiable) {
     echo('<pre>');
     var_dump($getValiable);
     echo('</pre>');
  }
}


class DbManager {
    const DB_NAME='bullteinBoard';
    const HOST='localhost';
    const UTF='utf8';
    const USER='observer';
    const PASS='test';

    public function pdo () {
        $dsn = "mysql:dbname=".SELF::DB_NAME.";host=".SELF::HOST.";charset=".SELF::UTF;
        $user = SELF::USER;
        $pass = SELF::PASS;

        try {
            $pdo = new PDO($dsn, $user, $pass, array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
        } catch (Exception $e) {
             echo 'error';
             die();
        }

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        return $pdo;
    }

    public function select ($sql){
        $getPdoMethod = $this->pdo();
        $stmt      = $getPdoMethod->query($sql);
        $items     = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $items;
    }

    public function insertTable ($column, $text) {
        $getPdoMethod = $this->pdo();
        $stmt         = $getPdoMethod->prepare("INSERT INTO userData(name, comment) VALUES(?,?)");
        $setTableData = $stmt->execute(array($column, $text));
        return $setTableData;
    }

  public function selectTable ($tableName) {
      // $getTable =$this->$con;
      // $stme     = $getTable->query($tableName);
      // $result   = $stmt->fetchALL(PDO::FETCH_ASSOC);
      // return $result;
  }

  public function getConnection ($name = null) {
      if (is_null($name)){
          return current($this->connections);
      }

      return $this->connections[$name];
  }
}


?>
