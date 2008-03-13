<?php
/*
Plugin Name: Googmonify
Plugin URI: http://www.gara.com/projects/googmonify/
Description: Add the ability to drop product links into your blog posts
Author: Gary Keorkunian
Author URI: http://www.gara.com/
Version: 0.5.1

Copyright 2008 GARA Systems, Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.


HISTORY

Version		Date		Author		Description
--------	--------	-----------	------------------------------------------
0.1			20080223	Gary		Initial version
0.2			20080229	Gary		Added Admin Options page
0.3			20080301	Gary		Enhanced Options Page
									Cleaned up comments
0.4			20080302	Gary		Eliminated redundant function calls
									Added confirmation message on Option Save
0.5			20080303	Gary		Added options to include Google Analytics 
									scripts in the Blog footer.
0.5.1		20080313	Gary		Replaced short tags with long tags for max
									compatibility with PHP5


SEE THE README.TXT FOR INSTRUCTIONS

*/

// Googmonify Options 
$googmonify_options["PID"]="";
$googmonify_options["Limit"]=3;


// Initialize Googmonify Count
if(!isset($googmonify_count))
	$googmonify_count=0;


// Retrieve Googmonify Options from the WordPress DB
function googmonifyGetOptions()
{
	global $googmonify_options;
	
	$googmonify_options['PID']=get_option('googmonify_PID');
	$googmonify_options['Limit']=get_option('googmonify_Limit');
	if(empty($googmonify_options['Limit']) | !isset($googmonify_options['Limit']) | $googmonify_options['Limit']=='') $limit=3;
	
}

// Googmonify the Content
function googmonifyContent($content)
{
	global $googmonify_options;
	global $googmonify_count;
	googmonifyGetOptions();

	$sp=0;
	$ep=0;
	
	$new_content=$content;
	
	// find each googmonify tag
	while($sp=strpos($new_content, '[googmonify]'))		
	{
		$ep=strpos($new_content, '[/googmonify]');
		
		if($sp>0 & $ep>0)
		{
			$sub=substr($new_content, $sp, $ep-$sp);	// extract the begin tag and parameters from contents
			$sub=str_replace('[googmonify]', '', $sub);	// remove the begin tag
			
			// extract the parameters 
			list($slot,$align,$width,$height,$alt_pid)=split(":", $sub, 5);	
			
			// set default values for missing parameters
			if($align=='') $align='right';
			if($width=='') $width=120;
			if($height=='') $height=240;
			if($alt_pid!='') $googmonify_options['PID']=$alt_pid;
			
			
			// Create Link
			if($align=='center')
				$link='<div class="googmonify" style="margin:3px;text-align:center">';
			else
				$link='<div class="googmonify" style="margin:3px;float:'.$align.';">';
				
			
			$link.='<script type="text/javascript"><!--' . "\n"
					. 'google_ad_client = "pub-'.$googmonify_options['PID'].'";'
					. 'google_ad_slot = "'.$slot.'";'
					. 'google_ad_width = '.$width.';'
					. 'google_ad_height = '.$height.';'
					. "\n//-->"
					. '</script>'
					. '<script type="text/javascript"'
					. 'src="http://pagead2.googlesyndication.com/pagead/show_ads.js">'
					. '</script>'
					. '</div>';
					
			if($googmonify_count > $googmonify_options['Limit'])
				$link="";
			
			$new_content=str_replace('[googmonify]'.$sub.'[/googmonify]', $link, $new_content);
			$googmonify_count++;
			
		}
	}
	
	return $new_content;
}

// Add Amazon Context Ads script
function googmonifyAnalytics()
{
	if(get_option('googmonify_Analytics')=="1")
	{
?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("<?php echo  get_option('googmonify_AID'); ?>");
pageTracker._initData();
pageTracker._trackPageview();
</script>
<?php
	}
}


// Admin Options Page
function googmonifyOptionsPage()
{
	$pid="";
	$limit=0;

	if(isset($_POST['GoogmonifyUpdate']))
	{
		$pid=$_POST["PID"];

		if(!is_numeric($_POST["Limit"]))
			$limit=3;
		else
			$limit=$_POST["Limit"];
		
		$analytics=$_POST['Analytics'];
		$aid=$_POST['AID'];
			
		update_option('googmonify_PID', $pid);
		update_option('googmonify_Limit', $limit);
		update_option('googmonify_Analytics', $analytics);
		update_option('googmonify_AID', $aid);
		
?>
<div class="updated fade" id="message" style="background-color: rgb(207, 235, 247);"><p><strong>Options saved.</strong></p></div>
<?php
	}
	else
	{
		$pid=get_option('googmonify_PID');
		$limit=get_option('googmonify_Limit');
		if(!is_numeric($limit)) $limit=3;
		$analytics=get_option('googmonify_Analytics');
		$aid=get_option('googmonify_AID');
	}

?>
	<div class="wrap">
		<h2>Googmonify</h2>
		<form method="POST">
			<table class="optiontable">
				<tr valign="top">
					<th>Google AdSense Publisher ID:</th>
					<td><input id="PID" name="PID" type="text" value="<?php echo $pid; ?>"></td>
				</tr>
				<tr valign="top">
					<th>Ad Limit:</th>
					<td><input id= "Limit" name="Limit" type="text" value="<?php echo $limit; ?>" size="5"><br>
					Google AdSense allows only 3 ad units per page.  If you include ad units in other parts of your blog theme, you should reduce this limit accordingly.
					</td>
				</tr>
				<tr>
					<th colspan="2"><hr></th>
				</tr>
				<tr valign="top">
					<th>Google Analytics</th>
					<td>
					<input type="checkbox" id="Analytics" name="Analytics" value="1" <?php echo $analytics ? 'checked' : ''; ?>> Include Google Analytics scripts</td>
				</tr>
				<tr valign="top">
					<th>Tracking ID:</th>
					<td><input id="AID" name="AID" type="text" value="<?php echo $aid; ?>"> <br>
					The Tracking ID includes your account ID plus a site number (i.e. AB-0000000-1).<br>
					You can find this value from the Tracking Code page on Google Analytics.</td>
				</tr>
			</table>
			<p class="submit"><input name="GoogmonifyUpdate" type="submit" value="Update Options &raquo;"></p>
		</form>
		<h3>Markup Tags</h3>
		<p>To include a Google AdSense ad in your post simply add 
		the following googmonify tag set in the location where you 
		want the ad:</p>
		<code>[googmonify]slot:align:width:height[/googmonify]</code>
		<p>where ... </p>
		<ul>
			<li><b>slot</b> is the id of the Google AdSense ad.</li>
			<li><b>align</b> is the alignment of the ad block (ex. right, left or center).&nbsp; Using right and left values will cause 
			the ads to float right or left respectively.&nbsp; If you use center, the tag set should be placed on its own line.</li>
			<li><b>width</b> is the width of the ad</li>
			<li><b>height</b> is the height of the ad</li>
		</ul>
		<p>Note - the width and height must match the dimensions of the AdSense unit you are inserting.</p>
		<h4>Example</h4>
		<p>A 300x250 ad that floats to the right: </p>
		<code>[googmonify]1234567890:right:300:250[/googmonify]</code>
	</div>
	<div class="wrap">
		<h2>More Information</h2>
		<p>Check for the latest information on Googmonify here:  <a href="http://www.gara.com/projects/googmonify/">http://www.gara.com/projects/googmonify/</a></p>
		<p>Subscribe to Googmonify Updates via RSS or Email here:  <a href="http://feeds.feedburner.com/Googmonify">http://feeds.feedburner.com/Googmonify</a></p>
		<p>If you like Googmonify, then you might also like <a href="http://www.gara.com/projects/amazonify/">Amazonify</a> and <a href="http://www.gara.com/projects/bookmarkify/">Bookmarkify</a>, also by <a href="http://www.gara.com/">GARA Systems</a>.</p>
	</div>
<?php
}

// Add Options Page
function googmonifyAdminSetup()
{
	add_options_page('Googmonify', 'Googmonify', 8, basename(__FILE__), 'googmonifyOptionsPage');	
}

// Add Googmonify Action
if(function_exists('add_action'))
{
	add_action('the_content', 'googmonifyContent');
	add_action('wp_footer', 'googmonifyAnalytics');
	add_action('admin_menu', 'googmonifyAdminSetup');
}
	
?>