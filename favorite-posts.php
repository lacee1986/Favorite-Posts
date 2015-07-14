<?php
/**
* Plugin Name: Favorite Posts
* Plugin URI: 
* Description: Save the users favorite post.
* Version: 0.1
* Author: Laszlo Kruchio
*
*/

// Enqueue Scripts 
function fp_script() {
	wp_enqueue_script('fp-js', plugins_url( '/js/fp-js.js' , __FILE__ ), array(), '1.0.0', true );
	
	wp_localize_script( 'fp-js', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );
}
add_action( 'wp_enqueue_scripts', 'fp_script' );
  
// Create table on first activation 	
function favorite_posts_activation() {
   global $wpdb;
   $query = "CREATE TABLE " . $wpdb->prefix . "favorite_posts
               (id INT AUTO_INCREMENT,
               post_id INT NOT NULL,
               user_id INT NOT NULL,
               PRIMARY KEY (id))";
   $wpdb->get_results($query);
}
register_activation_hook( __FILE__, 'favorite_posts_activation' );

// Get Favorite Posts
function get_favorite_posts($userid) {
	global $wpdb;
	$match = $wpdb->get_results("
		SELECT wp_posts.ID, wp_favorite_posts.post_id 
		FROM wp_posts 
		INNER JOIN wp_favorite_posts 
		ON wp_posts.ID=wp_favorite_posts.post_id 
		AND wp_favorite_posts.user_id = '$userid'
		ORDER BY wp_posts.ID
	");
	if ( !empty($match) ) {
		$favorites = array();
		foreach ( $match as $post ){
			$favorites[] = $post->ID;
		}
		return $favorites;
	} else {
		return FALSE;
	}
}

// Save to DB
if ( isset($_POST['save_later']) && !empty($_POST['save_later']) ) {
	$the_post = $_POST['post_id'];
	$the_user = $_POST['user_id'];
	global $wpdb;
	$check = $wpdb->get_results("
		SELECT * 
		FROM wp_favorite_posts 
		WHERE post_id = '$the_post' 
		AND user_id = '$the_user'
	");
	if ( empty($check) ) {
		$wpdb->insert( 'wp_favorite_posts', array( 'post_id' => $the_post, 'user_id' => $the_user), array( '%d', '%d' ) );
	}
}

// Check Favorites
function check_favorite_posts($post_id, $user_id){
	global $wpdb;
	$match = $wpdb->get_results("
		SELECT * 
		FROM wp_favorite_posts 
		WHERE post_id = '$post_id'
		AND user_id = '$user_id'
	");
	if ( !empty($match) ) {
		return TRUE;
	} else {
		return FALSE;
	}
}

// Get Save Later Button
function get_favorites_add_link($title = 'Save it'){
	$postid = get_the_ID();
	$userid = get_current_user_id();
	if ( is_user_logged_in() ) {
		if ( !check_favorite_posts($postid,$userid) ) {
			if ( is_user_logged_in() ) {
				return '
					<form id="fp_post" method="post" action="" class="save">
						<input type="hidden" value="1" name="save_later" />
						<input type="hidden" value="'.get_the_ID().'" name="post_id" class="fp_id" />
						<input type="hidden" value="'.get_current_user_id().'" name="user_id" class="fp_user_id" />
						<input type="submit" class="save_it_later" value="'.$title.'" /> 
					</form>
				';
			}
		} else {
			if ( get_post_type() == 'product' ) {
				echo get_favorites_remove_link('Remove');
			} elseif ( get_post_type() == 'product' ) {
				echo get_favorites_remove_link('Remove bookmark');
			} else {
				echo get_favorites_remove_link('Remove bookmark');
			}
		}
	} else {
		return '<a href="" class="must_login">Read Later</a>';
	}
}

// Remove from DB
if ( isset($_POST['remove_it']) && !empty($_POST['remove_it']) ) {
	$the_post = $_POST['post_id'];
	$the_user = $_POST['user_id'];
	global $wpdb;
	$check = $wpdb->get_results("
		SELECT * 
		FROM wp_favorite_posts 
		WHERE post_id = '$the_post' 
		AND user_id = '$the_user'
	");
	if ( !empty($check) ) {
		$wpdb->delete( 'wp_favorite_posts', array( 'post_id' => $the_post, 'user_id' => $the_user), array( '%d', '%d' ) );
	}
}

// Get Remove Button
function get_favorites_remove_link($title = 'Remove', $class = NULL){
	if ( is_user_logged_in() ) {
		return '
			<form id="fp_post" method="post" action="" class="remove">
				<input type="hidden" value="1" name="remove_it" />
				<input type="hidden" value="'.get_the_ID().'" name="post_id" class="fp_id" />
				<input type="hidden" value="'.get_current_user_id().'" name="user_id" class="fp_user_id" />
				<input type="submit" class="remove_it_now '.$class.'" value="'.$title.'" /> 
			</form>
		';
	}
}


add_action('wp_ajax_savePost', 'savePost');
add_action('wp_ajax_nopriv_savePost', 'savePost');

function savePost(){
	
	$the_post = $_POST['post_id'];
	$the_user = $_POST['user_id'];
	
	global $wpdb;
	
	$check = $wpdb->get_results("
		SELECT * 
		FROM wp_favorite_posts 
		WHERE post_id = '$the_post' 
		AND user_id = '$the_user'
	");
	
	if ( empty($check) ) {
		$wpdb->insert( 'wp_favorite_posts', array( 'post_id' => $the_post, 'user_id' => $the_user), array( '%d', '%d' ) );
	}
	
	echo 'saved';
	
	exit;		
}

add_action('wp_ajax_removePost', 'removePost');
add_action('wp_ajax_nopriv_removePost', 'removePost');

function removePost(){
	
	$the_post = $_POST['post_id'];
	$the_user = $_POST['user_id'];
	
	global $wpdb;
	
	$check = $wpdb->get_results("
		SELECT * 
		FROM wp_favorite_posts 
		WHERE post_id = '$the_post' 
		AND user_id = '$the_user'
	");
	
	if ( !empty($check) ) {
		$wpdb->delete( 'wp_favorite_posts', array( 'post_id' => $the_post, 'user_id' => $the_user), array( '%d', '%d' ) );
	}
	
	
}
?>