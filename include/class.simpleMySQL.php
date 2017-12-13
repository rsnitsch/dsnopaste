<?php
// a class for simple use of MySQL
// copyright by Robert Nitsch, 2006
// www.robertnitsch.de

// version 1.1.0.0

if(!defined('SSQL_INC_CHECK')) die('denied!');

class simpleMySQL {

    private $connection;
    private $querycount;
    private $affectedrows;
    public $lasterror;
    public $lastquery;
    
    // constructor
    public function __construct($db_user, $db_pass, $db_name, $db_host='localhost')
    {
        $this->querycount=0;
        $this->affectedrows=0;
        $this->lasterror='';
        $this->lastquery='';
        
        // connect to mysql database
        if($this->connection = @mysqli_connect($db_host, $db_user, $db_pass))
        {
            if(mysqli_select_db($this->connection, $db_name))
                return TRUE;
            else
            {
                $this->saveError('Konnte Datenbank nicht auswählen.');
                $this->connection = FALSE;
                return FALSE;
            }
        }
        else
        {
            $this->saveError('Verbindung zur MySQL-Datenbank fehlgeschlagen.');
            return FALSE;
        }
    }
    
    public function connected()
    {
        if($this->connection)
            return true;
        else
            return false;
    }
    
    public function escape($data)
    {
        return mysqli_real_escape_string($this->connection, $data);
    }
    
    public function sql_query($query)
    {
        $result=FALSE;
        
        $this->lastquery = $query;
        
        if($result=@mysqli_query($this->connection, $query))
        {
            $this->querycount++;
            $this->affectedrows += mysqli_affected_rows($this->connection);
        
            return $result;
        }
        else
        {
            // save the error message
            $this->saveError();
            return FALSE;
        }
    }
    
    public function sql_result($queryid, $row, $column)
    {
        $return=FALSE;
        if($return=$this->mysqli_result($queryid, $row, $column))
        {
            return $return;
        }
        
        $this->saveError();
        return FALSE;
    }
    
    public function sql_num_rows($queryid)
    {
        return mysqli_num_rows($queryid);
    }
    
    public function fetch($queryid)
    {
    	return $this->sql_fetch_assoc($queryid);
    }
    
    public function sql_fetch_assoc($queryid)
    {
        return mysqli_fetch_assoc($queryid);
    }
    
    private function saveError($msg='')
    {
        if(empty($msg))
        {
            $this->lasterror=mysqli_error($this->connection);
        }
        else
        {
            $this->lasterror=$msg;
        }
    }
    
    private function mysqli_result($res, $row, $field=0) { 
        $res->data_seek($row);
        $datarow = $res->fetch_array();
        return $datarow[$field];
    }

};
