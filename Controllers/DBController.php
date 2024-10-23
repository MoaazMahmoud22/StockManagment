<?php 

class DBController
{
    public $dbHost="localhost";
    public $dbUser="root";
    public $dbPassword="";
    public $dbName="elmostafa";
    public $connection;

    public function openConnection()
    {
        $this->connection = new mysqli($this->dbHost, $this->dbUser, $this->dbPassword, $this->dbName);
        if ($this->connection->connect_error)
        {
            echo "Error in Connection : ".$this->connection->connect_error;
            return false;
        }
        else
        {
            return true;
        }
    }

    public function closeConnection()
    {
        if($this->connection)
        {
            $this->connection->close();
        }
        else
        {
            echo "Connection is not opened";
        }
    }

    public function select($qry)
    {
        $result = $this->connection->query($qry);
        if(!$result)
        {
            echo "Error : ".$this->connection->error;
            return false;
        }
        else
        {
             return $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    public function insert($qry)
    {
        $result = $this->connection->query($qry);
        if(!$result)
        {
            echo "Error : ".$this->connection->error;
            return false;
        }
        else
        {
             return $this->connection->insert_id;
        }
    }

    public function delete($qry)
    {
        $result = $this->connection->query($qry);
        if(!$result)
        {
            echo "Error : ".$this->connection->error;
            return false;
        }
        else
        {
             return $result;
        }
    }

    public function update($qry)
    {
        $result = $this->connection->query($qry);
        if(!$result)
        {
            echo "Error : ".$this->connection->error;
            return false;
        }
        else
        {
             return $result;
        }
    }

    public function runQuery($query)
    {
        $result = mysqli_query($this->connection, $query);

        if (!$result) {
            die("Query failed: " . $this->connection->error);
        }

        return $result;
    }
    public function getLastInsertId()
    {
        return $this->connection->insert_id; // or $this->connection->lastInsertId() depending on your DB library
    }
    
    public function executeQuery($query, $params) {
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        $stmt->execute();
        $stmt->close();
        return true;
    }
    public function fetchQuery($query, $params) {
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }


}

?>