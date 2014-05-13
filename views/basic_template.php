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


global $INDEXPHPWASACCESSED;
if($INDEXPHPWASACCESSED !== true)
{
    die('<meta http-equiv="refresh" content="0; url=../index.php" />');
}


include_once 'database/DatabaseConnectionInterface.php';

/**
 * This is the basic Template for controller-classes
 * 
 * order of method calls: constuct, handleCookies, setup, modifiedBodyValues, display, additionalJavascript, destruct
 */
class Home
{
	private $databaseconnection;

	function __construct(DatabaseConnection &$databaseconnection)
	{
		$this->databaseconnection = $databaseconnection;
	}
	function __destruct()
	{
	}

	function setup()
	{
	     
	}
	
	function display()
	{
		echo "Hello";
	}

	/**
	 * Remove function if not necessary
	 * @return string
	 */
	function modifiedBodyValues()
	{
		return ""; // <body> tag not modified
	}


	/**
	 * Remove function if not necessary
	 * @return string
	 */
	function additionalJavascript()
	{
		return ""; // additional javascript
	}
	
	/**
	 * Remove function if not necessary
	 */
	function handleCookies()
	{
	     
	}

}


?>