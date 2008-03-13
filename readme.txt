=== Googmonify ===
Contributors: GaryKeorkunian
Donate link: http://www.gara.com/projects/googmonify/
Tags: widget, google, adsense, ad, ads, monetize, analytics, statistics, stats
Requires at least: 2.0.2
Tested up to: 2.0.2
Stable tag: trunk
Version: 0.5.1

== Description ==

Googmonify lets you insert Google AdSense ad units into your posts with easy to use tag sets (requires a Google AdSense account).

Googmonify can also insert your Google Analytics tracking code in to every page on your blog help so you can keep accurate statistics (requires a Google Analytics account).

Googmonify Your Blog Today!!

== Installation ==

1.  Upload 'googmonify.php' to the '/wp-content/plugins/' directory
2.  Activate the plugin through the 'Plugins' menu in WordPress
3.  Enter your Google Account Info on the 'Options' | 'Googmonify' page
4.  Insert Googmonify tags into your posts.

== Frequently Asked Questions ==

None.

== Screenshots ==

1.  The Googmonify Options Screen

== Googmonify Tags ==

To include a Google AdSense ad in your post simply add the following googmonify tag set in the location where you want the ad:

[googmonify]slot:align:width:height[/googmonify]

where ...

* slot is the id of the Google AdSense ad.
* align is the alignment of the ad block (ex. right, left or center).  Using right and left values will cause the ads to float right or left respectively.  If you use center, the tag set should be placed on its own line.
* width is the width of the ad; only used for Omakase ads; default=120; ignored for text and product
* height is the height of the ad; only used for Omakase ads; default=240; ignored for text and product
  
Note - the width and height properties must match the dimensions of the AdSense unit you are inserting.

Example

A 350x250 ad that floats to the right:

[googmonify]1234567890:right:300:250[/googmonify]
