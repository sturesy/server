<?php

/**
 * This is the basic Template for controller-classes
 */
class Home
{
	private $databaseconnection;

	function __construct(&$databaseconnection)
	{
		$this->databaseconnection = $databaseconnection;
	}
	function __destruct()
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