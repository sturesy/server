<?php


class textarea implements IModule
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
        $length = isset($this->values["length"]) ? $this->values["length"] : false;
        $id = $this->id;
        $addonid = "addon-".$this->id;
        
        if($length === false)
        {
            return "";
        }
        else
        {
            return '$("#'.$id.'").keyup(function(){if(this.value.length>'.$length.'){return false}$("#'.$addonid.'").html("Remaining characters: "+('.$length.'-this.value.length))});';
        }
    }

    /**
     * Echos the HTML code
    */
    public function html()
    {
        $length = isset($this->values["length"]) ? $this->values["length"] : false;
        
        $maxlength = $length !== false ? "maxlength=\"$length\"" : "";
        
        ?>
	    <div class="text-center">
	        <p><?php echo $this->values["text"]?></p>
	        <textarea rows="5" cols="60" <?php echo $maxlength?> id="<?php echo $this->id?>"></textarea>
	        <p><span id='<?php echo "addon-".$this->id;?>'><?php echo $length !== false ? "Remaining characters: ".$length : "";?></span></p>
	        <p></p>
	    </div>
        <?php 
    }
}


?>