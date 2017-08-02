=== Gallery-Buddy ===
Contributors: Jonua
Tags: gallery
Requires at least: 3.2
Tested up to: 3.3.1
Stable tag: 0.2.9

With Gallery-Buddy you can select and sort pictures at the medialibrary-tab of a post or page and embed them as an gallery via shortcode.

== Description ==

[youtube http://www.youtube.com/watch?v=kpwFLlrkH0M]

= Summary =
With Gallery-Buddy you can select and sort pictures at the medialibrary-tab of a post or page and embed them as an gallery via shortcode.

= Select Images =
Select a page or post, you want to add a gallery. Click at the "Add Media" button above the editor and choose the "Media Libray" Tab. Then use the checkboxes to define the images of the gallery. The selected images appears below in a list where you can sort and delete the gallery-images.

= Embedding =
To get the gallery on the current page or post, you have to insert following shortcode in the editor **[buddy-gallery width="600" height="450"]** Change width and height for your needs.
= Styling = 
This is the html and its classes they will be used by the plugin to build the gallery. So may you can style the gallery for your own by the classes.
`<div class="gallery-buddy" style="width: 600px">
	<!-- each image -->
	<div class="gallery-buddy-slide">
		<div class="gallery-buddy-img">
			<img src="" width="" height=""/>
			<div class="gallery-buddy-prev"><span class="gallery-buddy-btn">‹</span></div>
			<div class="gallery-buddy-next"><span class="gallery-buddy-btn">›</span></div>
		</div>
	</div>
</div>`

= Additional Functions =
There are some PHP functions for additional usage.
`<?php 
	/* returns all gallery-buddy images post_id´s as an array depending on the current post-id of a loop */
	get_gallery_buddy_images_by_postid();

	/* returns all gallery-buddy images post_id´s as an array depending to post-id 23 */
	get_gallery_images_by_postid(23);

	/* returns an image-tag from the first image of a gallery-buddy gallery */
	get_gallery_buddy_thumbnail($post->ID, 'thumbnail', 'classes');

	/* returns the post_id from the first image of a gallery-buddy gallery */
	get_gallery_buddy_thumbnail_id($post->ID);
?>`

== Installation ==

1. Upload folder `gallery-buddy` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. The Plugin installs a database-table named like "wp_gallery_buddy". May include this table in your backups.

== Frequently Asked Questions ==

= Can I have multiple galleries with different images per page or post? =
No, not yet.

= What languages are supported? =
* english
* german

== Screenshots ==

1. This shows the backend.
2. This is the default frontend.

== Videos ==

[youtube http://www.youtube.com/watch?v=kpwFLlrkH0M]

== Changelog ==

= 0.2.9 =
* Gallery frontend javascript improved. Encapsulated in object galleryBuddy.
* Backend actions conflict relieved. Preventing all actions until an action is finished.
* Fix an issue that always put the gallery at the beginning of the editors content.

= 0.2.8 =
* gallerylist now below media-library list for better usability
* improved sorting

= 0.2.7 =
* improvement for sorting images
* fix an issue of getting the first image of an gallery

= 0.2.6 =
* improvement for gallery styles

= 0.2.5 =
* fix style and script loading

= 0.2.4 =
* fix problem with language file

= 0.2.3 =
* fix problem with db-version checker at install

= 0.2.2 =
* languages (en/de)

= 0.2.1 =
* improvement for gallery styles

= 0.2 =
* fixed some shortcode issues