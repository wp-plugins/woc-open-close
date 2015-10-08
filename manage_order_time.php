<table align="center" width="70%" style="border: 2px solid #861212; margin-top:15px;" cellspacing="3" cellpadding="3">
	<tr>
		<td width="16%" nowrap="nowrap"><a href="admin.php?page=woc_manage_ordertime"> View Setting </a></td>
		<td width="16%" align="center"><a href="admin.php?page=woc_manage_ordertime&action=edit"> Edit Setting </a></td>
		<td width="16%" align="center"><a href="admin.php?page=woc_manage_ordertime&action=clear"> Clear Setting </a></td>
		<td width="22%" align="center"> Shortcode <input style="text-align:center;" type='text' disabled value='[woc_open_close]'  size=25/> </td>
	</tr>
</table>
<?php 
global $wpdb; 
extract($_GET);
if (!isset($action)) $action = '';
if ( $action == "edit" )
{
?>
	<h2 align="center" style="border: 2px solid #861212;padding: 5px;width: 30%;margin-left: auto;margin-right: auto;"> Edit your Settings </h2> 
	<form method="post" action="admin.php?page=woc_manage_ordertime&action=save"  onSubmit="return validate_edit_form(this);">
		<?php
			$result = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".DB_TABLE_NAME ." WHERE %d",1));
			
			echo '<table align="center" width="60%" style="border: 2px solid #861212;" cellspacing="3" cellpadding="3">';
			
			foreach( $result as $rt ) 
			{		
				echo 	'<tr>
							<td width="20%" nowrap="nowrap">Settings for '. ucfirst($rt->day) . "day" . ' </td>
							<td width="2%" align="center">:</td>
							<td width="18%"><input type="text" maxlength="5"  name="st_' .$rt->id .'" id="st_' .$rt->id .'" value="'.$rt->start_time .'" size="20" placeholder="Opening Time"></td>
							<td width="18%"><input type="text" maxlength="5"  name="et_'.$rt->id .'" id="et_' .$rt->id .'" value="'.$rt->end_time .'" size="20" placeholder="Closing Time"></td>
						</tr>';
				$message = $rt->message;
			}
			echo '</table>';	
			
			echo 	'<h2 align="center" style="border: 2px solid #861212;padding: 5px;width: 60%;margin-left: auto;margin-right: auto;"> Alert Message Box 
					</br><hr>
						<textarea rows="3" cols="50" type="text" name="message" id="message" placeholder="Write message when Restaurant is off"> '.$message.' </textarea>
					</h2>';	
			?>
		<div style="padding:15px;" align="center"><input type="submit" style="border: 2px solid #861212;padding: 5px;width: 30%;margin-left: auto;margin-right: auto;" value="Save the Data" /> </div>
    </form>
<?php
}
else if ( $action == "save" )
{
	$st_mon   = $_POST['st_1'];	if ( ! $st_mon ) $st_mon = '';
	$st_tue   = $_POST['st_2']; if ( ! $st_tue ) $st_tue = '';
	$st_wed   = $_POST['st_3'];	if ( ! $st_wed ) $st_wed = '';
	$st_thu   = $_POST['st_4'];	if ( ! $st_thu ) $st_thu = '';
	$st_fri   = $_POST['st_5'];	if ( ! $st_fri ) $st_fri = '';
	$st_sat   = $_POST['st_6'];	if ( ! $st_sat ) $st_sat = '';
	$st_sun   = $_POST['st_7']; if ( ! $st_sun ) $st_sun = '';
	$et_mon   = $_POST['et_1'];	if ( ! $et_mon ) $et_mon = '';
	$et_tue   = $_POST['et_2'];	if ( ! $et_tue ) $et_tue = '';
	$et_wed   = $_POST['et_3'];	if ( ! $et_wed ) $et_wed = '';
	$et_thu   = $_POST['et_4'];	if ( ! $et_thu ) $et_thu = '';
	$et_fri   = $_POST['et_5'];	if ( ! $et_fri ) $et_fri = '';
	$et_sat   = $_POST['et_6'];	if ( ! $et_sat ) $et_sat = '';
	$et_sun   = $_POST['et_7'];	if ( ! $et_sun ) $et_sun = '';
	
	$message   = $_POST['message'];	if ( ! $message ) $message = '';
	
	if ( $message != "" && $st_mon != "" && $st_tue != "" && $st_wed != "" && $st_thu != "" && $st_fri  != "" && $st_sat  != "" && $st_sun  != "" )
	{
		$wpdb->query($wpdb->prepare("UPDATE ".DB_TABLE_NAME ." SET start_time='%s',end_time='%s' WHERE id = %d",$st_mon,$et_mon,1));
		$wpdb->query($wpdb->prepare("UPDATE ".DB_TABLE_NAME ." SET start_time='%s',end_time='%s' WHERE id = %d",$st_tue,$et_tue,2));
		$wpdb->query($wpdb->prepare("UPDATE ".DB_TABLE_NAME ." SET start_time='%s',end_time='%s' WHERE id = %d",$st_wed,$et_wed,3));
		$wpdb->query($wpdb->prepare("UPDATE ".DB_TABLE_NAME ." SET start_time='%s',end_time='%s' WHERE id = %d",$st_thu,$et_thu,4));
		$wpdb->query($wpdb->prepare("UPDATE ".DB_TABLE_NAME ." SET start_time='%s',end_time='%s' WHERE id = %d",$st_fri,$et_fri,5));
		$wpdb->query($wpdb->prepare("UPDATE ".DB_TABLE_NAME ." SET start_time='%s',end_time='%s' WHERE id = %d",$st_sat,$et_sat,6));
		$wpdb->query($wpdb->prepare("UPDATE ".DB_TABLE_NAME ." SET start_time='%s',end_time='%s' WHERE id = %d",$st_sun,$et_sun,7));
		
		$wpdb->query($wpdb->prepare("UPDATE ".DB_TABLE_NAME ." SET message='%s' WHERE id > %d",$message,0));
		
		if ($wpdb->last_error) echo $wpdb->last_error;
		else woc_redirect("admin.php?page=woc_manage_ordertime");
	}
	else woc_redirect("admin.php?page=woc_manage_ordertime");
}
else
{		
	$result = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".DB_TABLE_NAME ." WHERE %d",1));
	
	echo '<h2 align="center" style="border: 2px solid #861212;padding: 5px;width: 60%;margin-left: auto;margin-right: auto;"> Opening and Closing Time for 7 Days </h2>';
	echo '<table align="center" width="45%" style="border: 2px solid #861212;" cellspacing="3" cellpadding="3">';
	foreach( $result as $rt ) 
	{
		echo 	'<tr>
					<td width="15%" nowrap="nowrap">Settings for '. ucfirst($rt->day) . "day" . ' </td>
					<td width="2%" align="center">:</td>
					<td width="14%">'. $rt->start_time .'</td>
					<td width="14%">'. $rt->end_time .'</td>
				</tr>';
		$message = $rt->message;
    }
	echo '</table>';
	
	
	echo	'<h2 align="center" style="border: 2px solid #861212;padding: 5px;width: 60%;margin-left: auto;margin-right: auto;"> Alert Message Box 
			</br><hr>
				<font color="red">'.$message.'</font>
			</h2>';
			
	if ( WOC_USER_TYPE == 'free' ):
		echo '<h2 align="center" style="background:#3993D0;border: 2px solid #861212;padding: 5px;width: 80%;margin-left: auto;margin-right: auto;"> 
				<b>Warning !!! </b>
				</br><hr>
				<font color="#400000"><b>Currently You are using free version of this Plugin. To get all features have a Paid Version.</b></font>
				</br><hr>
				<a style="text-decoration:none;" href="http://pluginbazar.ml" target="_blank"><font color="#400000"><b>Click Here to BUY</b></font></a>
			</h2>';
	elseif ( WOC_USER_TYPE == 'pro' ):	
		echo '<h2 align="center" style="background:#3993D0;border: 2px solid #861212;padding: 5px;width: 80%;margin-left: auto;margin-right: auto;"> 
				<b>Congratulations !!! </b>
				</br><hr>
				<font color="#fff"><b>Currently You are using PREMIUM version of this Plugin.</b></font>
				</br><hr>
				<a style="text-decoration:none;" href="http://pluginbazar.ml" target="_blank"><font color="#fff"><b>PLUGINBAZAR</b></font></a>
			</h2>';
	endif;
} 
?>
<script language="javascript">
function validate_edit_form(frm)
{
	if(document.getElementById('st_1').value=="")
	{
		//alert('Please enter start order time!');
		//document.getElementById('st_1').focus();
		return true;
	}
	if(document.getElementById('et_1').value=="")
	{
		alert('Please enter end order time!');
		document.getElementById('et_1').focus();
		return false;
	}
	
	if(document.getElementById('st_2').value=="")
	{
		alert('Please enter start order time!');
		document.getElementById('st_2').focus();
		return false;
	}
	if(document.getElementById('et_2').value=="")
	{
		alert('Please enter end order time!');
		document.getElementById('et_2').focus();
		return false;
	}
	
	if(document.getElementById('st_3').value=="")
	{
		alert('Please enter start order time!');
		document.getElementById('st_3').focus();
		return false;
	}
	if(document.getElementById('et_3').value=="")
	{
		alert('Please enter end order time!');
		document.getElementById('et_3').focus();
		return false;
	}
	
	if(document.getElementById('st_4').value=="")
	{
		alert('Please enter start order time!');
		document.getElementById('st_4').focus();
		return false;
	}
	if(document.getElementById('et_4').value=="")
	{
		alert('Please enter end order time!');
		document.getElementById('et_4').focus();
		return false;
	}
	
	if(document.getElementById('st_5').value=="")
	{
		alert('Please enter start order time!');
		document.getElementById('st_5').focus();
		return false;
	}
	if(document.getElementById('et_5').value=="")
	{
		alert('Please enter end order time!');
		document.getElementById('et_5').focus();
		return false;
	}
	
	if(document.getElementById('st_6').value=="")
	{
		alert('Please enter start order time!');
		document.getElementById('st_6').focus();
		return false;
	}
	if(document.getElementById('et_6').value=="")
	{
		alert('Please enter end order time!');
		document.getElementById('et_6').focus();
		return false;
	}
	
	if(document.getElementById('st_7').value=="")
	{
		alert('Please enter start order time!');
		document.getElementById('st_7').focus();
		return false;
	}
	if(document.getElementById('et_7').value=="")
	{
		alert('Please enter end order time!');
		document.getElementById('et_7').focus();
		return false;
	}
	if(document.getElementById('message').value=="")
	{
		alert('Please Write the message!');
		document.getElementById('message').focus();
		return false;
	}
	
}
</script>

<?php
function woc_redirect($url)
{
    if (!headers_sent())
    {    
        header('Location: '.$url);
        exit;
        }
    else
        {  
        echo '<script type="text/javascript">';
        echo 'window.location.href="'.$url.'";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>'; exit;
    }
}
?>