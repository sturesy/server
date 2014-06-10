<?php


include_once 'views/modules/interface.module.php';
class slider implements IModule
{
    private $values;
    private $id;
    
    function __construct(array $values, $id)
    {    
        $this->values = $values;
        $this->id = $id;
        
        $this->values["middle"] = ($values["max"]-$values["min"])/2;
    }
    
    function javascript()
    {
        return '$("#'.$this->id.'").slider({formater:function(e){return"Current value: "+e}});';
    }
    
    function html()
    {
    ?>
    <div class="text-center">
        <p>
	    <p><?php echo $this->values["text"]?> <input type="text" class="span2" value="1" name="<?php echo $this->id?>" id="<?php echo $this->id?>" data-slider-min="<?php echo $this->values["min"]?>" data-slider-max="<?php echo $this->values["max"]?>" data-slider-step="<?php echo $this->values["step"]?>" data-slider-value="<?php echo $this->values["middle"]?>" ></p>
    </div>
    <?php 
    }
    
}