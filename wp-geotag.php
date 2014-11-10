<?php
/**
* Plugin Name: WP Geotag
* Plugin URI: http://www.daniel-yang.com/wp-geotag
* Description: Add geotags to posts and pages
* Version: 1.0
* Author: Daniel Yang
* Author URI: http://www.daniel-yang.com
**/


add_action( 'load-post.php', 'geotag_meta_boxes_setup' );
add_action( 'load-post-new.php', 'geotag_meta_boxes_setup' );

function geotag_meta_boxes_setup() {
	add_action( 'add_meta_boxes', 'geotag_add_post_meta_boxes' );
	add_action( 'save_post', 'save_geotag_info_meta', 10, 2);
}

function geotag_add_post_meta_boxes() {
	add_meta_box(
	'wp_geotag',      // Unique ID
	esc_html__( 'Geotag', 'example' ),    // Title
	'geotag_post_class_meta_box',   // Callback function
	'post',         // Admin page (or post type)
	'side',         // Context
	'default'         // Priority
	);
}

function geotag_post_class_meta_box( $object, $box ) { ?>

<?php wp_nonce_field( basename( __FILE__ ), 'geotag_post_class_nonce' ); ?>

<p>
	<label for="geotag-latitude"><?php _e( "Latitude", 'example' ); ?></label>
	<br />
	<input class="widefat" type="text" name="geotag-latitude" id="geotag-latitude" value="<?php echo esc_attr( get_post_meta( $object->ID, 'geotag_latitude', true ) ); ?>" size="30" />
	<br />
	<label for="geotag-longitude"><?php _e( "Longitude", 'example' ); ?></label>
	<br />
	<input class="widefat" type="text" name="geotag-longitude" id="geotag-longitude" value="<?php echo esc_attr( get_post_meta( $object->ID, 'geotag_longitude', true ) ); ?>" size="30" />
</p>
<?php }

function save_geotag_info_meta( $post_id, $post ) {

  if ( !isset( $_POST['geotag_post_class_nonce'] ) || !wp_verify_nonce( $_POST['geotag_post_class_nonce'], basename( __FILE__ ) ) )
    return $post_id;

  $post_type = get_post_type_object( $post->post_type );

  $fields = array('geotag-latitude', 'geotag-longitude');
  
  foreach($fields as $field)
  {
	  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
	    return $post_id;

	  $new_meta_value = ( isset( $_POST[$field] ) ? sanitize_html_class( $_POST[$field] ) : '' );

	  $meta_key = str_replace('-','_',$field);

	  $meta_value = get_post_meta( $post_id, $meta_key, true );

	  if ( $new_meta_value && '' == $meta_value )
	    add_post_meta( $post_id, $meta_key, $new_meta_value, true );

	  elseif ( $new_meta_value && $new_meta_value != $meta_value )
	    update_post_meta( $post_id, $meta_key, $new_meta_value );

	  elseif ( '' == $new_meta_value && $meta_value )
	    delete_post_meta( $post_id, $meta_key, $meta_value );
  }
  
}