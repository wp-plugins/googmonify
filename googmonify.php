<?php
/*
Plugin Name: Googmonify
Plugin URI: http://www.gara.com/projects/googmonify/
Description: Add the ability to drop product links into your blog posts
Author: Gary Keorkunian
Author URI: http://www.gara.com/
Version: 0.3

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

SEE THE README.TXT FOR INSTRUCTIONS

*/

// Googmonify Options 
$googmonify_options["PID"]="";
$googmonify_options["Limit"]=3;


// Initialize Googmonify Count
if(!isset($googmonify_count))
	$googmonify_count=0;


// Check Content for Googmonify tags
function isGoogmonified($content)
{

	global $post, $page, $pages, $single;

	if ($page > count($pages))
		$page = count($pages);
        $post_content = $pages[$page-1];

	if (strpos($post_content, '[googmonify]') !== false)
		return true;

	return false;

}

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

// Googmonify the Post
function googmonifyPost($content)
{
	if (!isGoogmonified($content))
		return $content;

	return googmonifyContent($content);
}

// Add Content Action
if(function_exists('add_action'))
{
	add_action('the_content', 'googmonifyPost');
}

// Admin Options Page
function googmonifyOptionsPage()
{
	$pid="";
	$limit=0;

?>
	<div class="wrap">
		<h2>Googmonify</h2>
<?
	
	if(isset($_POST['GoogmonifyUpdate']))
	{
		$pid=$_POST["PID"];

		if(!is_numeric($_POST["Limit"]))
			$limit=3;
		else
			$limit=$_POST["Limit"];
			
		update_option('googmonify_PID', $pid);
		update_option('googmonify_Limit', $limit);
	}
	else
	{
		$pid=get_option('googmonify_PID');
		$limit=get_option('googmonify_Limit');
		if(!is_numeric($limit)) $limit=3;
	}

?>
		<form method="POST">
			<table>
				<tr>
					<td>Google AdSense Publisher ID:</td>
					<td><input id="PID" name="PID" type="text" value="<? echo $pid; ?>"></td>
				</tr>
				<tr>
					<td>Ad Limit:</td>
					<td><input id= "Limit" name="Limit" type="text" value="<? echo $limit; ?>"><br>
					<small>Google AdSense allows only 3 ad units per page.  If you include ad units in other parts of your blog theme, reduce this limit accordingly.</small>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input name="GoogmonifyUpdate" type="submit" value="Update"></td>
				</tr>
			</table>
		</form>
		<h3>Googmonify Tags</h3>
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
<?
}

// Add Options Page
function googmonifyAdminSetup()
{
	add_options_page('Googmonify', 'Googmonify', 8, basename(__FILE__), 'googmonifyOptionsPage');	
}

add_action('admin_menu', 'googmonifyAdminSetup');
	
?>