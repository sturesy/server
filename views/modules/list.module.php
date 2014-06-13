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
        return "";
    }

    /**
     * Echos the HTML code
     */
    public function html()
    {
?>
    <div class="text-center">
        <p><?php echo $this->values["text"]?></p>
        <p>
            <div class="btn-group" data-toggle="buttons">
            <?php
            $selectionCounter = 1;
            foreach($this->values["elements"] as $value){?>
                <label class="btn btn-primary">
                    <input type="radio" name="<?php echo $this->id?>" value="<?php echo $selectionCounter;?>" id="<?php echo $this->id?>"><?php echo $value?>
                </label>
            <?php $selectionCounter++;
            }?>
            </div>
	    </p>
    </div>
<?php 
    }

}

?>