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
	add_action( 'save_post', 'save_geotag_info_meta');
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