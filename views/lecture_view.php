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
    <table class="table table-condensed text-center">
        <tr>
     <?php for ($i = 0; $i < count($answers); $i++) 
           {
            $c = chr(65+$i); 
        ?>
        <td class="text-center" style="border-top:none;">
            <input type="checkbox" id="checkbox-2-<?php echo $i;?>" name="<?php echo $c;?>"><label for="checkbox-2-<?php echo $i;?>"><?php echo $c;?></label>
        </td>
        <?php
              if(($i+1) % 2 == 0)
                {
                ?>
            </tr>
            <tr>
                <?php 
                }
           }?>
        </tr>
			<tr>
			    <td colspan="2" style="border-top:none;">
			        <button type="submit" class="btn btn-default btn-large"><i class="icon-ok"></i>Submit</button>
			    </td>
			</tr>
	</table>
</div>
</form>
	<table class="table table-condensed text-center">
	    <tr>
		    <td colspan="2" style="border-top:none;">
		        <?php display_reload_button();?>
		    </td>
        </tr>
	</table>
    <?php 
}



function display_single_choice($lecture_name, $answers, $preparedValuesForButton)
{
    ?>
<table class="table table-condensed text-center table-borderless">
    <tr>
    <?php 
    for ($i = 0; $i < count($answers); $i++)
    {
    ?>
    
        <td style="border-top: none;">
		    <form method="post" action="index.php?lecture=<?php echo $lecture_name;?>">
	    	    <input type="hidden" name="cmd" value="<?php echo $preparedValuesForButton[$i][0];?>"/>
		        <input type="hidden" name="type" value="s"/>
		        <input type="submit" class="btn btn-default btn-large btn-block" value="<?php echo $preparedValuesForButton[$i][1];?>" />
		    </form>
		</td>
    <?php 
        if(($i+1) % 2 == 0)
        {
            ?></tr>
            <tr><?php 
        }
    }
    
    ?>
    </tr>
	<tr>
	    <td colspan="2" style="border-top: none;">
	        <?php display_reload_button();?>
	    </td>
	</tr>
        </table>
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
    				<i class="icon-arrow-left icon-white"></i> Back
    			</button>
    		</form>
    	</div>
<?php 
}


?>