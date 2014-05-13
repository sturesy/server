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
 <button type="button" class="btn btn-large" onclick="reload();"><i class="icon-refresh"></i> Reload</button>
    <?php
}

function display_question($lecture_name, $question_text)
{
?>
<p class="text-center">
	<small>Lecture-ID: <?php echo $lecture_name;?></small>
</p>
<h4 class="text-center">
	<?php echo $question_text; ?>
</h4>
<?php 
}



function display_text_choice($lecture_name)
{
?>
<br>
<center>
	<form method="post" action="index.php?lecture=<?php echo $lecture_name;?>">
		<label>Answer:</label> <input type="text" placeholder="" name="text">
		<input type="hidden" name="cmd" value="text" /> <input type="hidden"
			name="type" value="t" /><br> <input type="submit"
			class="btn btn-large" value="Submit" />
	</form>
	<br>

	<?php display_reload_button();?>
</center>
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
        <td class="text-center" style="text-align:center;">
            <input type='checkbox' id='check<?php echo $i;?>' class='regular-checkbox big-checkbox' name='<?php echo $c;?>' value='true'><label for='check<?php echo $i;?>'><?php echo $c;?></label>
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
			    <td colspan="2" style="text-align:center;">
			        <button type="submit" class="btn btn-large"><i class="icon-ok"></i>Submit</button>
			    </td>
			</tr>
	</table>
</div>
</form>
	<table class="table table-condensed text-center">
	    <tr>
		    <td colspan="2" style="text-align:center;">
		        <?php display_reload_button();?>
		    </td>
        </tr>
	</table>
    <?php 
}



function display_single_choice($lecture_name, $answers, $preparedValuesForButton)
{
    ?>
<table class="table table-condensed" align="center">
    <tr>
    <?php 
    for ($i = 0; $i < count($answers); $i++)
    {
    ?>
    
        <td>
		    <form method="post" action="index.php?lecture=<?php echo $lecture_name;?>">
	    	    <input type="hidden" name="cmd" value="<?php echo $preparedValuesForButton[$i][0];?>"/>
		        <input type="hidden" name="type" value="s"/>
		        <input type="submit" class="btn btn-large btn-block" value="<?php echo $preparedValuesForButton[$i][1];?>" />
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
	    <td colspan="2" style="text-align:center;">
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