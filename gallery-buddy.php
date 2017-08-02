<?php
    
	/*
	Plugin Name: Gallery-Buddy
	Plugin URI: http://www.johannheyne.de/wordpress/gallery-buddy/
	Description: Pick and sort some images at the media-library tab of a post or page.
	Version: 0.2.9
	Author: Johann Heyne
	Author URI: http://www.johannheyne.de/
	Update Server: http://www.johannheyne.de/wordpress/gallery-buddy/
	Min WP Version: 3.2
	Max WP Version: 3.3.1
	*/
	
    /*
    Copyright (C) 2011
    Contact me at mail@johannheyne.de 
    */
    
	
   	/**
	 * LOAD TRANSLATION
	 */
	
	load_plugin_textdomain('gallery-buddy', false, basename( dirname( __FILE__ ) ) . '/languages' );
	


	/**
	 * INSTALATION
	 */
	
	register_activation_hook(__FILE__, 'gallery_buddy_install');
	function gallery_buddy_install() {
		
		/* Prüft auf ausreichende WordPress-Version */
		
		global $wp_version;
		
		if (version_compare($wp_version, '3.2.1', "<"))
		{
			deactivate_plugins(basename(__FILE__));
			wp_die(_e('This Plugin »Gallery-Buddy« requires WordPress 3.2.1 or higher.', 'gallery-buddy'));
		}
		
		
		
		/* Setup */
		
		global $wpdb;
		
		$gallery_buddy_this_db_version = "0.2";
		$gallery_buddy_installed_db_version = get_option("mediathekgallery_this_db_version");
		if (!$gallery_buddy_installed_db_version) $gallery_buddy_installed_db_version = get_option("gallery_buddy_db_version");
		
		$table_name = $wpdb->prefix . "gallery_buddy";
		
		
		
		
		if (!$gallery_buddy_installed_db_version) {
			
			/* Database Install */
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			if($wpdb->get_var("SHOW TABLES LIKE `" . $table_name . "`") != $table_name)
			{
				$sql =
				"CREATE TABLE `" . $table_name . "` (
				`post_id` bigint(20) DEFAULT NULL,
				`img_id` bigint(20) DEFAULT NULL,
				`pos` int(3) DEFAULT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				dbDelta($sql);

				add_option("gallery_buddy_db_version", $gallery_buddy_this_db_version);

			}
			
		}
		else {
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			/* Update from 0.1 */

			if ($gallery_buddy_installed_db_version === '0.1') {

				// Änderungen von Version 0.1 auf Version 0.2

				$wpdb->query("ALTER TABLE `gallery` RENAME TO `" . $table_name . "`;");
				$sql =
				"CREATE TABLE `" . $table_name . "` (
				`post_id` bigint(20) DEFAULT NULL,
				`img_id` bigint(20) DEFAULT NULL,
				`pos` int(3) DEFAULT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				dbDelta($sql);
				
				$gallery_buddy_installed_db_version = '0.2';
				update_option("gallery_buddy_db_version", $gallery_buddy_installed_db_version);
				delete_option('mediathekgallery_this_db_version');
				
			}
			
			/* Update from 0.2 
			
			if($gallery_buddy_installed_db_version === '0.2')
			{
				// Änderungen von Version2 auf Version3
				
				$sql = 
				"CREATE TABLE `" . $table_name . "` (
				`post_id` bigint(20) DEFAULT NULL,
				`img_id` bigint(20) DEFAULT NULL,
				`pos` int(3) DEFAULT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				dbDelta($sql);
				
				$gallery_buddy_installed_db_version = '0.3';
				update_option( "gallery_buddy_db_version", $gallery_buddy_installed_db_version);
			}
			
			*/
			
		}
		
		
		/* Deaktiviert das Plugin, wenn zur vorhandenen Plugin-Datenbank 
		 * keine Versionsnummer in den WordPress-Optionen vorhanden ist.
		
		if(!$gallery_buddy_installed_db_version)
		{
			deactivate_plugins(basename(__FILE__));
			wp_die('<strong>Es existiert keine in den WordPress-Optionen notwendige Datenbankversion zur vorhandenen gallery_buddy-Datentabelle.</strong><br/><br/>Eine mögliche Ursache wäre, wenn die Plugindatenbanktabelle aus einer anderen WordPress-Datenbank in diese WordPress-Installation eingefügt wurde. In diesem Fall muss auch der Eintrag "gallery_buddy_db_version" in den WordPress-Optionen mit in diese WordPress-Installation übernommen werden.');
		}
		*/
		
		
	}
	
	
	// add Backend Styles and Help
	
	add_action('admin_head', 'ini_gallery_buddy');
	function ini_gallery_buddy() {
		wp_enqueue_style('gallery-buddy-styles', plugins_url( 'styles.css', __FILE__ ), array(), '0.2.8');
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('gallery-buddy-main', plugins_url( 'script.js', __FILE__ ), array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'), '0.2.8', true);
	}
	
	add_action('wp_enqueue_scripts', 'ini_gallery_buddy_front');
	function ini_gallery_buddy_front() {
		wp_enqueue_style('gallery-buddy-styles', plugins_url( 'gallery-buddy.css', __FILE__ ), array(), '1');
		wp_enqueue_script('gallery-buddy-lang', plugins_url( 'gallery-buddy.js', __FILE__ ), array('jquery'), '1');
	}
	

	
	// Wenn Post oder Attachement gelöscht wird
	
	function remove_gallery_img_on_post_delete($pid) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . "gallery_buddy";
		
		$query = "DELETE FROM `" . $table_name . "` WHERE post_id='" . $pid . "';";
        $wpdb->query($query);

		return true;
	}
	function remove_gallery_img_on_post_delete_init() {
	  if (current_user_can('delete_posts')) add_action('delete_post', 'remove_gallery_img_on_post_delete', 10);
	}
	add_action('admin_init', 'remove_gallery_img_on_post_delete_init');
	
	function remove_gallery_img_on_attachement_delete($aid) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . "gallery_buddy";
		
		$query = "DELETE FROM `" . $table_name . "` WHERE img_id='" . $aid . "';";
        $wpdb->query($query);

		return true;
	}
	function remove_gallery_img_on_attachment_delete_init() {
	  if (current_user_can('delete_posts')) add_action('delete_attachment', 'remove_gallery_img_on_attachement_delete', 10);
	}
	add_action('admin_init', 'remove_gallery_img_on_attachment_delete_init');
	
	
	
	// Shortcode
	
	function shortcode_gallery_buddy($atts) {
		
		extract(shortcode_atts(array(
			'width' => esc_attr(get_option('width_parameter')),
			'height' => esc_attr(get_option('height_parameter'))
        ), $atts));

		if(!$width) $width = 600;
		if(!$height) $height = 450;
		
		global $wpdb;
		$table_name = $wpdb->prefix . "gallery_buddy";
		
		$query = "SELECT * FROM `" . $table_name . "` as g LEFT JOIN " . $wpdb->posts . " as p ON g.img_id = p.ID WHERE g.post_id='" . get_the_ID() . "' ORDER BY pos ASC;";
        $data = $wpdb->get_results($query, 'ARRAY_A');
		foreach($data as $key => $item) 
		{
			$meta[$item['img_id']] = $item;
		}
		
		$html = '<div class="gallery-buddy" style="width: ' . $width . 'px">';
		
		$array = get_gallery_buddy_images_by_postid();
		foreach($array as $key => $item) 
		{	
			$html .= '<div class="gallery-buddy-slide">';
				$html .= '<div class="gallery-buddy-img">';
					// $html .= '<a href="' . wp_get_attachment_url($item['img_id']) . '">';
					$html .= wp_get_attachment_image($item['img_id'], array($width,$height));
					$html .= '<div class="gallery-buddy-prev"><span class="gallery-buddy-btn">‹</span></div>';
					$html .= '<div class="gallery-buddy-next"><span class="gallery-buddy-btn">›</span></div>';
					// $html .= '</a>';
				$html .= '</div>';
				$html .= '<p>' . $meta[$item['img_id']]['post_title'] . '</p>';
			$html .= '</div>';
		}
		
		$html .= '</div>';
		
		return $html;

	}
	add_shortcode('buddy-gallery', 'shortcode_gallery_buddy');
	
	
	
	// Frontend: Galeriebilder per PostID 
	
	function get_gallery_buddy_images_by_postid($post_id = false) 
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "gallery_buddy";
		
		if(!$post_id) $post_id = get_the_ID();
		$query = "SELECT * FROM `" . $table_name . "` WHERE post_id='" . $post_id . "' ORDER BY pos ASC;";
        $data = $wpdb->get_results($query, 'ARRAY_A');
	
		return $data;
	    
	}
	
	// Frontend: Thumbnail aus erstem Bild der Bildergalerie

	function get_gallery_buddy_thumbnail($pid,$size = 'thumbnail', $class = 'post_thumbnail thumbnail')
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "gallery_buddy";
		
		$query = "SELECT * FROM `" . $table_name . "` as g LEFT JOIN " . $wpdb->posts . " as p ON g.img_id = p.ID WHERE g.post_id='" . $pid . "' AND g.pos=0;";
        	$data = $wpdb->get_row($query, 'ARRAY_A');
		
		$html = wp_get_attachment_image($data['img_id'], $size,'',array("class"=>$class));
		
		if($html != '')
		{
			return $html;
		}
		else
		{
			return FALSE;
		}
	}
	
	// Frontend: ID aus erstem Bild der Bildergalerie
	
	function get_gallery_buddy_thumbnail_id($pid)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "gallery_buddy";
		
		$query = "SELECT * FROM `" . $table_name . "` as g LEFT JOIN " . $wpdb->posts . " as p ON g.img_id = p.ID WHERE g.post_id='" . $pid . "' AND g.pos=0;";
        $data = $wpdb->get_row($query, 'ARRAY_A');
		
		if($data != NULL)
		{
			return $data['img_id'];
		}
		else
		{
			return FALSE;
		}
		
	}



?>