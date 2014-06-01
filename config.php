<?php 
/*
 * StuReSy - Student Response System
 * Copyright (C) 2012-2014  StuReSy-Team
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


/**
 * By specifying an adminpassword, only users with access to the
 * password will be able to login to the admin panel.
 */
$admin_password = "test";


/**
 * The encryption key is being used for native apps, to prevent "spamming".
 * This can be any arbitrary combination of letters, numbers and special characters.
 * 
 * Length of key must be 16 characters!
 */
$encryption_key =  "0011223344556677";

/**
 * Setup-Guide for MySQL:
 *
 * - Edit mysql_host, _db, _user and _pw to match your MySQL-Setup
 *
 */
$mysql_host = "localhost";  // Host Address
$mysql_db   = "sturesy";    // Databasename
$mysql_user = "username";   // MySQL-Username
$mysql_pw   = "password";   // Password for Username
global $connection;


if (version_compare(phpversion(), '5.3.0', '<')) 
{
    include_once 'database/MySQLDatabase.php';
    $connection = new MySQLDatabase($mysql_host, $mysql_user, $mysql_pw, $mysql_db);
}
else
{
    include_once 'database/MySQL_i_Database.php';
    $connection = new MySQLiDatabase($mysql_host, $mysql_user, $mysql_pw, $mysql_db);
}

    
?>