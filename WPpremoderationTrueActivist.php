<?php 

/*
Plugin Name: WPpremoderationTrueActivist
Description: WPpremoderationTrueActivist
Version: 1.0
Author: WebWare
*/

// Hook for adding admin menus
add_action('admin_menu', 'premoderationTa_add_pages');

// action function for above hook
function premoderationTa_add_pages() {
    add_menu_page('PremoderationTA', 'PremoderationTA', 8, __FILE__, 'PremoderationTA_admin');
}

function PremoderationTA_admin() {
	if (!empty($_POST)) {
		if ($_POST['submit']=='Save Changes') {
			update_option('premoderationTaReplace',$_POST['replace']);
		}
	}

    echo "<h2>Test Toplevel</h2>";
    echo '<form method="POST">';
    echo "<textarea name='replace'>".get_option('premoderationTaReplace')."</textarea>";
    submit_button();
    echo "</form>";
}


function filterHookTA(){
	remove_action( 'save_post', 'premoderationTA');
}

function premoderationTA(){


	if( current_user_can('contributor') ) {

		$premoderationTaReplace = get_option('premoderationTaReplace');

		if (!empty($premoderationTaReplace) && !empty(get_post($post_id)->post_content)) {

			$replacements = explode(',', $premoderationTaReplace);

			if  (wp_is_post_revision( $post_id ) && get_post($post_id)->post_status != 'pending')
			return;

		    $string = implode('|', $replacements);

		    foreach ($replacements as $replace) {
		    	$pattern = '/.*'.$replace.'.*/';
		    	$subject = get_post($post_id)->post_content;
		    	$authorId = get_post($post_id)->post_author;
		    	$authorEmail = get_userdata($authorId)->user_email;
		    	if (preg_match($pattern, $subject)) {
		    		
		    		//mail($authorEmail, 'wp_subject', 'message');

		    		// if ( ! wp_is_post_revision( $post_id ) ){
		    		// 	remove_action('save_post', 'premoderationTA');
		    			
		    		// 	$new_post_status = array(
		    		// 			'ID' => $post_id,
		    		// 			'post_status' => 'draft'
		    		// 	);
		    			
		    		// 	wp_update_post($new_post_status);
		    			
		    		// 	add_action( 'save_post', 'premoderationTA');
		    		// }

		    		return;
		    	}
		    }


		}

 	}

}
 

add_action( 'save_post', 'premoderationTA');

add_action('pending_to_published','filterHookTA');
add_action('pending_to_draft','filterHookTA');
add_action('pending_to_trash','filterHookTA');

add_action('trash_to_published','filterHookTA');
add_action('trash_to_draft','filterHookTA');
add_action('trash_to_pending','filterHookTA');

add_action('draft_to_published','filterHookTA');
add_action('draft_to_draft','filterHookTA');
add_action('draft_to_trash','filterHookTA');


 ?> 