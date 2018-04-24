<?php
/*
 * StuReSy - Student Response System
 * Copyright (C) 2012-2014  StuReSy-Team
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


global $INDEXPHPWASACCESSED;
if($INDEXPHPWASACCESSED !== true)
{
    die('<meta http-equiv="refresh" content="0; url=../index.php" />');
}

function display_reload_button()
{
    ?>
<button type="button" class="btn btn-default btn-lg" onclick="reload();"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
    <?php
}

function display_question($lecture_name, $question_text)
{
?>

<div class="container-fluid">
    <div class="row text-center">
    <p><small>Lecture-ID: <?php echo $lecture_name;?></small></p>
    <h4 class="text-center"><?php echo $question_text; ?></h4>
    </div>
<?php 
}



function display_text_choice($lecture_name)
{
?>

    <br>
    <div class="row"> <!-- row1 -->
    	<div class="col-xs-offset-2 col-xs-8 col-sd-offset-4 col-sd-4 col-md-offset-4 col-md-4">
    	    <form class="form-group text-center" method="post" action="index.php?lecture=<?php echo $lecture_name;?>">
        		<label class="">Answer:</label> 
        		<input class="form-control" placeholder="" name="text" type="text">
        		<input class="form-control" name="cmd" value="text" type="hidden">
        		<input class="form-control" name="type" value="t" type="hidden">
        		<br>
        		<input class="btn btn-default btn-lg" value="Submit" type="submit">
    	    </form>
    	</div>
    </div><!-- row1 -->
    <div class="row"> <!-- row2 -->
        <div class="text-center">
    	    <?php display_reload_button();?>
    	</div>
    </div><!-- row2 -->

</div>	<!-- container-fluid -->
<?php
}


function display_multiple_choice($lecture_name, $answers)
{
    ?>
<form method="post" action="index.php?lecture=<?php echo $lecture_name;?>">
    <input type="hidden" name="cmd" value="multiple"/>
    <input type="hidden" name="type" value="m"/>
	<div class="text-center">

     <?php for ($i = 0; $i < count($answers); $i++) 
           {
	            if($i % 2 == 0)
	           	{
	           		?>
	           		<div class="row"><!-- row -->
	           		<?php 
	           	}
	           	
	            $c = chr(65+$i); 
	        	?>
	        	<div class="col-xs-12 col-md-6"> <!-- col-xs-12 -->
	            	<input type="checkbox" id="checkbox-2-<?php echo $i;?>" name="<?php echo $c;?>"><label for="checkbox-2-<?php echo $i;?>"><?php echo $answers[$i]?></label>
	        	</div> <!-- /col-xs-12 -->
	        	<?php
	              if(($i+1) % 2 == 0)
	              {
	                ?>
	           		</div><!-- /row -->
	                <?php 
	              }
           }?>
        </div>
			<div class="row text-center"><!-- submitbtn -->
			        <button type="submit" class="btn btn-default btn-large btn-block"><i class="icon-ok"></i> Submit </button>
			</div><!-- /submitbtn -->
		</div>
</form>
	<div class="row" style="margin-top: 10px;"> <!-- row2 -->
        <div class="text-center">
    	    <?php display_reload_button();?>
    	</div>
    </div><!-- row2 -->
    <?php 
}



function display_single_choice($lecture_name, $answers, $preparedValuesForButton)
{
	
	//var_dump(json_encode($answers));var_dump(json_encode($preparedValuesForButton));
	
    ?>
    <?php 
    for ($i = 0; $i < count($answers); $i++)
    {
    	if($i%2 == 0)
    	{
    		?>
    		<div class="row">
    		<?php 
    	}
    	
    ?>
        <div class="col-md-6 col-xs-12" style="margin-bottom: 8px;">
		    <form method="post" action="index.php?lecture=<?php echo $lecture_name;?>">
	    	    <input type="hidden" name="cmd" value="<?php echo $preparedValuesForButton[$i][0];?>"/>
		        <input type="hidden" name="type" value="s"/>
		        <input type="submit" class="btn btn-default btn-large btn-block" value="<?php echo $answers[$i];?>" style="word-break: break-word;white-space:normal;"/>
		    </form>
		</div>
    <?php 
        if(($i+1) % 2 == 0)
        {
            ?></div><?php 
        }
    }
    
    ?>
    <div class="row">
    	<div class="text-center">
	        <?php display_reload_button();?>
    	</div>
    </div>
        <?php 
}



function display_no_lecture_id($message)
{
?>
    	<div class="container" align="center">
    		<form class="form-signin">
    			<h2>Error</h2>
    			<p class="red size20"><?php echo $message; ?></p>
    			<br>
    			<button class="btn btn-warning btn-large" onClick="history.go(-1);return true;">
    				<i class="glyphicon glyphicon-arrow-left"></i> Back
    			</button>
    		</form>
    	</div>
<?php 
}


?>