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
add_action( 'wp_enqueue_scripts', 'enqueue_maps' );

function enqueue_maps()
{
	wp_enqueue_script( 'gmaps', "https://maps.googleapis.com/maps/api/js?sensor=false&key=".get_option( 'wp_geotag_gmaps_api_key' ));
}

function wp_geotag_map() {
	return '<div id="wp-geotag-map"></div>';
}
add_shortcode( 'wp_geotag_map', 'wp_geotag_map' );

function geotag_meta_boxes_setup() {
	add_action( 'add_meta_boxes', 'geotag_add_post_meta_boxes' );
	add_action( 'save_post', 'save_geotag_info_meta', 10, 2);
}

function geotag_add_post_meta_boxes() {
	$post_types = array('post','page');
	foreach ($post_types as $post_type)
	{
		add_meta_box(
		'wp_geotag',      // Unique ID
		esc_html__( 'Geotag', 'example' ),    // Title
		'geotag_post_class_meta_box',   // Callback function
		$post_type,         // Admin page (or post type)
		'side',         // Context
		'default'         // Priority
		);
	}
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

	  $new_meta_value = ( isset( $_POST[$field] ) ?  $_POST[$field] : '' );

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


// create custom plugin settings menu
add_action('admin_menu', 'wp_geotag_create_menu');

function wp_geotag_create_menu() {

	add_options_page('WP Geotag Plugin Settings', 'WP Geotag', 'administrator', __FILE__, 'wp_geotag_settings_page');

	add_action( 'admin_init', 'register_mysettings' );
}


function register_mysettings() {
	register_setting( 'wp-geotag-settings-group', 'wp_geotag_gmaps_api_key' );
}

function wp_geotag_settings_page() {
?>
<div class="wrap">
<h2>WP Geotag Settings</h2>

<form method="post" action="options.php">
 <?php settings_fields( 'wp-geotag-settings-group' ); ?>
    <?php do_settings_sections( 'wp-geotag-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Google Maps API Key</th>
        <td><input type="text" name="wp_geotag_gmaps_api_key" value="<?php echo esc_attr( get_option('wp_geotag_gmaps_api_key') ); ?>" /></td>
        </tr>
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>