<?php
/*
Plugin Name: Googmonify
Plugin URI: http://www.gara.com/projects/googmonify/
Description: Add the ability to drop product links into your blog posts
Author: Gary Keorkunian
Author URI: http://www.gara.com/
Version: 0.1

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

SEE THE README.TXT FOR INSTRUCTIONS

*/

/* SETTINGS - The following settings can be modified for your specific blog */
$googmonifyPID="";		// Your Google Publisher ID
$googmonifyLimit=3;
/* END SETTINGS */

if(!isset($googmonifyCount))
	$googmonifyCount=0;


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

function googmonifyContent($content)
{
	global $googmonifyPID;

	$sp=0;
	$ep=0;
	
	$new_content=$content;
	
	while($sp=strpos($new_content, '[googmonify]'))		// find each googmonify tag
	{
		$ep=strpos($new_content, '[/googmonify]');
		
		if($sp>0 & $ep>0)
		{
			$sub=substr($new_content, $sp, $ep-$sp);	// extract the begin tag and parameters from contents
			$sub=str_replace('[googmonify]', '', $sub);	// remove the begin tag
			
			// extract the parameters 
			list($slot,$align,$width,$height,$pid)=split(":", $sub, 5);	
			
			// set default values for missing parameters
			if($align=='') $align='right';
			if($width=='') $width=120;
			if($height=='') $height=240;
			if($pid=='') $pid=$googmonifyPID;
			
			
			// Create Link
			if($align=='center')
				$link='<div class="googmonify" style="margin:3px;text-align:center">';
			else
				$link='<div class="googmonify" style="margin:3px;float:'.$align.';">';
				
			
			$link.='<script type="text/javascript"><!--' . "\n"
					. 'google_ad_client = "pub-'.$pid.'";'
					. 'google_ad_slot = "'.$slot.'";'
					. 'google_ad_width = '.$width.';'
					. 'google_ad_height = '.$height.';'
					. "\n//-->"
					. '</script>'
					. '<script type="text/javascript"'
					. 'src="http://pagead2.googlesyndication.com/pagead/show_ads.js">'
					. '</script>'
					. '</div>';

			
			$new_content=str_replace('[googmonify]'.$sub.'[/googmonify]', $link, $new_content);
			
		}
	}
	
	return $new_content;
}

function googmonifyPost($content)
{
	if (!isGoogmonified($content))
		return $content;

	return googmonifyContent($content);
}

if (function_exists('add_action')) {
	add_action('the_content', 'googmonifyPost');
}


?>
