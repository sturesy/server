<?php



class MySQLDatabase implements DatabaseConnection
{
    private $mysql;

    function __construct($host, $user, $password, $database)
    {
        $this->mysql =mysql_connect($host, $user, $pwd) or die ("Connection Error");
        mysql_select_db($database, $this->connection) or die("Couldn't Select Database");
    }

    function __destruct()
    {
        if(!is_null($this->connection))
        {
            mysql_close($this->connection);
        }
    }

}