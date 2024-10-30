<?php
/*
Plugin Name: Hoopler
Plugin URI: http://hoopler.com/web/wordpress
Description: Allows you to integrate a hoopler hoop to your blog
Version: 1.0
License: GPL
Author: Hoopler.com
Author URI: http://hoopler.com
*/

function get_hoopler() 
{
  	for($i = 0 ; $i < func_num_args(); $i++) 
	{
	    	$args[] = func_get_arg($i);
    	}

  	if (!isset($args[0])) $feedName = get_option('hoopler_display_name'); else $feedName = $args[0];
  	if (!isset($args[1])) $feedLength = get_option('hoopler_display_length'); else $feedLength = $args[1];
        
	if (!function_exists('MagpieRSS')) 
	{
		include_once (ABSPATH . WPINC . '/rss.php');
		error_reporting(E_ERROR);
	}

	$rss_url = 'http://www.hoopler.com/web/xml.php?n=' . urlencode($feedName) . '&m=' . urlencode($feedLength);
	
	$rss = @ fetch_rss($rss_url);

	if ($rss) 
	{
		$items = array_slice($rss->items, 0, $feedLength);
		print "<ul>";
		foreach ($items as $item ) 
		{
			$title = htmlspecialchars(stripslashes($item['title']));
			$description = htmlspecialchars(stripslashes($item['description']));
			$url = $item['link'];
                	print "<li><a href=\"$url\" title=\"$description\">$title</a></li>";
		} 
		print "</ul>";
  	}
}

function widget_hoopler_init() 
{
	if (!function_exists('register_sidebar_widget')) return;

	function widget_hoopler($args) 
	{		
		extract($args);

		$options = get_option('widget_hoopler');
		$title = $options['title'];
		$name = $options['name'];
		$length = $options['length'];

		echo $before_widget . $before_title . $title . $after_title;
		get_hoopler($name, $length);
		echo $after_widget;
	}

	function widget_hoopler_control() 
	{
		$options = get_option('widget_hoopler');
		if ( !is_array($options) )
		{
			$options = array('title'=>'');
		}
		if ( $_POST['hoopler-submit'] ) 
		{
			$options['title'] = strip_tags(stripslashes($_POST['hoopler-title']));
			$options['name'] = strip_tags(stripslashes($_POST['hoopler-name']));
			$options['length'] = strip_tags(stripslashes($_POST['hoopler-length']));
			update_option('widget_hoopler', $options);
		}

		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$name = htmlspecialchars($options['name'], ENT_QUOTES);	
		$length = htmlspecialchars($options['length'], ENT_QUOTES);
	
		echo '<p style="text-align:right;"><label for="hoopler-title">Title: <input style="width: 200px;" id="gsearch-title" name="hoopler-title" type="text" value="'.$title.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="hoopler-name">Hoop: <input style="width: 200px;" id="gsearch-title" name="hoopler-name" type="text" value="'.$name.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="hoopler-length">Articles: ';
		echo '<select name="hoopler-length">';
		echo '<option '; if($length == '5') { echo 'selected '; } echo 'value="5">5</option>';
		echo '<option '; if($length == '10') { echo 'selected '; } echo 'value="10">10</option>';
		echo '<option '; if($length == '20') { echo 'selected '; } echo 'value="20">20</option>';
		echo '<option '; if($length == '30') { echo 'selected '; } echo 'value="30">30</option>';
		echo '<option '; if($length == '50') { echo 'selected '; } echo 'value="50">50</option>';
		echo '<option '; if($length == '100') { echo 'selected '; } echo 'value="100">100</option>';
		echo '</select></p>';
		echo '<input type="hidden" id="hoopler-submit" name="hoopler-submit" value="1" />';
	}		

	register_sidebar_widget('Hoopler', 'widget_hoopler');
	register_widget_control('Hoopler', 'widget_hoopler_control', 300, 100);
}

function hoopler_subpanel() 
{
	if (isset($_POST['save_hoopler_options'])) 
	{
		$option_display_name = $_POST['display_name'];
		$option_display_length = $_POST['display_length'];
		update_option('hoopler_display_name', $option_display_name);
		update_option('hoopler_display_length', $option_display_length);
		?> <div class="updated"><p>Options changes saved.</p></div> <?php
	}
	?>

	<div class="wrap">
		<h2>Hoopler Options</h2>

		<br/>Note 1: The plugin will attempt to cache the hoop. Please don't be upset if it takes a few minutes for your changes to be reflected.
		<br/>
		<br/>Note 2: If you use hoopler as a widget, the options below will be overridden by the widget control selections.<br/><br/>

		<form method="post">
		
		<fieldset class="options">
		<table>
		 <tr>
		  <td><p><strong><label for="hoopler_name">Hoop name</label>:</strong></p></td>
		  <td><input name="display_name" type="text" id="hoopler_name" value="<?php echo get_option('hoopler_display_name'); ?>" size="20" /></p></td>
                 </tr>
                <tr>
          	<td><p><strong>Number of Articles:</strong></p></td>
          	<td>
        	<select name="display_length" id="display_length">
        	  <option <?php if(get_option('hoopler_display_length') == '5') { echo 'selected'; } ?> value="5">5</option>
		  <option <?php if(get_option('hoopler_display_length') == '10') { echo 'selected'; } ?> value="10">10</option>
		  <option <?php if(get_option('hoopler_display_length') == '20') { echo 'selected'; } ?> value="20">20</option>
		  <option <?php if(get_option('hoopler_display_length') == '30') { echo 'selected'; } ?> value="30">30</option>
		  <option <?php if(get_option('hoopler_display_length') == '50') { echo 'selected'; } ?> value="50">50</option>
		  <option <?php if(get_option('hoopler_display_length') == '100') { echo 'selected'; } ?> value="100">100</option>
		</select>
           </td> 
         </tr>        
	<tr><td colspan=2><br/><a href="http://www.hoopler.com">Hosted by hoopler.com</a></td></tr>
         </table>
        </fieldset>
		<p><div class="submit"><input type="submit" name="save_hoopler_options" value="<?php _e('Save Options', 'save_hoopler_options') ?>"  style="font-weight:bold;" /></div></p>
        </form>       
    </div>

<?php } 

function fHoopler_admin_menu() 
{
	if (function_exists('add_options_page')) 
	{
		add_options_page('Hoopler Options Page', 'Hoopler', 8, basename(__FILE__), 'hoopler_subpanel');
        }
}

add_action('admin_menu', 'fR_admin_menu'); 
add_action('plugins_loaded', 'widget_hoopler_init');
?>
