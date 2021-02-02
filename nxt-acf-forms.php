<?php
/**
 * @package Contact Forms with ACF by nexTab
 * @version 1.1
 */
/*
Plugin Name: Contact Forms based on Advanced Custom Fields (ACF) by nexTab
Plugin URI: https://nextab.de/
Description: This plugin helps you to receive messages by setting up a custom post type. It also sends a notification via e-mail, so it effectively works like a contact form plugin. It requires Advanced Custom Fields to work.
Author: nexTab - Oliver Gehrmann
Version: 1.1
Text Domain: nxt-acf-forms
Author URI: http://nexTab.de/
*/

/* setting up internationalization */
function nxt_i18n() {
	load_plugin_textdomain( 'nxt-acf-forms', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/'  );
}
add_action( 'plugins_loaded', 'nxt_i18n' );

/**
 * Registers the 'message' post type.
 */
function nxt_msg_init() {
	register_post_type( 'message', array(
		'labels'	=> array(
			'add_new_item'				=> __( 'Add New Message', 'nxt-acf-forms' ),
			'add_new'					=> __( 'Add New', 'nxt-acf-forms' ),
			'all_items'					=> __( 'All Messages', 'nxt-acf-forms' ),
			'archives'					=> __( 'Message Archives', 'nxt-acf-forms' ),
			'attributes'				=> __( 'Message Attributes', 'nxt-acf-forms' ),
			'edit_item'					=> __( 'Edit Message', 'nxt-acf-forms' ),
			'featured_image'			=> _x( 'Featured Image', 'message', 'nxt-acf-forms' ),
			'filter_items_list'			=> __( 'Filter Messages list', 'nxt-acf-forms' ),
			'insert_into_item'			=> __( 'Insert into Message', 'nxt-acf-forms' ),
			'items_list_navigation'		=> __( 'Messages list navigation', 'nxt-acf-forms' ),
			'items_list'				=> __( 'Messages list', 'nxt-acf-forms' ),
			'menu_name'					=> __( 'Messages', 'nxt-acf-forms' ),
			'name'						=> __( 'Messages', 'nxt-acf-forms' ),
			'new_item'					=> __( 'New Message', 'nxt-acf-forms' ),
			'not_found_in_trash'		=> __( 'No Messages found in trash', 'nxt-acf-forms' ),
			'not_found'					=> __( 'No Messages found', 'nxt-acf-forms' ),
			'parent_item_colon'			=> __( 'Parent Message:', 'nxt-acf-forms' ),
			'remove_featured_image'		=> _x( 'Remove featured image', 'message', 'nxt-acf-forms' ),
			'search_items'				=> __( 'Search Messages', 'nxt-acf-forms' ),
			'set_featured_image'		=> _x( 'Set featured image', 'message', 'nxt-acf-forms' ),
			'singular_name'				=> __( 'Message', 'nxt-acf-forms' ),
			'uploaded_to_this_item'		=> __( 'Uploaded to this Message', 'nxt-acf-forms' ),
			'use_featured_image'		=> _x( 'Use as featured image', 'message', 'nxt-acf-forms' ),
			'view_item'					=> __( 'View Message', 'nxt-acf-forms' ),
			'view_items'				=> __( 'View Messages', 'nxt-acf-forms' ),
		),
		'exclude_from_search'	=> true,
		'has_archive'			=> false,
		'hierarchical'			=> false,
		'menu_icon'				=> 'dashicons-testimonial',
		'public'				=> false,
		'query_var'				=> false,
		'rewrite'				=> false,
		'show_in_rest'			=> false,
		'show_ui'				=> true,
		'supports'				=> array( 'title' ),
		) 
	);

	// Now register the hierarchical taxonomy = Courses
	register_taxonomy('message-category', 'message', 
		[
			'labels' => [
				'name' => __('Message Category', 'nxt-acf-forms'),
				'singular_name' => __('Message Category','nxt-acf-forms' ),
				'add_new_item' => __('Add new Message Category', 'nxt-acf-forms'),
				'edit_item' => __('Edit Category', 'nxt-acf-forms'),
				'parent_item' => __('Parent Category', 'nxt-acf-forms'),
			],
			'hierarchical' => true,
			'query_var' => true,
			'rewrite' => ['slug' => 'schulung'],
			'show_admin_column' => true,
			'show_ui' => true,
			'public' => false,
			'update_count_callback' => '_update_post_term_count',
		]
	);
}
add_action( 'init', 'nxt_msg_init' );

/**
 * Sets the post updated messages for the 'message' post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the 'message' post type.
 */
function nxt_msg_update( $messages ) {
	global $post;
	$permalink = get_permalink( $post );
	$messages['message'] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'Message updated. <a target="_blank" href="%s">View Message</a>', 'nxt-acf-forms' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'nxt-acf-forms' ),
		3  => __( 'Custom field deleted.', 'nxt-acf-forms' ),
		4  => __( 'Message updated.', 'nxt-acf-forms' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Message restored to revision from %s', 'nxt-acf-forms' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Message published. <a href="%s">View message</a>', 'nxt-acf-forms' ), esc_url( $permalink ) ),
		7  => __( 'Message saved.', 'nxt-acf-forms' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'Message submitted. <a target="_blank" href="%s">Preview message</a>', 'nxt-acf-forms' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf( __( 'Message scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview message</a>', 'nxt-acf-forms' ),
		date_i18n( __( 'M j, Y @ G:i', 'nxt-acf-forms' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Message draft updated. <a target="_blank" href="%s">Preview message</a>', 'nxt-acf-forms' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);
	return $messages;
}
add_filter( 'post_updated_messages', 'nxt_msg_update' );

/**
 * Add more colums to the listing of messages in the backend
 */
function nxt_more_columns($columns) {
	$columns = array(
		'cb'					=> '<input type="checkbox" />',
		'title' 				=> __('Title', 'nxt-acf-forms'),
		'cf_category'			=> __('Category', 'nxt-acf-forms'),
		'cf_email' => __('E-Mail', 'nxt-acf-forms'),
		'cf_tel'				=> __('Telephone', 'nxt-acf-forms'),
		'nxt_application_status'		=> __('Message Status', 'nxt-acf-forms'),
		'date'					=> __('Date', 'nxt-acf-forms'),
	);
	return $columns;
}
add_filter('manage_edit-message_columns', 'nxt_more_columns');

/**
 * Populate extra colums in the backend
 */
function nxt_post_custom_columns($column) {
	global $post;
	$post_id = get_the_ID();
	$nxt_field_email = get_field('cf_email', $post_id);
	$nxt_field_cat = get_the_terms($post_id, 'message-category');
	$nxt_field_tel = get_field('cf_tel', $post_id);
	$nxt_field_ms_value = get_field('nxt_application_status', $post_id);
	// print_r($nxt_field_cat);
	$nxt_field_value = $nxt_field_cat[0]->term_id;
	$nxt_field_desc = $nxt_field_cat[0]->name;
	// print_r($column);
	switch ($column) {
		case "cf_category":
			echo '<a href="' . admin_url( 'edit.php?post_type=message&cf_category=' . urlencode( $nxt_field_value ) ) . '">' . $nxt_field_desc . '</a>';
			// echo $nxt_field_tax;
			break;
		case "cf_email":
			echo '<a href="' . admin_url( 'edit.php?post_type=message&cf_email=' . urlencode( $nxt_field_email ) ) . '">' . $nxt_field_email . '</a>';
			// echo $nxt_field_email;
			break;
		case "cf_tel":
			echo '<a href="' . admin_url( 'edit.php?post_type=message&cf_tel=' . urlencode( $nxt_field_tel ) ) . '">' . $nxt_field_tel . '</a>';
			break;
		case "nxt_application_status":
			echo '<a href="' . admin_url( 'edit.php?post_type=message&nxt_application_status=' . urlencode( $nxt_field_ms_value ) ) . '">' . $nxt_field_ms_value . '</a>';
			break;
	}
}
add_action ('manage_message_posts_custom_column','nxt_post_custom_columns');

// Make column headers clickable to initiate sort
function nxt_sort_columns( $columns ) {
	// $columns['message_category'] = 'message_category';
	$columns['cf_email'] = 'cf_email';
	$columns['cf_category'] = 'cf_category';
	$columns['nxt_application_status'] = 'nxt_application_status';
	$columns['cf_tel'] = 'cf_tel';
	return $columns;
}
add_filter( 'manage_edit-message_sortable_columns', 'nxt_sort_columns' );

// Fix sorting logic
function nxt_custom_query_variables( $vars ) {
	$vars[] .= 'cf_email';
	$vars[] .= 'cf_category';
	$vars[] .= 'cf_tel';
	$vars[] .= 'nxt_application_status';
	return $vars;
}
add_filter( 'query_vars', 'nxt_custom_query_variables' );

function nxt_alter_query( $query ) {
	// check if we are in admin
	if ( !is_admin() ) return;
	// check if the post type is set AND if it's set, make sure it's "message"
	if ( isset($query->query['post_type'])) {
		if( 'message' != $query->query['post_type'] ) return;
	}

	if ( !empty(get_query_var('cf_email'))) {
		$query->set( 'meta_key', 'cf_email' );
		$query->set( 'meta_value', $query->query_vars['cf_email'] );
	}
	if ( !empty(get_query_var('cf_tel'))) {
		$query->set( 'meta_key', 'cf_tel' );
		$query->set( 'meta_value', $query->query_vars['cf_tel'] );
	}
	if ( !empty(get_query_var('cf_category'))) {
		$query->set( 'taxonomy', 'message-category' );
		$query->set( 'terms', $query->query_vars['cf_category'] );
	}
	if ( !empty(get_query_var('nxt_application_status'))) {
		$query->set( 'meta_key', 'nxt_application_status' );
		$query->set( 'meta_value', $query->query_vars['nxt_application_status'] );
	}

	$orderby = $query->get( 'orderby');
	if( 'cf_email' == $orderby ) {
		$query->set('meta_key','cf_email');
		$query->set('orderby','meta_value');
	} 
	if( 'cf_tel' == $orderby ) {
		$query->set('meta_key','cf_tel');
		$query->set('orderby','meta_value_num');
	}
	if( 'cf_category' == $orderby ) {
		$query->set('meta_key','cf_category');
		$query->set('orderby','meta_value');
	}
	if( 'nxt_application_status' == $orderby ) {
		$query->set('meta_key','nxt_application_status');
		$query->set('orderby','meta_value');
	}
}
add_action( 'pre_get_posts', 'nxt_alter_query' );

/* Create a contact form via shortcode */
function nxt_contact_form($atts, $content = null) {
	$defaults = shortcode_atts( array(
		'button' => __('Send Message', 'nxt-acf-forms'),
		'cat' => '',
		'date_start' => '',
		'date_end' => '',
		'location' => '',
		'field_group_id' => '',
		'fields' => '',
		'participants_fields' => '',
		'id' => 'new-message',
		'post_id' => 'new_post',
		'post_status' => 'publish',
		'post_type' => 'message',
		// 'set_category' => '',
		'show_class' => '',
		'ty_message' => __('Thank you for getting in touch!', 'nxt-acf-forms'),
		'url' => __('/thank-you/', 'nxt-acf-forms'),
	), $atts );
	$message_category = $defaults['cat'];
	$nxt_date_start = $defaults['date_start'];
	$nxt_date_end = $defaults['date_end'];
	$nxt_location = $defaults['location'];
	// echo '<pre>'; print_r($defaults); echo '</pre>'; 
	// Get all fields associated with the post type the form displays
	$form_fields = [];
	if ($defaults['field_group_id'] != '') {
		$fields_in_cpt = acf_get_fields_by_id($defaults["field_group_id"]);
		foreach($fields_in_cpt as $single_field_group) {
			if( strpos($single_field_group['wrapper']['class'], 'no_show') === FALSE ) {
				$form_fields[] = $single_field_group['key'];
			}
		}
	} elseif($defaults['fields'] != '') {
		// echo "<p>Keine Fields submitted</p>";
		$field_additions = explode(',',$defaults['fields']);
		foreach($field_additions as $field_addition) $form_fields[] = $field_addition;
	} elseif($defaults["show_class"] != '') {
		// echo "<!-- nxt debug show_class = " . $defaults['show_class'] . " -->";
		$groups = acf_get_field_groups(array('post_type' => $defaults['post_type']));
		$fields_in_cpt = acf_get_fields_by_id($groups[0]['ID']);
		foreach($fields_in_cpt as $single_field_group) {
			if( strpos($single_field_group['wrapper']['class'], $defaults["show_class"]) > -1 ) {
				$form_fields[] = $single_field_group['key'];
			}
		}
	} else {
		$groups = acf_get_field_groups(['post_type' => $defaults['post_type']]);
		$fields_in_cpt = acf_get_fields_by_id($groups[0]['ID']);
		// print_r($fields_in_cpt);
		// Add all fields to the form except those with the class "no_show"
		foreach($fields_in_cpt as $single_field_group) {
			// if( strpos($single_field_group['wrapper']['class'], 'showme') !== FALSE ) {
				// echo "Class = " . $single_field_group['wrapper']['class'];
			if( strpos($single_field_group['wrapper']['class'], 'no_show') === FALSE ) {
				$form_fields[] = $single_field_group['key'];
			}
		}
	}

	// filter acf_form output before it is being generated
	if($defaults['participants_fields'] != '') {
		$filter_participants = explode(',', $defaults['participants_fields']);
		foreach($filter_participants as $field_to_remove) {
			filter_acf_form($field_to_remove);
		}
	}

	ob_start();
	acf_form(array(
		'id'				=> $defaults['id'],
		'honeypot'			=> true,
		'post_id'			=> $defaults['post_id'],
		'new_post'			=> array(
			'post_type'		=> $defaults['post_type'],
			'post_status'	=> $defaults['post_status'],
		),
		'post_title'		=> false,
		'post_content'		=> false,
		'uploader'			=> 'wp',
		'return'			=> $defaults['url'],
		'fields'			=> $form_fields,
		'updated_message'	=> $defaults['ty_message'],
		'submit_value'		=> $defaults['button'],
		)
	);
	$html = ob_get_contents();
	ob_end_clean();
	$html = str_replace('</form>', '<input type="text" id="acf-field_5dbf4d58a3c15" name="acf[field_5dbf4d58a3c15]" style="visibility: hidden; position: absolute; display: none; height: 0; width: 0;" required="required" value="' . $message_category . '" /></form>', $html);
	if(!in_array('cf_email', $form_fields) && is_user_logged_in()) {
		$current_user = wp_get_current_user();
		$html = str_replace('</form>', '<input type="email" id="acf-field_5dbf568e69c9f" name="acf[field_5dbf568e69c9f]" placeholder="E-Mail" required="required" style="visibility: hidden; position: absolute; display: none; height: 0; width: 0;" value="' . $current_user->user_email . '"></form>', $html);
	}
	return $html;
}

/* The following function sends an e-mail to all administrators when a new post with the post type 'message' is created on the front end (via the form we created above) and also updates the title of the post to something meaningful. */
function nxt_send_message_email( $post_id ) {
	if( ( get_post_type($post_id) !== 'message' && get_post_status($post_id) == 'draft' ) || ( is_admin() ) ) {
		return;
	}
	update_post_meta($post_id, 'nxt_application_status', 'erhalten');
	$nxt_output_category = '';
	if(get_field('cf_category', $post_id) != '') {
		wp_set_object_terms($post_id, (int)get_field('cf_category', $post_id), 'message-category');
		$nxt_message_category = get_term((int)get_field('cf_category', $post_id), 'message-category');
		$nxt_output_category = $nxt_message_category->name;
	}
	$date_string = '';
	date_default_timezone_set(get_option('timezone_string'));
	$date_string = 'erhalten: ' . date('Y-m-d @ H:i');
	$sender_name = get_field('cf_first_name', $post_id) . ' ' . get_field('cf_last_name', $post_id);
	$new_post_title = $nxt_output_category . ' - ' . $sender_name . ' - ' . $date_string;
	$post_update = array(
		'ID'			=> $post_id,
		'post_title'	=> $new_post_title,
	);
	wp_update_post( $post_update );
	
	// create a .csv file after a post has been generated
	// create_file_in_output(cpt_fields_to_array($post_id), $post_id . "-anfrage.csv");

	$message = __('<p>Hi,</p><p>this message has just been submitted on your website:</p><p>', 'nxt-acf-forms');
	$message .= '<p><strong>Datum: </strong> ' . $date_string;
	$message .= '<br /><strong>Schulung: </strong> ' . $nxt_output_category;
	$message .= '<br /><strong>Ort: </strong> ' . $nxt_output_location . '</p><hr />';
	$fields = get_field_objects( $post_id );
	// echo '<pre>'; print_r($fields); echo '</pre>';
	$attachments = [];
	if( $fields ) {
		foreach( $fields as $single_field_group ) {
			/* if( $single_field_group['type'] == 'taxonomy' ) {
				$message .= '</p><p><strong>' . $single_field_group['label'] . "</strong><br />" . $single_field_group['value']->name . '</p>';
				// <p>Und jetzt für die $single_field_group: ' . print_r($single_field_group, true) . '</p><p>';
			} */
			if(in_array($single_field_group['name'], ['nxt_invoice_adr_check', 'nxt_participants', 'nxt_agb'])) {
				$message .= '<hr style="margin: 10px 0;" />';
			}
			if (!in_array($single_field_group['name'], ['_validate_email', 'cf_category', 'nxt_application_status', 'nxt_date_start', 'nxt_date_end', 'nxt_location'])) {
				if($single_field_group['type'] == 'repeater') {
					$message .= '<p><strong>' . $single_field_group["label"] . '</strong>:</p>';
					foreach($single_field_group['value'] as $participant) {
						$message .= '<hr />';
						foreach($participant as $sub_field => $sub_field_value) {
							if($sub_field_value != '') {
								$message .= '<strong>' . $sub_field . '</strong>: ' . $sub_field_value . '<br />';
							}
						}
					}
				} else {
					if($single_field_group["value"] != '') {	
						$message .= '<strong>' . $single_field_group["label"] . '</strong>: ' . $single_field_group["value"] . '<br />';
					}
				}
			}
		}
	}
	/* Upload functionality
	$upload_dir = wp_upload_dir();
	$upload_url = $upload_dir["baseurl"] . '/csv-export/' . $post_id . '-anfrage.csv';
	*/
	$message .= '</p><p>' . __('You can also find the message here: ', 'nxt-acf-forms') . ' <a href="' . get_admin_url() . 'post.php?post=' . $post_id . '&action=edit' . '">Edit message</a>.</p><p>' . __('That is all. Make sure to respond soon!</p>', 'nxt-acf-forms');
	$subject 	= $new_post_title;
	$headers = array('Content-Type: text/html; charset=UTF-8');
	// echo '<pre>'; print_r($attachments); echo '</pre>';
	wp_mail( 'yourmail@domain.com', $subject, $message, $headers, $attachments);
	
	// $message2 = '<p>Guten Tag ' . get_field("nxt_contact_first_name", $post_id) . ',</p>
	// <p>diese E-Mail wurde automatisch versendet. Vielen Dank für Ihre Nachricht.</p>';
}
add_action('acf/save_post', 'nxt_send_message_email');

/* Polylang Integration */
add_action('init', function () {
	if(function_exists('pll_the_languages')) {
		foreach (acf_get_field_groups() as $group) {
			$fields = acf_get_fields($group['ID']);
			if (is_array($fields) && count($fields)) {
				foreach ($fields as &$field) {
					pll_register_string('form_field_group'.$group['ID'].'_label_'.$field['name'], $field['label'], 'acf_form_fields');
					pll_register_string('form_field_group'.$group['ID'].'_placeholder_'.$field['name'], $field['placeholder'], 'acf_form_fields');
					pll_register_string('form_field_group'.$group['ID'].'_instructions_'.$field['name'], $field['instructions'], 'acf_form_fields');
					pll_register_string('form_field_group'.$group['ID'].'_message_'.$field['name'], $field['message'], 'acf_form_fields');
					if($field['choices'] != '') {
						foreach($field['choices'] as $single_choice_field => $scf_value) {
							pll_register_string('form_field_group'.$group['ID'].'_field_choice_'.$single_choice_field, $scf_value, 'acf_form_fields');
						}
					}
				}
			}
		}
	}
	// pll_register_string('Retreats', 'Retreats', 'acf_form_fields');
});

add_filter('acf/prepare_field', function ($field) {
	if ( (!is_admin()) && function_exists('pll__') ) {
		// print_r($field);
		$field['label'] = pll__($field['label']);
		$field['placeholder'] = pll__($field['placeholder']);
		$field['instructions'] = pll__($field['instructions']);
		$field['message'] = pll__($field['message']);
		$output_choices = [];
		foreach((array) $field['choices'] as $single_choice_field => $scf_value) {
			$output_choices[$single_choice_field] = pll__($scf_value);
		}
		$field['choices'] = null;
		$field['choices'] = $output_choices;
	}
	return $field;
}, 10, 1);

function filter_acf_form($field_key) {
	add_filter('acf/prepare_field/key=' . $field_key, function() { return false; });
}

function my_admin_menu() {
	$page_hook_suffix = add_submenu_page('edit.php?post_type=message',
		__( 'Shortcode Forms', 'nxt-acf-forms' ),
		__( 'Shortcode Forms', 'nxt-acf-forms' ),
		'manage_options',
		'nxt-shortcode-forms',
		'nxt_admin_page_contents',
		0,
		10
	);
	add_action( "admin_print_scripts-{$page_hook_suffix}", 'nxt_form_script_enqueue');
}
add_action( 'admin_menu', 'my_admin_menu' );

function nxt_admin_page_contents() {
	?>
		<div class="jump_around_jump_up_jump_up_and_get_down">
			<h1>
				<?php esc_html_e( 'Form Builder', 'nxt-acf-forms' ); ?>
			</h1>
			<div>
				<div class="nxt_form_control">
					<input type="checkbox" name="nxt_toggle_all" id="nxt_toggle_all" onchange="nxt_toggle_all_fields()"> <label for="nxt_toggle_all">Alle Felder einschließen</label>
					<form action="<?php echo site_url() . '/wp-admin/admin-ajax.php';?>" method="POST" id="shortcode_generator_form" class="">
						<?php
						wp_nonce_field('shortcode_form_submission');
						echo '<input type="hidden" value="true" name="form_builder_button" />';
						$groups = acf_get_field_groups(array('post_type' => 'message'));
						$fields_in_cpt = acf_get_fields_by_id($groups[0]['ID']);
						?> <div class="checkbox-container"> <?php
						foreach($fields_in_cpt as $single_field_group) { ?>
							<?php // print_r($single_field_group); ?>
							<div class="single-field-checkbox-wrapper"><input type="checkbox" class="nxt_field_element" name="<?php echo 'nxt-field-id-' . $single_field_group['ID'] ?>" id="<?php echo 'nxt-field-id-' . $single_field_group['ID']; ?>" onchange="nxt_form_shortcode_generator()" /> <label for="<?php echo 'nxt-field-id-' . $single_field_group['ID']; ?>"><?php echo $single_field_group['label']; ?></label></div>
						<?php } ?>
						</div> <!-- .checkbox-container -->
						<div class="request_taxonomies">
							<?php $message_categories = get_terms([
								'taxonomy' => 'message-category',
								'hide_empty' => false,
							]);
							// print_r($message_categories);
							if($message_categories) {
								?> <h4 class="category_header"><?php esc_html_e('Please select the category', 'nxt-acf-forms' ); ?></h4>
								<select name="tax_select" class="tax_select" onchange="nxt_form_shortcode_generator()"> <?php
								foreach($message_categories as $single_message_category) { ?>
									<option value="<?php echo $single_message_category->term_id; ?>"  /><?php echo $single_message_category->name; ?></option>
								<?php }
								?> </select>
							<?php } ?>
						</div>
						<div class="request_url">
							<h4 class="url_header"><?php esc_html_e('Please specify the URL of the thank you page', 'nxt-acf-forms' ); ?></h4>
							<input name="target_url" class="target_url" onchange="nxt_form_shortcode_generator()" placeholder="/danke" />
						</div>
						<input type="hidden" name="action" value="shortcode_pastebin">
						<?php /* submit_button('Formular-Code erstellen', 'primary', 'nxt_form_submit'); */ ?>
					</form>
				</div> <!-- .nxt_form_control -->
				<div class="form-output">
					<h4><?php esc_html_e('Copy this form code', 'nxt-acf-forms' ); ?></h4>
					<div id="shortcode_pastebin_container" class="shortcode_pastebin_container"></div>
				</div>
				<div class="plugin-extensions">
					<?php
						echo '<hr /><h4>' . __("Export message", "nxt-acf-forms" ) . '</h4><form action="/wp-admin/edit.php?post_type=message&page=nxt-shortcode-forms" method="post">';
							// this is a WordPress security feature - see: https://codex.wordpress.org/WordPress_Nonces
							wp_nonce_field('submission_export');
							echo '<input type="hidden" value="true" name="anfragen_export_clicked" />';
							echo '<input type="text" name="nxt_export_post_id" />';
							submit_button(__('Export message', 'nxt-acf-forms'));
						echo '</form>';
					?>
				</div> <!-- .plugin-extensions -->
			</div>
		</div> <!-- .jump... ->
	<?php
}

function nxt_form_shortcode_output() {
	$return_string = '';
	if (isset($_POST['form_builder_button']) && check_admin_referer('shortcode_form_submission')) {
		// the button has been pressed AND we've passed the security check
		$return_string = '[nxt_anmeldung';
		$fields_string = '';
		$participants_fields_string = '';
		// print_r($_POST);
		foreach($_POST as $single_post_key => $post_value) {
			if(substr($single_post_key, 0, 13) == 'nxt-field-id-') {
				// $return_string .= $single_post_key . ' ';
				// $fields_string .= filter_var($single_post_key, FILTER_SANITIZE_NUMBER_INT); 
				// $fields_string .= substr($single_post_key, -4);
				// $fields_string .= ',';
				$fields_string .= substr($single_post_key, 13 - strlen($single_post_key)) . ',';
			} elseif (substr($single_post_key, 0, 19) == 'nxt-participant-id-') {
				$participants_fields_string .= substr($single_post_key, 19 - strlen($single_post_key)) . ',';
			}
		}
		$fields_string = substr($fields_string, 0, -1);
		$participants_fields_string = substr($participants_fields_string, 0, -1);
		if($fields_string != '') {
			$return_string .= " fields='" . $fields_string . "'";
		}
		if($participants_fields_string != '') {
			$return_string .= " participants_fields='" . $participants_fields_string . "'";
		}
		if(isset($_POST['tax_select'])) {
			$return_string .= " cat='" . $_POST['tax_select'] . "'";
		}
		if(isset($_POST['target_url']) && $_POST['target_url'] != '') {
			$return_string .= " url='" . $_POST['target_url'] . "'";
		}
		$return_string .= "]";
		echo $return_string;
		die();
	} else return;
}
add_action('wp_ajax_shortcode_pastebin' , 'nxt_form_shortcode_output');
add_action('wp_ajax_nopriv_shortcode_pastebin','nxt_form_shortcode_output');

function acf_form_insertion() {
	acf_form_head();
}
add_action('wp_head', 'acf_form_insertion');

// Initialize all the shortcodes defined above after WordPress is done initializing
add_action('init', 'nxt_acf_forms_add_custom_shortcodes');
function nxt_acf_forms_add_custom_shortcodes() {
	add_shortcode('nxt_anmeldung', 'nxt_contact_form');
}

function nxt_acf_forms_plugin_init() {
	// Register our script.
	wp_register_script( 'nxt_acf_forms_script', plugins_url( '/js/nxt_form_scripts.js', __FILE__ ), [], '1.0', true);
	// Register our style
	wp_register_style('nxt_acf_forms_style', plugins_url( '/css/nxt-acf-forms-styles.css', __FILE__ ));
}
add_action( 'admin_init', 'nxt_acf_forms_plugin_init' );

/**
 * Enqueue registered script in the admin area but only on our page.
 */
function nxt_form_script_enqueue() {
	// Link our already registered script to a page.
	wp_enqueue_script('nxt_acf_forms_script' );
	wp_enqueue_style( 'nxt_acf_forms_style' );
}

function save_array_to_csv($output_array, $filename = "export.csv", $post_id = 0, $delimiter=";") {
	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename="'.$filename.'";');
	// open the "output" stream
	// see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
	$f = fopen('php://output', 'w');

	foreach ($output_array as $line) {
		fputcsv($f, $line, $delimiter);
	}
	fclose($f);
	exit;
}

function create_file_in_output($output_array, $filename = "export.csv", $delimiter = ";") {
	// $f = plugin_dir_path( __FILE__ ) . '/output/'.$filename; 
	$upload_directory = wp_upload_dir();
	$upload_path = $upload_directory['basedir'];
	$f = $upload_path.'/csv-export/'.$filename;
	// $open = fopen( $f, "a" ); 
	$file = fopen($f, 'w');
	foreach ($output_array as $line) {
		fputcsv($file, $line, $delimiter);
	}
	fclose($file);
	// exit;
}

function nxt_message_export() {
	// Check whether the button has been pressed AND also check the nonce
	if (!(isset($_POST['anfragen_export_clicked']) && check_admin_referer('submission_export'))) { return; }
	$nxt_export_post_id = ($_POST['nxt_export_post_id'] > 0) ? $_POST['nxt_export_post_id'] : 8669;
	create_file_in_output(cpt_fields_to_array($nxt_export_post_id), $nxt_export_post_id . "-anfrage.csv");
	save_array_to_csv(cpt_fields_to_array($nxt_export_post_id), $nxt_export_post_id . "-anfrage.csv");

	/* // show participants info
	$nxt_participants = get_field('nxt_participants', $nxt_export_post_id);
	echo '<pre>'; print_r($nxt_participants); echo '</pre>'; */

	// create_file_in_output(cpt_fields_to_array($nxt_export_post_id), $nxt_export_post_id . "-anfrage.csv");
	/* // nxt debugging: Check array of all custom fields
	$groups = acf_get_field_groups(array('post_type' => 'message'));
	$fields_in_cpt = acf_get_fields_by_id($groups[0]['ID']);
	echo '<pre>'; print_r($fields_in_cpt); echo '</pre>';
	*/
	// nxt debugging: check if category connection is working 
	// wp_set_object_terms(8625, (int)get_field('cf_category', 8625), 'message-category');
	
	/* debugging field list */ /*
	$output_fields = get_fields($nxt_export_post_id);
	// $output_fields = get_field_objects( $nxt_export_post_id);
	// echo '<pre>'; print_r($output_fields); echo '</pre>';
	$file_path = get_attached_file($output_fields['nxt_participants'][0]['nxt_part_certificate_upload']);
	echo '<pre>'; print_r($file_path); echo '</pre>';
	/*
	foreach($output_fields as $single_field_group) {
		if($single_field_group['type'] == 'repeater') {
			echo '<pre>'; print_r($single_field_group); echo '</pre>';
			echo '<p><strong>' . $single_field_group["label"] . '</strong>:</p>';
			foreach($single_field_group['sub_fields'] as $sub_field) {
				echo '<strong>' . $sub_field["label"] . '</strong>: ' . $sub_field["value"] . '<br />';
			}
		}
	} */
	
	/* debugging participant output
	$nxt_participants = get_field('nxt_participants', $nxt_export_post_id);
	echo '<pre>'; print_r($nxt_participants); echo '</pre>'; */
}
add_action('admin_init','nxt_message_export');

function cpt_fields_to_array($post_id) {
	if($post_id < 1) return;
	// get all custom fields for our custom post type 'message' (regardless of whether or not a value is present for the current post)
	$groups = acf_get_field_groups(['post_type' => 'message']);
	$fields_in_cpt = acf_get_fields_by_id($groups[0]['ID']);
	// prepare our 2-dimensional output csv
	/* echo '<pre>';
	print_r($fields_in_cpt);
	echo '</pre>'; */
	$output_csv = [];
	$i = 0;
	$p = 0;
	$nxt_participants = get_field('nxt_participants', $post_id);
	foreach($nxt_participants as $single_participant) {
		if( $fields_in_cpt ) {
			foreach($fields_in_cpt as $single_field_group) {
				if($single_field_group['type'] == 'repeater') {
					foreach($single_field_group['sub_fields'] as $sub_field) {
						if($p == 0) {
							$output_csv[0][$i] = $sub_field['name'];
						}
						$output_csv[$p+1][$i++] = $single_participant[$sub_field['name']];
					}
				} else {
					if($p == 0) {
						$output_csv[0][$i] = $single_field_group['name']; 
					}
					if($single_field_group['name'] == 'cf_category') {
						$term = get_term((int)get_field('cf_category', $post_id));
						$output_csv[$p+1][$i++] = $term->name;
					} elseif($single_field_group['name'] == 'nxt_location') {
						$term = get_term((int)get_field('nxt_location', $post_id));
						$output_csv[$p+1][$i++] = $term->name;
					} else {
						$output_csv[$p+1][$i++] = get_field($single_field_group['name'], $post_id);
					}
				}
			}
		}
		$p++;
	}
	return $output_csv;	
}
