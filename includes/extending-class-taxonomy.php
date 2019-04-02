<?php
/**
 * Register taxonomy for campaigns.
 *
 * @package dude-really-simple-ads
 */
class DRSA_Taxonomy extends Dude_Really_Simple_Ads {

	public function __construct() {
		parent::__construct();
	} // end __construct

	/**
	 * Add hooks that actually makes everything.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	public static function run() {
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_filter( 'manage_edit-drsa_campaigns_columns', array( $this, 'list_columns' ) );
		add_action( 'manage_drsa_campaigns_custom_column', array( $this, 'list_columns_content' ), 10, 3 );
		add_action( 'pre_get_posts', array( $this, 'kill_taxonomy_archive' ) );
	} // end run

	/**
	 * Register taxonomy for drsa_campaigns.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	public static function register_taxonomy() {
		register_taxonomy( 'drsa_campaigns', array( 'drsa_ad' ), self::arguments() );
	} // end register_taxonomy

	/**
	 * Get labels for drsa_campaigns taxonomy.
	 *
	 * @return  array 		translated and filtered labels
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	private function labels() {
		return apply_filters(
			'drsa_taxonomy_labels', array(
				'name'              => __( 'Kampanjat', 'dude-really-simple-ads' ),
				'singular_name'     => __( 'Kampanja', 'dude-really-simple-ads' ),
				'search_items'      => __( 'Etsi kampanjoita', 'dude-really-simple-ads' ),
				'all_items'         => __( 'Kaikki kampanjat', 'dude-really-simple-ads' ),
				'parent_item'       => __( 'Yläkampanja', 'dude-really-simple-ads' ),
				'parent_item_colon' => __( 'Yläkampanja:', 'dude-really-simple-ads' ),
				'edit_item'         => __( 'Muokkaa kampanjaa', 'dude-really-simple-ads' ),
				'update_item'       => __( 'Päivitä kampanjaa', 'dude-really-simple-ads' ),
				'add_new_item'      => __( 'Lisää uusi kampanja', 'dude-really-simple-ads' ),
				'new_item_name'     => __( 'Uuden kampanjan nimi', 'dude-really-simple-ads' ),
				'menu_name'         => __( 'Kampanjat', 'dude-really-simple-ads' ),
			)
		);
	} // end labels

	/**
	 * Get arguments for drsa_campaigns taxonomy.
	 *
	 * @return  array 			filtered arguments
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	private static function arguments() {
		return apply_filters(
			'drsa_taxonomy_args', array(
				'hierarchical'      => true,
				'labels'            => self::labels(),
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => false,
			)
		);
	} // end arguments

	/**
	 * Customize drsa_campaigns taxonomy listing columns.
	 *
	 * @param   array $columns wp default columns.
	 * @return  array             our custom columns
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	public static function list_columns( $columns ) {

		/**
		 * Remove some unwanted default columns
		 */
	  unset( $columns['slug'] );
		unset( $columns['description'] );
		unset( $columns['posts'] );

		/**
		 * Merge our custom columns with rest of default ones, and re-set column for
		 * posts count in new position.
		 */
		return array_merge( $columns,
	  	array(
				'drsa_timing_start'	=> __( 'Näyttö alkaa', 'dude-really-simple-ads' ),
				'drsa_timing_end'		=> __( 'Näyttö loppuu', 'dude-really-simple-ads' ),
				'drsa_placement'		=> __( 'Mainospaikka', 'dude-really-simple-ads' ),
				'posts'							=> __( 'lkm', 'dude-really-simple-ads' ),
			)
		);
	} // end list_columns

	/**
	 * Show content on our custom drsa_campaigns listing columns.
	 *
	 * @param   string  $content content for column.
	 * @param   string  $column  current listing column.
	 * @param   integer $term_id current list item id.
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	public static function list_columns_content( $content, $column, $term_id ) {
		switch ( $column ) {
			case 'drsa_timing_start':
				$date = get_term_meta( $term_id, '_drsa_ad_campaing_timing_start_date', true );

				if ( ! empty( $date ) ) {
					echo date_i18n( 'd.m.Y H:i', $date );
        }
				break;

			case 'drsa_timing_end':
				$date = get_term_meta( $term_id, '_drsa_ad_campaign_timing_end_date', true );

				if ( ! empty( $date ) ) {
					echo date_i18n( 'd.m.Y H:i', $date );
        }
				break;

			case 'drsa_placement':
				$place = get_term_meta( $term_id, '_drsa_ad_campaign_placement', true );
				$places = DRSA_Places::get_ad_placement_options( false );

				if ( ! empty( $place ) && array_key_exists( $place, $places ) ) {
					echo $places[ $place ];
        }
				break;
		}
	} // end list_columns_content

	/**
	 * Set 404 page if requested archive page for drsa_campaigns taxonomy. It's
	 * meant for internal use only, but WordPress does not provide core build
	 * way to accomblish that.
	 *
	 * @param   object $query current query.
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	public static function kill_taxonomy_archive( $query ) {
  	if ( ! is_admin() && is_tax( 'drsa_campaigns' ) ) {
			$query->set_404();
    }

		return;
	} // end kill_taxonomy_archive
} // end class
