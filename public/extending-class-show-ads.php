<?php
/**
 * Handle ad showing related things.
 *
 * @package dude-really-simple-ads
 */
class DRSA_Show_Ads extends Dude_Really_Simple_Ads {

	/**
	 *  Array containing information which ads are visible on the page.
	 *
	 *  @var array
	 */
	public static $visible_ads = array();

	public function __construct() {
		parent::__construct();
	} // end __construct

  public static $ads_shown = [];

	public static function get_active_ad( $place = null ) {
		if ( is_null( $place ) ) {
			return false;
    }

    $query_args = array(
      'post_type'				=> 'drsa_ad',
      'post_status'			=> 'publish',
      'posts_per_page'	=> 1,
      'no_found_rows' 	=> true,
      'meta_query'			=> array(
        'relation'		=> 'AND',
        array(
          'key'		=> '_drsa_ad_show',
        ),
        array(
          'key'		=> '_drsa_ad_placement',
          'value'	=> $place,
        ),
        array(
          'key'			=> '_drsa_ad_timing_start_date',
          'value' 	=> current_time( 'timestamp' ),
          'compare' => '<',
          'type'		=> 'NUMERIC',
        ),
      ),
    );

    $place_data = DRSA_Places::get_ad_placements()[ $place ];
    if ( isset( $place_data['multiple'] ) && true === $place_data['multiple'] ) {
      $query_args['orderby'] = 'rand meta_value_num';
      $query_args['meta_key'] = '_drsa_campaing_show_counter';
      $query_args['order'] = 'ASC';
      $query_args['post__not_in'] = isset( DRSA_Show_Ads::$ads_shown[ $place ] ) ? DRSA_Show_Ads::$ads_shown[ $place ] : [];
    } else {
      $query_args['orderby'] = 'rand';
    }

    if ( false === Dude_Really_Simple_Ads::ad_visibility_by_show_count() ) {
      $query_args['meta_query'][] = [
        'key'			=> '_drsa_ad_timing_end_date',
        'value' 	=> current_time( 'timestamp' ),
        'compare' => '>',
        'type'		=> 'NUMERIC',
      ];
    }

		$query = new WP_Query( $query_args );

    $return = [];
		if ( $query->have_posts() ) :
		  while ( $query->have_posts() ) :
				$query->the_post();
				$post_id = get_the_id();

        DRSA_Show_Ads::$ads_shown[ $place ][] = get_the_ID();

				$return = array(
					'src'			=> wp_get_attachment_url( get_post_thumbnail_id() ),
					'target'	=> get_post_meta( $post_id, '_drsa_ad_target_url', true ),
					'slug'		=> sanitize_title( get_the_title() ),
					'id'			=> $post_id,
				);
		  endwhile;
		endif;

		wp_reset_postdata();
		return empty( $return ) ? false : $return;
	} // end get_active_ad

	public static function get_campaign_ad( $campaign = null, $place = null ) {
		if ( is_null( $campaign ) || is_null( $place ) ) {
			return false;
    }

		$query = new WP_Query(
			array(
				'post_type'				=> 'drsa_ad',
				'post_status'			=> 'publish',
				'posts_per_page'	=> 1,
				'no_found_rows' 	=> true,
				'orderby'					=> 'rand',
				'meta_query'			=> array(
					'relation'		=> 'AND',
					array(
						'key'		=> '_drsa_ad_show',
					),
					array(
						'key'		=> '_drsa_ad_placement',
						'value'	=> $place,
					),
					array(
						'key'			=> '_drsa_ad_timing_start_date',
						'value' 	=> current_time( 'timestamp' ),
						'compare' => '<',
						'type'		=> 'NUMERIC',
					),
					array(
						'key'			=> '_drsa_ad_timing_end_date',
						'value' 	=> current_time( 'timestamp' ),
						'compare' => '>',
						'type'		=> 'NUMERIC',
					),
				),
				'tax_query'				=> array(
					array(
						'taxonomy' => 'drsa_campaigns',
						'terms'    => $campaign,
						'field'		 => 'slug',
					),
				),
			)
		);

		if ( $query->have_posts() ) :
		  while ( $query->have_posts() ) :
				$query->the_post();
				$post_id = get_the_id();

				$return = array(
					'src'						=> wp_get_attachment_url( get_post_thumbnail_id() ),
					'target'				=> get_post_meta( $post_id, '_drsa_ad_target_url', true ),
					'slug'					=> sanitize_title( get_the_title() ),
					'campaign_slug'	=> $campaign,
					'id'						=> $post_id,
				);
		  endwhile;
		else :
			$return = false;
		endif;

		wp_reset_postdata();
		return $return;
	} // end get_campaign_ad

	public static function get_active_campaign( $place = null ) {
		if ( is_null( $place ) ) {
			return false;
    }

		$query = new WP_Term_Query(
			array(
				'taxonomy'				=> 'drsa_campaigns',
				'number'					=> 1,
				'no_found_rows' 	=> true,
				'meta_query'			=> array(
					'relation'		=> 'AND',
					array(
						'key'		=> '_drsa_ad_campaign_placement',
						'value'	=> $place,
					),
					array(
						'key'			=> '_drsa_ad_campaign_timing_start_date',
						'value' 	=> current_time( 'timestamp' ),
						'compare' => '<',
						'type'		=> 'NUMERIC',
					),
					array(
						'key'			=> '_drsa_ad_campaign_timing_end_date',
						'value' 	=> current_time( 'timestamp' ),
						'compare' => '>',
						'type'		=> 'NUMERIC',
					),
				),
			)
		);

		if ( ! empty( $query->terms ) ) :
		  foreach ( $query->terms as $term ) :
				$return = $term->slug;
		  endforeach;
		else :
			$return = false;
		endif;

		wp_reset_postdata();
		return $return;
	} // end get_active_campaign

	public static function build_target_with_utm( $ad = null, $from = 'single', $place = null ) {
		if ( empty( $ad ) || empty( $place ) ) {
			return;
    }

		$utm = array();
		$utm['utm_source'] = apply_filters( 'drsa_utm_source', sanitize_title( get_bloginfo( 'page_name' ) ) );
		$utm['utm_medium'] = apply_filters( 'drsa_utm_medium/' . $place, apply_filters( 'drsa_utm_medium', $place ) );

		if ( 'campaign' === $from ) {
			$utm['utm_content'] = $ad['slug'];
			$utm['utm_campaign'] = $ad['campaign_slug'];
		} else {
			$utm['utm_campaign'] = $ad['slug'];
		}

    // Get possibly existing utm tags
    $parts = parse_url( $ad['target'] );
    parse_str( $parts['query'], $existing_utm );

    // Remove forced utm tags if already in url
    foreach ( $utm as $k => $v ) {
      if ( array_key_exists( $k, $existing_utm ) ) {
        unset( $utm[ $k ] );
      }
    }

		return add_query_arg( $utm, $ad['target'] );

	} // end build_target_with_utm

	public static function update_statistics() {
		$ad = sanitize_text_field( $_POST['ad'] ); // @codingStandardsIgnoreLine
		check_ajax_referer( 'drsa' . $ad, 'nonce' );

		$meta_key = '_drsa_campaing_show_counter';
		if ( isset( $_POST['type'] ) && $_POST['type'] === 'click' ) { // @codingStandardsIgnoreLine
			$meta_key = '_drsa_campaing_click_counter';
    }

		$i = get_post_meta( $ad, $meta_key, true );
		if ( false === $i ) {
			$i = 0;
    }

		$i++;
		update_post_meta( $ad, $meta_key, $i );

    // If ads are shown by count and count goes over the limit, disable ad to prevent it being shown again
    if ( '_drsa_campaing_show_counter' === $meta_key && Dude_Really_Simple_Ads::ad_visibility_by_show_count() ) {
      $post_show_count_limit = get_post_meta( $ad, '_drsa_ad_timing_end_view_count', true );
      if ( $i >= $post_show_count_limit ) {
        delete_post_meta( $ad, '_drsa_ad_show' );
      }
    }

    global $wpdb;
    $ad_data = [
      'ad_id' => $ad,
      'date' => wp_date( 'Y-m-d' ),
      'page' => sanitize_text_field( $_POST['page'] ),
      'place' => sanitize_text_field( $_POST['place'] ),
    ];

    if ( ! isset( $_POST['type'] ) || 'click' !== $_POST['type'] ) {
      $updated = $wpdb->query( $wpdb->prepare( 'UPDATE wp_drsa_ad_data SET show_count = show_count + 1 WHERE ad_id=%d AND date=%s AND page=%s AND place=%s', [ $ad_data['ad_id'], $ad_data['date'], $ad_data['page'], $ad_data['place'] ] ) );
      if ( false === $updated || 0 === $updated ) {
        $ad_data['show_count'] = 1;
        $ad_data['click_count'] = 0;
        $wpdb->insert( 'wp_drsa_ad_data', $ad_data );
      }
    } elseif ( 'click' === $_POST['type'] ) {
      $wpdb->query( $wpdb->prepare( 'UPDATE wp_drsa_ad_data SET click_count = click_count + 1 WHERE ad_id=%d AND date=%s AND page=%s AND place=%s', [ $ad_data['ad_id'], $ad_data['date'], $ad_data['page'], $ad_data['place'] ] ) );
    }

		wp_send_json_success();
	} // end update_statistics

	public static function enqueue_js() {
		if ( ! empty( DRSA_Show_Ads::$visible_ads ) ) {
			wp_enqueue_script( 'drsa_ad_tracking', plugin_dir_url( dirname( __FILE__ ) ) . 'public/js/script.js', [ 'jquery' ], 1, true );
			wp_localize_script( 'drsa_ad_tracking', 'drsa', array(
				'ajax_url'								=> admin_url( 'admin-ajax.php' ),
				'counter_cookie_timeout'	=> apply_filters( 'drsa_counter_cookie_timeout', 30000 ),
				'ads'											=> DRSA_Show_Ads::$visible_ads,
			) );
		}
	}
} // end class

if ( ! function_exists( 'get_the_active_ad' ) ) {
	function get_the_active_ad( $place = null ) {
		$campaign = DRSA_Show_Ads::get_active_campaign( $place );

		$from = 'single';
		if ( $campaign ) {
			$ad = DRSA_Show_Ads::get_campaign_ad( $campaign, $place );
			$from = 'campaign';
		} else {
			$ad = DRSA_Show_Ads::get_active_ad( $place );
		}

		if ( ! empty( $ad ) && empty( $ad['target'] ) ) {
			$ad['target'] = false;
    }

		if ( ! $ad ) {
			$ad['src'] = apply_filters( 'drsa_default_ad/' . $place, false );
			$ad['target'] = apply_filters( 'drsa_default_ad_target/' . $place, false );
			$ad['id'] = 0;
		}

		// No active ad or default, return empty.
		if ( empty( $ad['src'] ) ) {
			return;
		}

		$use_utm = apply_filters( 'drsa_use_utm', true );
		$use_utm = apply_filters( 'drsa_use_utm/' . $place, true );
		$use_utm = apply_filters( 'drsa_use_utm/ad/' . $ad['id'], true );

		if ( $use_utm && ! empty( $ad['target'] ) ) {
			$ad['target'] = DRSA_Show_ads::build_target_with_utm( $ad, $from, $place );
    }

    $place_data = DRSA_Places::get_ad_placements()[ $place ];
    if ( isset( $place_data['multiple'] ) && true === $place_data['multiple'] ) {
      $place = "{$place}-order-" . count(DRSA_Show_Ads::$ads_shown[ $place ] );
    }

		DRSA_Show_Ads::$visible_ads[] = array(
			'adid'									=> $ad['id'],
			'click_counter_element'	=> '.' . apply_filters( 'drsa_click_counter_element', 'drsa-' . $place ),
			'nonce'									=> wp_create_nonce( 'drsa' . $ad['id'] ),
			'place'									=> $place,
			'open_blank'						=> true, // TODO: add global and ad filter
		);

		DRSA_Show_Ads::enqueue_js();

		$ad['place'] = $place;
		$ad['click_counter_class'] = apply_filters( 'drsa_click_counter_element', 'drsa-' . $place );

    if ( true === Dude_Really_Simple_Ads::allow_alternative_image() ) {
      $ad['alternative_image_id'] = get_post_meta( $ad['id'], '_drsa_alternative_image_id' );
      $ad['alternative_image_src'] = get_post_meta( $ad['id'], '_drsa_alternative_image' );
    }

		unset( $ad['id'] );
		unset( $ad['slug'] );
		unset( $ad['campaign_slug'] );
		return $ad;
	} // end get_the_active_ad
}
