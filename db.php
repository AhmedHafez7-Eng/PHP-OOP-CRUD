<?php

class DB
{
    private $host = "localhost";
    private $dbname = "phpcrudpdo";
    private $user = "root";
    private $password = "";
    private $dbtype = "mysql";
    private $connection;

    public function __construct()
    {
        try {
            // $this->connection = new pdo(
            //     "
            // {$this->dbtype}:host={$this->host};
            // dbname={$this->dbname}",
            //     $this->user,
            //     $this->password
            // );
            $this->connection = new pdo(
                "$this->dbtype:
                    host=$this->host;
                    dbname=$this->dbname",
                $this->user,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo " Fail " . $e->getMessage();
        }
    }

    function getConnection()
    {
        return $this->connection;
    }

    function errMessage($message)
    {
        return $message;
    }

    function getAllRows($table, $data, $condition = '')
    {
        $sql = "SELECT $data from $table $condition";
        //Prepare the query:
        $query = $this->connection->prepare($sql);

        return $query;
    }

    function deleteRow($table, $condition = '')
    {
        $sql = "delete from $table $condition";
        //Prepare the query:
        $query = $this->connection->prepare($sql);

        return $query;
    }

    function showRow($table, $condition)
    {
        return $this->connection->query("select * from $table $condition");
    }

    function insertRow($table, $cols, $data)
    {

        // Query for Insertion
        $sql = "INSERT INTO $table($cols) VALUES($data)";
        //Prepare Query for Execution
        $query = $this->connection->prepare($sql);

        return $query;
    }

    function updateRow($table, $cols, $condition = '')
    {
        // Query for Insertion
        $sql = "update $table set $cols $condition";
        //Prepare Query for Execution
        $query = $this->connection->prepare($sql);

        return $query;
    }
}