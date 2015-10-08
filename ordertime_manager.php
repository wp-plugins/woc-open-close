<?php
/*
	Plugin Name:	WOC Open Close
	Plugin URI:		http://pluginbazar.ml/blog/woc-open-close/
	Description: 	This is a plug-in for a web shop to maintain it's opening and closing 
						time in different days of a week. Isn't this awesome ?
	Version: 1.1.0
	Author: Jaed Mosharraf
	Author URI: http://pluginbazar.ml/
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

//define section
	global $wpdb;
	$woc_off_message = '';
	
	if ( ! defined( 'DB_TABLE_NAME' ) ) define( 'DB_TABLE_NAME', $wpdb->prefix .'woocommerce_open_close' );
	if ( ! defined( 'WOC_USER_TYPE' ) ) define('WOC_USER_TYPE', 'free');
	
	add_action('admin_menu', 'woc_display_admin_menu');
	add_shortcode( 'woc_open_close', 'woc_woocommerce_open_close' );
	
	function woc_include_admin_menu() 
	{
		global $wpdb;
		global $default;
		global $limit;
		global $p;
		global $searchTerm;
		include("manage_order_time.php"); 
	}
	
	function woc_display_admin_menu() 
	{
		add_menu_page('WOC Open Close', 'WOC Open Close', 'edit_pages', 'woc_manage_ordertime', 'woc_include_admin_menu');
	}
	

//actication and deactication hook registration
function woc_activate()
{
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "	CREATE TABLE IF NOT EXISTS ".DB_TABLE_NAME ." (
				id int(1) NOT NULL AUTO_INCREMENT,
				day varchar(16) NOT NULL,
				start_time varchar(16) DEFAULT '00:00',
				end_time varchar(16) DEFAULT '00:00',
				message varchar(256) DEFAULT 'At this time we do not deliver. Thanks.',
				PRIMARY KEY (id),
				UNIQUE KEY (day)
			) $charset_collate;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	
	woc_add_demo_data();
}
function woc_add_demo_data()
{
	$day = "";
	$count = 0;
	for ( ;; )
	{
		$count++;
		switch ($count)
		{
			case 1:
				woc_add_row('mon');
				break;
			case 2:
				woc_add_row('tues');
				break;
			case 3:
				woc_add_row('wednes');
				break;
			case 4:
				woc_add_row('thurs');
				break;
			case 5:
				woc_add_row('fri');
				break;
			case 6:
				woc_add_row('satur');
				break;
			case 7:
				woc_add_row('sun');
				break;
			default:
				echo woc_add_row('---');
		}
		if ( $count == 7 ) break;
	}
	
}
function woc_add_row($day)
{
	global $wpdb;
	$wpdb->insert( 
		DB_TABLE_NAME, 
		array (
			'day' => $day, 
		)
	);
}
register_activation_hook( __FILE__, 'woc_activate' );

function woc_deactivate()
{
	global $wpdb;
	$wpdb->query("DROP TABLE IF EXISTS ".DB_TABLE_NAME ."");
}
register_deactivation_hook( __FILE__, 'woc_deactivate' );


//adding top bar in website
add_action( 'wp_footer', 'woc_show_footer_off_message' );

function woc_show_footer_off_message()
{
	global $wpdb;
	$zone =  get_option('timezone_string');
	date_default_timezone_set("$zone");	
	
	$ct		= strtotime("now");
	$time	= date('H:i',$ct); 
	$day 	= woc_get_day(date('D'));
	
	$sql_pr = $wpdb->get_row( $wpdb->prepare (
					"SELECT start_time,end_time,message FROM ".$wpdb->prefix ."woocommerce_open_close
						WHERE id = %d",$day));

	$startTime		= $sql_pr->start_time;
	$endTime		= $sql_pr->end_time;
	$popUpMessage	= $sql_pr->message;

	$woc_off_message = "$popUpMessage || Today we deliver from $startTime to $endTime.";
	
	if ( woc_check_is_open() == 0 )
	{
		if (!is_admin()) woc_show_top_bar($woc_off_message);		
	}
	
}

// Global Functions Start
function woc_get_day($str_day)
{
	$day_int = 0;
	switch ($str_day)
	{
		case "Mon":
			$day_int = 1;
			break;
		case "Tue":
			$day_int = 2;
			break;
		case "Wed":
			$day_int = 3;
			break;
		case "Thu":
			$day_int = 4;
			break;
		case "Fri":
			$day_int = 5;
			break;
		case "Sat":
			$day_int = 6;
			break;
		case "Sun":
			$day_int = 7;
			break;
		default:
			$day_int = 0;
	}
	return $day_int;
}
	
function woc_show_top_bar($msg)
{
	?>
	<script type="text/javascript"> 
		function showHide(divId){
			var theDiv = document.getElementById(divId);
			if(theDiv.style.display=="none"){
				theDiv.style.display="";
			}else{
				theDiv.style.display="none";
			}    
		}
	</script>
	<div id="msg" style="direction: ltr;color: #400000;font: 400 20px sans-serif;height: auto;position: fixed;bottom: 0;left: 0;width: 100%;z-index: 99999;background: #D6B75A;">
		<center><p style="color:red;margin-top:5px;">
			<?php echo $msg; ?> 
			<input type="button" onclick="showHide('msg')" value="Hide This Message"> 
		</p></center>
	</div>
	<?php
}

function woc_check_is_open() 
{
	global $wpdb;
	$zone =  get_option('timezone_string');
	date_default_timezone_set("$zone");	
	
	$ct		= strtotime("now");
	$time	= date('H:i',$ct); 
	$day 	= woc_get_day(date('D'));
	
	$sql_pr = $wpdb->get_row( $wpdb->prepare (
					"SELECT start_time,end_time,message FROM ".$wpdb->prefix ."woocommerce_open_close
						WHERE id = %d",$day));

	$startTime		= $sql_pr->start_time;
	$endTime		= $sql_pr->end_time;
	$popUpMessage	= $sql_pr->message;

	
	if($time>=$startTime and $time<=$endTime) return 1; //shop_open
	else return 0; //shop_open
}

function woc_woocommerce_open_close( $atts, $content = null ) 
{
    ob_start();
	
	global $wpdb;
	$result = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix ."woocommerce_open_close  where %d", 1));
	
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
	
	foreach( $result as $rt )
	{
		echo 	'<tr>
					<td>' . ucfirst($rt->day) . "day" .  '</td>
					<td>' . $rt->start_time . ' - ' . $rt->end_time . '</td>
				</tr>';
    }
	echo '</table>';

	$output = ob_get_contents();
    ob_end_clean(); 
    return $output;
}


?>