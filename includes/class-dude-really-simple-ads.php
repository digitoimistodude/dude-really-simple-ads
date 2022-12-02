<?php

/**
 * Base for the plugin.
 *
 * @package dude-really-simple-ads
 */
class Dude_Really_Simple_Ads {

	/**
	 *  Instace if this class
	 *
	 *  @var null
	 */
	private $instance = null;

	/**
	 *  Name slug of this plugin
	 *
	 *  @var string
	 */
	protected $plugin_name;

	/**
	 *  Version of this plugin
	 *
	 *  @var string
	 */
	protected $version;

	public function __construct() {
		$this->plugin_name = 'dude-really-simple-ads';
		$this->version = '1.1.5';
	} // end __construct

	/**
	 * Start the magic from here.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	public function run() {
		$this->set_hooks();
		$this->set_admin_hooks();
		$this->load_dependencies();

		$post_type = new DRSA_Post_Type();
		$post_type->run();

		$taxonomy = new DRSA_Taxonomy();
		$taxonomy->run();

		$metaboxes = new DRSA_Metaboxes();
		$metaboxes->run();
	} // end run

	/**
	 * Add front-end facing hooks.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	private function set_hooks() {
		load_plugin_textdomain( 'dude-really-simple-ads', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
		add_action( 'wp_ajax_drsa_count', array( 'DRSA_Show_Ads', 'update_statistics' ) );
		add_action( 'wp_ajax_nopriv_drsa_count', array( 'DRSA_Show_Ads', 'update_statistics' ) );
	} // end set_locale

	/**
	 * Add admin facing hooks.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	private static function set_admin_hooks() {
		if ( ! is_admin() ) {
			return;
        }

		global $pagenow;

		// These hooks are that essential that we require them in every view
		add_action( 'save_post_drsa_ad', array( 'DRSA_Admin_Hooks', 'validate_feature_image_size' ), 10, 3 );
		add_filter( 'post_row_actions', array( 'DRSA_Admin_Hooks', 'remove_quick_edit' ), 10, 2 );
    add_action( 'admin_init', [ 'DRSA_Admin_Hooks', 'init_data_table' ] );

		// These hooks are only needed when editing existing post
		if ( 'post.php' === $pagenow && isset( $_GET['post'] ) && 'drsa_ad' === get_post_type( sanitize_text_field( $_GET['post'] ) ) ) {
			add_action( 'add_meta_boxes', array( 'DRSA_Admin_Hooks', 'reorder_metaboxes' ) );
			add_action( 'admin_notices', array( 'DRSA_Admin_Hooks', 'admin_notices' ) );
			add_filter( 'admin_post_thumbnail_html', array( 'DRSA_Admin_Hooks', 'change_featured_image_links' ) );
		}

		// These hooks are only needed when editing new post
		if ( 'post-new.php' === $pagenow && isset( $_GET['post_type'] ) ) {
			add_action( 'add_meta_boxes', array( 'DRSA_Admin_Hooks', 'reorder_metaboxes' ) );
			add_filter( 'admin_post_thumbnail_html', array( 'DRSA_Admin_Hooks', 'change_featured_image_links' ) );
		}
	} // end set_admin_hooks

	/**
	 * Require files containing spesific functionalities.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	private static function load_dependencies() {
		require plugin_dir_path( dirname( __FILE__ ) ) . 'includes/extending-class-post-type.php';
		require plugin_dir_path( dirname( __FILE__ ) ) . 'includes/extending-class-taxonomy.php';
		require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/extending-class-metaboxes.php';
		require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/extending-class-admin-hooks.php';
		require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/extending-class-palces.php';
		require plugin_dir_path( dirname( __FILE__ ) ) . 'public/extending-class-show-ads.php';
	} // end load_dependencies

  public static function get_current_ad_end_mode() {
    return apply_filters( 'drsa_end_ads_by_show_count', false );
  } // end get_current_ad_edn_mode

  public static function allow_alternative_image() {
    return apply_filters( 'drsa_allow_alternative_image', false );
  } // end allow_alternative_image
} // end class
