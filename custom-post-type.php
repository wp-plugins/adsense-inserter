<?php

/*
*Instruction - if you are using this as a template for a plugin, change the class name, the call to create an object from this class *at the bottom, and modify the private variables to meet your needs.
*/

class AdsenseInserterCustomPostType{

private $post_type = 'adsenseinserter';
private $post_label = 'Adsense Inserter';
private $prefix = '_adsense_inserter_';
function __construct() {
	
	add_filter( 'cmb_meta_boxes', array(&$this,'metaboxes' ));
	add_action( 'init', array(&$this,'initialize_meta_boxes'), 9999 );
	add_action("init", array(&$this,"create_post_type"));
	add_action( 'init', array(&$this, 'adsense_inserter_register_shortcodes'));
	register_activation_hook( __FILE__, array(&$this,'activate' ));
}

function create_post_type(){
	register_post_type($this->post_type, array(
	         'label' => _x($this->post_label, $this->post_type.' label'), 
	         'singular_label' => _x('All '.$this->post_label, $this->post_type.' singular label'), 
	         'public' => true, // These will be public
	         'show_ui' => true, // Show the UI in admin panel
	         '_builtin' => false, // This is a custom post type, not a built in post type
	         '_edit_link' => 'post.php?post=%d',
	         'capability_type' => 'page',
	         'hierarchical' => false,
	         'rewrite' => array("slug" => $this->post_type), // This is for the permalinks
	         'query_var' => $this->post_type, // This goes to the WP_Query schema
	         //'supports' =>array('title', 'editor', 'custom-fields', 'revisions', 'excerpt'),
	         'supports' =>array('title', 'author'),
	         'add_new' => _x('Add New', 'Event')
	         ));
}


/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function metaboxes( array $meta_boxes ) {
	
	// Start with an underscore to hide fields from custom fields list
	//$prefix = '_adsense_inserter_';
	

	$meta_boxes[] = array(
		'id'         => 'adsense_metabox',
		'title'      => 'Ad',
		'pages'      => array( $this->post_type ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			
			array(
				'name' => 'Adsense Code',
				'desc' => 'Place the code for one adsense ad you would like to use on your site.',
				'id'   => $this->prefix . 'adsense_code',
				'type' => 'textarea_code',
			),

		),
	);

	

	// Add other metaboxes as needed

	return $meta_boxes;
}


function adsense_inserter_shortcode($atts){
		extract( shortcode_atts( array(
			'id' => '',
		), $atts ) );
		$code = get_post_meta( $id, $this->prefix . 'adsense_code', true );
		ob_start();
		echo $code;
		return ob_get_clean();
}



function adsense_inserter_register_shortcodes(){
		add_shortcode( 'adsense_inserter', array(&$this,'adsense_inserter_shortcode' ));
	}


function activate() {
	// register taxonomies/post types here
	$this->create_post_type();
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}


/*
 * Initialize the metabox class.
 */
 
function initialize_meta_boxes() {

	if ( ! class_exists( 'cmb_Meta_Box' ) )
		require_once 'lib/metabox/init.php';

}


}

new AdsenseInserterCustomPostType();


?>