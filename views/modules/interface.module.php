<?php


/**
 * Interface for modules
 */
interface IModule
{
    /**
     * Create the module
     * @param array $values is the rest-json-array as used in rest-api
     * @param string $id used for html-id
     */
    public function __construct(array $values, $id);

    /**
     * Returns Javascript code
    */
    public function javascript();

    /**
     * Echos the HTML code
    */
    public function html();


}

?>