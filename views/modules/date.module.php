<?php



class date implements IModule
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
        return '$("#'.$this->id.'").datepicker({format:"dd.mm.yyyy"});';
    }

    /**
     * Echos the HTML code
    */
    public function html()
    {
        ?>
        <div class="text-center">
            <p></p>
    	    <p><?php echo $this->values["text"]?><p>
    	    <input type="text" class="span2 text-center" value="<?php echo date("d.m.Y");?>" name="<?php echo $this->id?>" id="<?php echo $this->id?>">
        </div>
        <?php 
    }
}


?>