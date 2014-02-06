<?php 
/*
 * StuReSy - Student Response System
 * Copyright (C) 2012-2013  StuReSy-Team
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
   
  
class mysqlconnection
{
  private $connection;

  function __construct($host, $user, $pwd, $database) 
  {
    $this->connection = mysqli_connect($host, $user, $pwd, $database) or die ("Connection Error");
  }

  function query($sql)
  {
    return mysqli_query($this->connection, $sql);
  }
  
  function fetch_array($query)
  {
   return mysqli_fetch_array($query);
  }
  
  function fetch_assoc($query)
  {
   return mysqli_fetch_assoc($query);
  }
    
  function num_rows($query)
  {
   return mysqli_num_rows($query);
  }
  
  function affected_rows()
  {
  	return mysqli_affected_rows();
  }
   
  function error()
  {
    return mysqli_error($this->connection);
  }
  
  function escape_string($st)
  {
    return mysqli_escape_string($this->connection, $st);
  }
  
  function sql_result($query_result, $rowName)
  { 	
  	return mysqli_fetch_assoc($query_result)[$rowName];
  }
  
  function db_no_error()
  {
   return "";
  }
   
  function __destruct()
  { 
    if(!is_null($this->connection))
    { 
      mysqli_close($this->connection);
    }
  }

}
?>