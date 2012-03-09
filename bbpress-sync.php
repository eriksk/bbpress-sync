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

/* TODO: load options and post ackording to setup */

function bbp_sync_post_to_forum($post_id){
	global $wpdb;
	$post = $wpdb->get_row("select * from wp_posts where ID = " . $post_id);
	
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
		'post_parent' => '31',
		'guid' =>'',
		'menu_order' =>$post->menu_order,
		'post_type' => 'topic',
		'post_mime_type' =>$post->post_mime_type,
		'comment_count' =>$post->comment_count         
	);
	bbp_insert_topic($values);
}
add_filter('publish_post', 'bbp_sync_post_to_forum');
?>


<?php
/* =================== OPTIONS PANE ===================*/

add_action('admin_menu', 'my_plugin_menu');

function my_plugin_menu() {
    add_options_page('Forum sync', 'Forum sync', 'manage_options', 'my-unique-identifier', 'my_plugin_options');
}

function my_plugin_options() {
    if (!current_user_can('manage_options'))  {
        wp_die( __('You do not have sufficient permissions to access this page.') );
    }
    ?>

    <div class="wrap">
    	<h1>Forum sync options</h1>
    	<p>Ställ in vilka kategorier som skall postas till respektive forum.</p>
    
    	<form>
    		<?php
    			global $wpdb;
    			$categories = $wpdb->get_results(
    				"
    					select * 
    					from wp_terms t
    					where term_id in 
    						(select term_id 
    						 from wp_term_taxonomy tt 
    						 where t.term_id = tt.term_id and
    						 taxonomy = 'category') 
    				");
    			$forums = $wpdb->get_results("select * from wp_posts where post_type = 'forum'");
    			foreach($categories as $op){
    				?>
    				<div style="float:left;margin-right:10px;">
	    				<label>Kategori</label>
    					<select name="<?php $op ?>">
    						<option>-</option>
    						<?php
    						foreach($categories as $cat){
    							echo '<option>' . $cat->name . '</option>';
    						}
    						?>
    					</select>
    				</div>
    				<div>
	    				<label>Forum</label>	
    					<select name="<?php $op ?>">
    						<option>-</option>
    						<?php
    						foreach($forums as $f){
    							echo '<option>' . $f->post_title . '</option>';
    						}
    						?>
    					</select>
    				</div>
    				<br />
    			<?php
    			}
    		?>
    		<br />
    		<input type="submit" value="Spara"></input>
    	</form>
    </div>

    <?php
}

/* TODO: save and load options. */

?>