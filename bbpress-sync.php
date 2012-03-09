<?php
/*
Plugin Name: bbpress-sync
Plugin URI: 
Description: Syncar blog med forum
Version: 0.1
Author: Th3dz
Author URI: http://www.offbeatgames.se/
License: GPL2
*/
?>


<?php


                global $wpdb;
              

$forums = $wpdb->get_results("select * from wp_posts where post_type = 'forum'");

function get_category_from_post($post_id){
    get_the_category($post_id);
    return $category;
}
function get_forum_from_category($category){
    $options = get_option('bbp_sync_idx');
    //TODO parse string and check for forum.
    //return null if none is found.
}

/* TODO: load options and post ackording to setup  IF there is any! */
function bbp_sync_post_to_forum($post_id){
	global $wpdb;
	$post = $wpdb->get_row("select * from wp_posts where ID = " . $post_id);
	
    $category = get_category_from_post($post_id);
    $forum = get_forum_from_category($category);

    if(!is_null($forum)){
    	// copy entire post
    	$values = array(
    		"ID" => '0',
    		'post_author' =>$post->post_author,
    		'post_date' =>$post->post_date,
    		'post_date_gmt' =>$post->post_date_gmt,
    		'post_content' =>$post->post_content,
    		'post_title' =>$post->post_title,
    		'post_excerpt' =>$post->post_excerpt,
    		'post_status' =>$post->post_status,
    		'comment_status' =>$post->comment_status,
    		'ping_status' =>$post->ping_status,
    		'post_password' =>$post->post_password,
    		'post_name' =>$post->post_name,
    		'to_ping' =>$post->to_ping,
    		'pinged' =>$post->pinged,
    		'post_modified' =>$post->post_modified,
    		'post_modified_gmt' =>$post->post_modified_gmt,
    		'post_content_filtered' =>$post->post_content_filtered,
    		'post_parent' => $forum,
    		'guid' =>'',
    		'menu_order' =>$post->menu_order,
    		'post_type' => 'topic',
    		'post_mime_type' =>$post->post_mime_type,
    		'comment_count' =>$post->comment_count         
    	);
    	bbp_insert_topic($values);
    }
}
add_filter('publish_post', 'bbp_sync_post_to_forum');
?>


<?php
/* =================== OPTIONS PANE ===================*/

$option_group = "_bbsync_options";

add_action('admin_menu', 'my_plugin_menu');
add_action('admin_init', 'register_mysettings' );

function register_mysettings() { // whitelist options
  register_setting("_bbsync_options", 'bbp_sync_idx' );
}

function my_plugin_menu() {
    add_options_page('Forum sync', 'Forum sync', 'manage_options', 'bbpress-sync-id', 'my_plugin_options');
}

function my_plugin_options() {
    if (!current_user_can('manage_options'))  {
        wp_die( __('You do not have sufficient permissions to access this page.') );
    }
    ?>

    <div class="wrap">
    	<h1>Forum sync options</h1>
    	<p>Ställ in vilka kategorier som skall postas till respektive forum.</p>
        <p>Syntax:
            <ul>
                <li>[term_id]:[post_id]|</li>
                <li>ex: 5:43|2:34|6:75</li>
            </ul>
        </p>
    	<form method="post" action="options.php">
            <?php
                settings_fields("_bbsync_options");
                do_settings_fields("_bbsync_options");    
            ?>
            <input type="text" id="bbp_sync_idx" name="bbp_sync_idx" value="<?php echo get_option('bbp_sync_idx'); ?>" />
    		<br />
            <input type="submit" class="button-primary" onsubmit="return update()" value="<?php _e('Save Changes') ?>" />
    	</form>
    </div>

    <?php
}

?>