=== Googmonify ===
Contributors: GaryKeorkunian
Donate link: http://www.gara.com/projects/googmonify/
Tags: widget, google, adsense, ad, monetize
Requires at least: 2.0.2
Tested up to: 2.0.2
Stable tag: trunk
Version: 0.1

Googmonify allows you to insert Google AdSense ad units into your posts.

== Description ==

This WordPress plugin allows a poster to include Google AdSense ad units with in their post.

== Installation ==

1.  Upload 'googmonify.php' to the '/wp-content/plugins/' directory
2.  Activate the plugin through the 'Plugins' menu in WordPress
3.  Insert Googmonify tags into your posts.

== Frequently Asked Questions ==

None.

== Screenshots ==

None.

== Googmonify Tags ==

To include a Google AdSense ad in your post simply add the following googmonify tag set in the location where you want the ad:

[googmonify]slot:align:width:height:publisher_id[/googmonify]

where ...

  * slot is the id of the Google AdSense ad.
  * align is the alignment of the ad block (ex. right, left or center).  Using right and left values will cause the ads to float right or left respectively.  If you use center, the tag set should be placed on its own line.
  * width is the width of the ad; only used for Omakase ads; default=120; ignored for text and product
  * height is the height of the ad; only used for Omakase ads; default=240; ignored for text and product
  * publisher_id is your Google AdSense Publisher ID

Example

A 250x250 ad that floats to the right:

[googmonify]1234567890:right:300:250:9876543210123456[/googmonify]

== Current Limitations ==

- the Google AdSense Publisher ID can not be set globally, it must be inserted for the ad to appear.
- not tested for invalid [googmonify] markup