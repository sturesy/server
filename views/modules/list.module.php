<?php


/**
 * Interface for modules
 */
class listmodule implements IModule
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
        if(isset($this->values["input"])) {
            return "$('#" . $this->id . "-" . $this->values["input"] . "').button('toggle');";
        }
        else return "";
    }

    /**
     * Echos the HTML code
     */
    public function html()
    {
?>
    <div class="text-center">
        <p><?php echo $this->values["description"]?></p>
        <p>
            <div class="btn-group" data-toggle="buttons">
            <?php
            $selectionCounter = 1;
            foreach($this->values["elements"] as $value){?>
                <label class="btn btn-primary" id="<?php echo $this->id."-".$value?>">
                    <input type="radio" name="<?php echo $this->id?>" value="<?php echo $selectionCounter;?>"><?php echo $value?></label>
            <?php $selectionCounter++;
            }?>
            </div>
	    </p>
    </div>
<?php 
    }

}

?>