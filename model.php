<?php
	
	require_once('../../../wp-config.php');
	
	$table_name = $wpdb->prefix . "gallery_buddy";
	
	if($_POST['job'] === 'insert')
	{
		$query = "INSERT INTO `" . $table_name . "` (post_id,img_id,pos) VALUES (" . $_POST['post_id'] . "," . $_POST['img_id'] . "," . $_POST['pos'] . ");";
        $wpdb->query($query);
	}
	
	if($_POST['job'] === 'remove')
	{
		$query = "DELETE FROM `" . $table_name . "` WHERE post_id='" . $_POST['post_id'] . "' AND img_id='" . $_POST['img_id'] . "';";
        $wpdb->query($query);
	}
	
	if($_POST['job'] === 'data')
	{
		$query = "SELECT * FROM `" . $table_name . "` WHERE post_id='" . $_POST['post_id'] . "' ORDER BY pos ASC;";
        $data = $wpdb->get_results($query, 'ARRAY_A');
		
		foreach($data as $key => $item) 
		{
			$data[$key]['title'] = get_the_title($item['img_id']);
			$data[$key]['icon'] = wp_get_attachment_thumb_url($item['img_id']);
		}
		
		echo json_encode($data);
	}
	
	if($_POST['job'] === 'sort')
	{
		foreach($_POST['mgsid'] as $pos => $img_id) 
		{
			$query = "UPDATE `" . $table_name . "` SET pos=" . $pos . " WHERE img_id=" . $img_id . ";";
	        $wpdb->query($query);
		}
		
	}

	
?>