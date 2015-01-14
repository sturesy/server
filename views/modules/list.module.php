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
        $out = "";
        if(isset($this->values["input"])) {
            foreach($this->values["input"] as $val) {
                $out .= "$('#" . $this->id . "-" . crc32($val) . "').prop('checked', true);";
            }
        }
        return $out;
    }

    /**
     * Echos the HTML code
     */
    public function html()
    {
?>
    <div class="text-center">
        <p><?php echo $this->values["description"]?></p>
            <?php

            $selectionCounter = 1;

            if(isset($this->values["multiple"]) && $this->values["multiple"])
                $type = "checkbox";
            else
                $type = "radio";


            foreach($this->values["elements"] as $value){?>
                <div class="<?php echo $type?>-inline">
                    <label><input style="display: block" type="<?php echo $type?>" id="<?php echo $this->id."-".crc32($value)?>" value="<?php echo $value;?>" name="<?php echo $this->id?>[]"><?php echo $value;?></label>
                </div>
            <?php $selectionCounter++;
            }
            ?>
    </div>
<?php 
    }

}

?>
