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
        var_dump($this->values["input"]);
        if(isset($this->values["input"])) {
            foreach($this->values["input"] as $val) {
                $out .= "$('#" . $this->id . "-" . crc32($val) . "').button('toggle');";
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
        <p>
            <?php
                echo "<div class=\"btn-group\" data-toggle=\"buttons\">";

            $selectionCounter = 1;

            $type = "radio";
            if(isset($this->values["multiple"]) && $this->values["multiple"])
                $type = "checkbox";


            foreach($this->values["elements"] as $value){?>
                <label class="btn btn-primary" id="<?php echo $this->id."-".crc32($value)?>">
                    <input type="<?php echo $type?>" name="<?php echo $this->id?>[]" value="<?php echo $value;?>"><?php echo $value?></label>
            <?php $selectionCounter++;
            }
                echo "</div>";
            ?>
	    </p>
    </div>
<?php 
    }

}

?>