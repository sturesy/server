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

include("databaseclass.php");

/**
 * By specifying an adminpassword, only users with access to the
 * password will be able to login to the admin panel
 */
$admin_password = "CHANGEME!!!";


/**
 * The encryption key is being used for native apps, to prevent "spamming".
 * This can be any arbitrary combination of letters, numbers and special characters
 */
$encryption_key =  "0011223344556677";

/**
 * Setup-Guide for MySQL:
 *
 * Step 1: Edit mysql_host, _db, _user and _pw to match your MySQL-Setup
 * Step 2: Done!
 *
 */
$mysql_host ="localhost";
$mysql_db = "test"; //Databasename
$mysql_user = "test"; // MySQL-Username
$mysql_pw = "test"; // Password for Username

$database = new mysqlconnection($mysql_host, $mysql_user, $mysql_pw, $mysql_db);

?>