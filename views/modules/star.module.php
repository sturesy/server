<?php



class star implements IModule
{
    private $values;
    private $id;
    
    function __construct(array $values, $id)
    {    
        $this->values = $values;
        $this->id = $id;
    }

    /**
     * Returns Javascript code
    */
    public function javascript()
    {
        return "";
    }

    /**
     * Echos the HTML code
    */
    public function html()
    {
        ?>
        <div class="text-center">
	        <p/>
		    <p><?php echo $this->values["text"]?></p>
		    <div class="rating">
                <input type="radio" name="<?php echo $this->id?>" value="1" checked="checked"/><span></span>
                <input type="radio" name="<?php echo $this->id?>" value="2" /><span></span>
                <input type="radio" name="<?php echo $this->id?>" value="3" /><span></span>
                <input type="radio" name="<?php echo $this->id?>" value="4" /><span></span>
                <input type="radio" name="<?php echo $this->id?>" value="5" /><span></span>
            </div>
		    <p>
	    </div>
        <?php 
    }
}


?>