<?php
/**
 * @package Contact Forms with ACF by nexTab
 * @version 1.0
 */
/*
Plugin Name: Contact Forms based on Advanced Custom Fields (ACF) by nexTab
Plugin URI: https://nextab.de/
Description: This plugin helps you to receive messages by setting up a custom post type. It also sends a notification via e-mail, so it effectively works like a contact form plugin. It requires Advanced Custom Fields to work.
Author: nexTab - Oliver Gehrmann
Version: 1.0
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
			'name'							=> __( 'Messages', 'nxt-acf-forms' ),
			'singular_name'				=> __( 'Message', 'nxt-acf-forms' ),
			'all_items'						=> __( 'All Messages', 'nxt-acf-forms' ),
			'archives'							=> __( 'Message Archives', 'nxt-acf-forms' ),
			'attributes'						=> __( 'Message Attributes', 'nxt-acf-forms' ),
			'insert_into_item'				=> __( 'Insert into Message', 'nxt-acf-forms' ),
			'uploaded_to_this_item'		=> __( 'Uploaded to this Message', 'nxt-acf-forms' ),
			'featured_image'				=> _x( 'Featured Image', 'message', 'nxt-acf-forms' ),
			'set_featured_image'			=> _x( 'Set featured image', 'message', 'nxt-acf-forms' ),
			'remove_featured_image'	=> _x( 'Remove featured image', 'message', 'nxt-acf-forms' ),
			'use_featured_image'			=> _x( 'Use as featured image', 'message', 'nxt-acf-forms' ),
			'filter_items_list'				=> __( 'Filter Messages list', 'nxt-acf-forms' ),
			'items_list_navigation'		=> __( 'Messages list navigation', 'nxt-acf-forms' ),
			'items_list'						=> __( 'Messages list', 'nxt-acf-forms' ),
			'new_item'						=> __( 'New Message', 'nxt-acf-forms' ),
			'add_new'						=> __( 'Add New', 'nxt-acf-forms' ),
			'add_new_item'				=> __( 'Add New Message', 'nxt-acf-forms' ),
			'edit_item'						=> __( 'Edit Message', 'nxt-acf-forms' ),
			'view_item'						=> __( 'View Message', 'nxt-acf-forms' ),
			'view_items'						=> __( 'View Messages', 'nxt-acf-forms' ),
			'search_items'					=> __( 'Search Messages', 'nxt-acf-forms' ),
			'not_found'						=> __( 'No Messages found', 'nxt-acf-forms' ),
			'not_found_in_trash'			=> __( 'No Messages found in trash', 'nxt-acf-forms' ),
			'parent_item_colon'			=> __( 'Parent Message:', 'nxt-acf-forms' ),
			'menu_name'					=> __( 'Messages', 'nxt-acf-forms' ),
		),
		'public'				=> false,
		'show_ui'				=> true,
		'hierarchical'		=> false,
		'supports'			=> array( 'title' ),
		'has_archive'		=> false,
		'rewrite'				=> false,
		'query_var'			=> false,
		'menu_icon'			=> 'dashicons-testimonial',
		'show_in_rest'		=> false,
		) 
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
		10 => sprintf( __( 'Message draft updated. <a target="_blank" href="%s">Preview mecipe</a>', 'nxt-acf-forms' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);
	return $messages;
}
add_filter( 'post_updated_messages', 'nxt_msg_update' );

/**
 * Registers the 'message_category' taxonomy,
 * for use with 'message'.
 */
function nxt_message_cat_setup() {
	register_taxonomy( 'message_category', array( 'message' ), array(
		'hierarchical'	=> true,
		'public'			=> false,
		'show_ui'			=> true,
		'query_var'		=> false,
		'rewrite'			=> true,
		'capabilities'		=> array(
			'manage_terms'	=> 'edit_posts',
			'edit_terms'			=> 'edit_posts',
			'delete_terms'		=> 'edit_posts',
			'assign_terms'		=> 'edit_posts',
		),
		'labels'			=> array(
			'name'									=> __( 'Message categories', 'nxt-acf-forms' ),
			'singular_name'						=> _x( 'Message category', 'taxonomy general name', 'nxt-acf-forms' ),
			'search_items'							=> __( 'Search Message categories', 'nxt-acf-forms' ),
			'popular_items'							=> __( 'Popular Message categories', 'nxt-acf-forms' ),
			'all_items'								=> __( 'All Message categories', 'nxt-acf-forms' ),
			'parent_item'							=> __( 'Parent Message category', 'nxt-acf-forms' ),
			'parent_item_colon'					=> __( 'Parent Message category:', 'nxt-acf-forms' ),
			'edit_item'								=> __( 'Edit Message category', 'nxt-acf-forms' ),
			'update_item'							=> __( 'Update Message category', 'nxt-acf-forms' ),
			'view_item'								=> __( 'View Message category', 'nxt-acf-forms' ),
			'add_new_item'						=> __( 'Add New Message category', 'nxt-acf-forms' ),
			'new_item_name'						=> __( 'New Message category', 'nxt-acf-forms' ),
			'separate_items_with_commas'	=> __( 'Separate Message categories with commas', 'nxt-acf-forms' ),
			'add_or_remove_items'				=> __( 'Add or remove Message categories', 'nxt-acf-forms' ),
			'choose_from_most_used'			=> __( 'Choose from the most used Message categories', 'nxt-acf-forms' ),
			'not_found'								=> __( 'No Message categories found.', 'nxt-acf-forms' ),
			'no_terms'								=> __( 'No Message categories', 'nxt-acf-forms' ),
			'menu_name'							=> __( 'Message categories', 'nxt-acf-forms' ),
			'items_list_navigation'				=> __( 'Message categories list navigation', 'nxt-acf-forms' ),
			'items_list'								=> __( 'Message categories list', 'nxt-acf-forms' ),
			'most_used'								=> _x( 'Most Used', 'message_category', 'nxt-acf-forms' ),
			'back_to_items'							=> __( '&larr; Back to Message categories', 'nxt-acf-forms' ),
		),
		'show_in_rest'	=> false,
		)
	);
}
add_action( 'init', 'nxt_message_cat_setup' );

/**
 * Sets the post updated messages for the 'message_cateory' taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the 'message_category' taxonomy.
 */
function nxt_msg_cat_updated( $messages ) {
	$messages['message_category'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Message category added.', 'nxt-acf-forms' ),
		2 => __( 'Message category deleted.', 'nxt-acf-forms' ),
		3 => __( 'Message category updated.', 'nxt-acf-forms' ),
		4 => __( 'Message category not added.', 'nxt-acf-forms' ),
		5 => __( 'Message category not updated.', 'nxt-acf-forms' ),
		6 => __( 'Message categories deleted.', 'nxt-acf-forms' ),
	);
	return $messages;
}
add_filter( 'term_updated_messages', 'nxt_msg_cat_updated' );

/* Add filters to backend for better sorting */
function nxt_more_columns($columns) {
	// print_r($columns);
	$columns = array(
		'cb'						=> '<input type="checkbox" />',
		'title' 						=> __('Title', 'nxt-acf-forms'),
		'message_category'	=> __('Category', 'nxt-acf-forms'),
		'cf_email'					=> __('E-Mail', 'nxt-acf-forms'),
		'cf_tel'						=> __('Telephone', 'nxt-acf-forms'),
		'date'						=> __('Date', 'nxt-acf-forms'),
	);
	// $columns['message_category'] = 'Category';
	return $columns;
}
add_filter('manage_edit-message_columns', 'nxt_more_columns');

function nxt_post_custom_columns($column) {
	global $post;
	$post_id = get_the_ID();
	// $custom = get_post_custom();
	/* 
	$fields = get_field_objects( $post_id );
	// print_r($fields);
	if( $fields ) {
		foreach( $fields as $single_field_group ) {
			if( $single_field_group['type'] == 'taxonomy' ) {
				$nxt_field_tax = $single_field_group['value']->name;
				$nxt_field_slug = $single_field_group['value']->slug;
				$nxt_field_tax_id = $single_field_group['value']->term_id;
				// break;
			}
			if ($single_field_group['label'] == 'E-Mail') $nxt_field_email = $single_field_group['value'];
			if ($single_field_group['name'] == 'cf_tel') $nxt_field_tel = $single_field_group['value'];
		}
	} */
	$nxt_field_email = get_field('cf_email', $post_id);
	$nxt_field_tel = get_field('cf_tel', $post_id);
	$nxt_field_tax = get_field('cf_interest', $post_id)->name;
	$nxt_field_slug = get_field('cf_interest', $post_id)->slug;

	switch ($column) {
		case "message_category":
			echo '<a href="' . admin_url( 'edit.php?post_type=message&taxonomy=message_category&term=' . urlencode( $nxt_field_slug ) ) . '">' . $nxt_field_tax . '</a>';
			// echo $nxt_field_tax;
			break;
		case "cf_email":
			echo '<a href="' . admin_url( 'edit.php?post_type=message&cf_email=' . urlencode( $nxt_field_email ) ) . '">' . $nxt_field_email . '</a>';
			// echo $nxt_field_email;
			break;
		case "cf_tel":
			echo '<a href="' . admin_url( 'edit.php?post_type=message&cf_tel=' . urlencode( $nxt_field_tel ) ) . '">' . $nxt_field_tel . '</a>';
			// echo $nxt_field_tel;
			break;
		}
}
add_action ('manage_message_posts_custom_column','nxt_post_custom_columns');

// Make column headers clickable to initiate sort
function nxt_sort_columns( $columns ) {
	// $columns['message_category'] = 'message_category';
	$columns['cf_email'] = 'cf_email';
	$columns['cf_tel'] = 'cf_tel';
	$columns['message_category'] = 'cf_interest';
	return $columns;
}
add_filter( 'manage_edit-message_sortable_columns', 'nxt_sort_columns' );

// Fix sorting logic
function nxt_custom_query_variables( $vars ) {
	$vars[] .= 'cf_email';
	$vars[] .= 'cf_tel';
	$vars[] .= 'cf_interest';
	return $vars;
}
add_filter( 'query_vars', 'nxt_custom_query_variables' );

function nxt_alter_query( $query ) {
	if ( !is_admin() || 'message' != $query->query['post_type'] )
		return;

	if ( $query->query_vars['cf_email'] ) {
		$query->set( 'meta_key', 'cf_email' );
		$query->set( 'meta_value', $query->query_vars['cf_email'] );
	}

	if ( $query->query_vars['cf_tel'] ) {
		$query->set( 'meta_key', 'cf_tel' );
		$query->set( 'meta_value', $query->query_vars['cf_tel'] );
	}

	if ( $query->query_vars['cf_interest'] ) {
		$query->set( 'meta_key', 'cf_interest' );
		$query->set( 'meta_value', $query->query_vars['cf_interest'] );
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
	if( 'cf_interest' == $orderby ) {
		$query->set('meta_key','cf_interest');
		$query->set('orderby','meta_value');
	}
}
add_action( 'pre_get_posts', 'nxt_alter_query' );

/* Create a contact form via shortcode */
function nxt_contact_form() {
	$defaults = shortcode_atts( array(
		'id' => 'new-message',
		'post_id' => 'new_post',
		'post_type' => 'message',
		'post_status' => 'publish',
	), $atts );

	$groups = acf_get_field_groups(array('post_type' => $defaults['post_type']));
	// print_r($groups);
	$fields_in_cpt = acf_get_fields($groups);
	// print_r($fields_in_cpt);
	$form_fields = array();
	foreach($fields_in_cpt as $single_field_group) {
		$form_fields[] = $single_field_group['key'];
	}
	// print_r($form_fields);
	ob_start();
	acf_form(array(
		'id'					=> $defaults['id'],
		'honeypot'		=> true,
		'post_id'			=> $defaults['post_id'],
		'new_post'		=> array(
			'post_type'	=> $defaults['post_type'],
			'post_status'	=> $defaults['post_status'],
		),
		'post_title'		=> false,
		'post_content'  	=> false,
		'uploader'      	=> 'wp',
		// 'return'			=> home_url('thank-you'),
		'fields'			=> $form_fields,
		'submit_value'		=> __('Send Message', 'nxt-acf-forms'),
		)
	);
	$html = ob_get_contents();
	ob_end_clean();
	return $html;
}
add_shortcode('nxt_cf', 'nxt_contact_form');

/* The following function sends an e-mail to all administrators when a new post with the post type 'message' is created on the front end (via the form we created above) and also updates the title of the post to something meaningful. */
function nxt_send_message_email( $post_id ) {
	if( ( get_post_type($post_id) !== 'message' && get_post_status($post_id) == 'draft' ) || ( is_admin() ) ) {
		return;
	}
	date_default_timezone_set(get_option('timezone_string'));
	$new_post_title = date('Y-m-d @ H:i') . ' - ' . get_field('cf_first_name', $post_id) . ' ' . get_field('cf_last_name', $post_id);
	$post_update = array(
		'ID'				=> $post_id,
		'post_title'	=> $new_post_title,
	);
	wp_update_post( $post_update );
	/* Create Message Body by reading out all fields */
	$message 	= __('<p>Hi,</p><p>this message has just been submitted on your website:</p><p>', 'nxt-acf-forms');
	$fields = get_field_objects( $post_id );
	if( $fields ) {
		foreach( $fields as $single_field_group ) {
			if( $single_field_group['type'] == 'taxonomy' ) {
				$message .= '</p><p><strong>' . $single_field_group['label'] . "</strong><br />" . $single_field_group['value']->name . '</p>';
				// <p>Und jetzt für die $single_field_group: ' . print_r($single_field_group, true) . '</p><p>';
			} elseif ($single_field_group['name'] == '_validate_email') {
			} else {
				$message .= '<strong>' . $single_field_group['label'] . "</strong>: " . $single_field_group['value'] . "<br />";
			}
		}
	}

	$message .= '</p><p>' . __('You can also find the message here: ', 'nxt-acf-forms') . get_admin_url() . 'post.php?post=' . $post_id . '&action=edit' . '</p><p>' . __('That is all. Make sure to respond soon!</p>', 'nxt-acf-forms');
	$subject 	= __('New Message - ' . $new_post_title, 'nxt-acf-forms');
	$headers = array('Content-Type: text/html; charset=UTF-8');

	$administrators = get_users(array(
		'role'	=> 'administrator'
	));
	foreach ($administrators as $administrator) {
		wp_mail( $administrator->data->user_email, $subject, $message, $headers );
	}
}
add_action('acf/save_post', 'nxt_send_message_email');