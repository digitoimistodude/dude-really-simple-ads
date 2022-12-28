<?php
/**
 * Register post type for ads
 *
 * @package dude-really-simple-ads
 */
class DRSA_Post_Type extends Dude_Really_Simple_Ads {

	public function __construct() {
		parent::__construct();
	} // end __construct

	/**
	 * Add hooks that actually makes everything.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	public function run() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_filter( 'manage_drsa_ad_posts_columns', array( $this, 'list_columns' ) );
		add_action( 'manage_drsa_ad_posts_custom_column', array( $this, 'list_columns_content' ), 10, 2 );
	} // end run

	/**
	 * Register post type for drsa_ads.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	public static function register_post_type() {
		register_post_type( 'drsa_ad', self::arguments() );
	} // end register_post_type

	/**
	 * Get labels for drsa_ad post type.
	 *
	 * @return  array 	    translated and filtered labels
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	private static function labels() {
		return apply_filters(
			'drsa_post_type_labels', array(
				'name'               => __( 'Mainokset', 'dude-really-simple-ads' ),
				'singular_name'      => __( 'Mainos', 'dude-really-simple-ads' ),
				'menu_name'          => __( 'Mainokset', 'dude-really-simple-ads' ),
				'name_admin_bar'     => __( 'Mainos', 'dude-really-simple-ads' ),
				'add_new'            => __( 'Lisää uusi', 'dude-really-simple-ads' ),
				'add_new_item'       => __( 'Lisää uusi mainos', 'dude-really-simple-ads' ),
				'new_item'           => __( 'Uusi mainos', 'dude-really-simple-ads' ),
				'edit_item'          => __( 'Muokkaa mainosta', 'dude-really-simple-ads' ),
				'view_item'          => __( 'Tarkastele mainosta', 'dude-really-simple-ads' ),
				'all_items'          => __( 'Kaikki mainokset', 'dude-really-simple-ads' ),
				'search_items'       => __( 'Etsi mainoksia', 'dude-really-simple-ads' ),
				'parent_item_colon'  => __( 'Ylämainos:', 'dude-really-simple-ads' ),
				'not_found'          => __( 'Mainoksia ei löytynyt.', 'dude-really-simple-ads' ),
				'not_found_in_trash' => __( 'Mainoksia ei löytynyt roskista.', 'dude-really-simple-ads' ),
			)
		);
	} // end labels

	/**
	 * Get arguments for drsa_ad post type.
	 *
	 * @return  array 	filtered agruments
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	private static function arguments() {
		return apply_filters(
			'drsa_post_type_args', array(
				'labels'             => self::labels(),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => null,
				'menu_icon'          => 'dashicons-pressthis',
				'supports'           => array( 'title', 'author', 'thumbnail' ),
				'capability_type'    => apply_filters( 'drsa_ad/cpt/capability_type', 'post' ),
			)
		);
	} // end arguments

	/**
	 * Customize drsa_ad post type listing columns.
	 *
	 * @param   array $columns wp default columns.
	 * @return  array 	            our custom columns
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	public static function list_columns( $columns ) {

		/**
		 * Remove some unwanted default columns, that does not provide any useful
		 * information.
		 */
	  unset( $columns['author'] );
		unset( $columns['date'] );
		unset( $columns['taxonomy-drsa_campaigns'] );

		/**
		 * Merge our custom columns with rest of default ones, and re-set column for
		 * drsa_campaigns in new position.
		 */
		$columns = array_merge( $columns,
	  	array(
				'drsa_timing_start'				=> __( 'Näyttö alkaa', 'dude-really-simple-ads' ),
				'drsa_timing_end'					=> Dude_Really_Simple_Ads::ad_visibility_by_show_count() ? __( 'Näyttökertojen yläraja', 'dude-really-simple-ads' ) : __( 'Näyttö loppuu', 'dude-really-simple-ads' ),
				'drsa_placement'					=> __( 'Mainospaikka', 'dude-really-simple-ads' ),
        'taxonomy-drsa_campaigns' => __( 'Kampanja', 'dude-really-simple-ads' ),
				'drsa_stats'							=> __( 'Luvut', 'dude-really-simple-ads' ),
				'drsa_src'								=> __( 'Esikatselu', 'dude-really-simple-ads' ),
			)
		);

    if ( ! Dude_Really_Simple_Ads::enable_campaigns() ) {
      unset( $columns['taxonomy-drsa_campaigns'] );
    }

    return $columns;
	} // end list_columns

	/**
	 * Show content on our custom drsa_ad listing columns.
	 *
	 * @param   string  $column  current list column.
	 * @param   integer $post_id current list item id.
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	public static function list_columns_content( $column, $post_id ) {
		switch ( $column ) {
			case 'drsa_timing_start':
				$date = get_post_meta( $post_id, '_drsa_ad_timing_start_date', true );

				if ( ! empty( $date ) ) {
					echo date_i18n( 'd.m.Y H:i', $date );
        }
				break;

			case 'drsa_timing_end':
        if ( true === Dude_Really_Simple_Ads::ad_visibility_by_show_count() ) {
          $count = get_post_meta( $post_id, '_drsa_ad_timing_end_view_count', true );

          echo esc_html( empty( $count ) ? '-' : $count );
          break;
        } else {
          $date = get_post_meta( $post_id, '_drsa_ad_timing_end_date', true );

          if ( ! empty( $date ) ) {
            echo date_i18n( 'd.m.Y H:i', $date );
          }
          break;
        }

			case 'drsa_placement':
				$place = get_post_meta( $post_id, '_drsa_ad_placement', true );
				$places = DRSA_Places::get_ad_placement_options( false );

				if ( ! empty( $place ) && array_key_exists( $place, $places ) ) {
					echo $places[ $place ];
        }
				break;

			case 'drsa_stats':
				$show_count = get_post_meta( $post_id, '_drsa_campaing_show_counter', true );
				$show_count = ( $show_count ) ? $show_count : '0';
				$click_count = get_post_meta( $post_id, '_drsa_campaing_click_counter', true );
				$click_count = ( $click_count ) ? $click_count : '0';
				printf( __( '%1$s näyttöä<br />%2$s avausta', 'dude-really-simple-ads' ), $show_count, $click_count );
				break;

			case 'drsa_src':
				$src = wp_get_attachment_url( get_post_thumbnail_id() );
				$target = get_post_meta( $post_id, '_drsa_ad_target_url', true );

				if ( ! empty( $src ) && ! empty( $target ) ) :
					echo "<a href='{$target}' target='_blank'><img src='{$src}' width='150' /></a>";
				elseif ( ! empty( $src ) ) :
					echo "<img src='{$src}' width='100' />";
				endif;

				break;
		}
	} // end list_columns_content
} // end class
