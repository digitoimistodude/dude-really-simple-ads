<?php

class DRSA_Show_Ads extends Dude_Really_Simple_Ads {
	public function __construct() {
		parent::__construct();
	} // end __construct

	public function get_active_ad( $place = null ) {
		if( is_null( $place ) )
			return false;

		$query = new WP_Query( array(
		  'post_type'				=> 'drsa_ad',
		  'post_status'			=> 'public',
			'posts_per_page'	=> 1,
			'no_found_rows' 	=> true,
		  'meta_query'			=> array(
				'relation'		=> 'AND',
				array(
					'key'		=> '_drsa_ad_show'
				),
				array(
					'key'		=> '_drsa_ad_placement',
					'value'	=> $place
				),
				array(
					'key'			=> '_drsa_ad_timing_start_date',
          'value' 	=> current_time( 'timestamp' ),
          'compare' => '<',
          'type'		=> 'NUMERIC'
				),
				array(
					'key'			=> '_drsa_ad_timing_end_date',
          'value' 	=> current_time( 'timestamp' ),
          'compare' => '>',
          'type'		=> 'NUMERIC'
				)
			)
		) );

		if( $query->have_posts() ):
		  while( $query->have_posts() ):
		    $query->the_post();
				$post_id = get_the_id();

				$return = array(
					'src'			=> wp_get_attachment_url( get_post_thumbnail_id() ),
					'target'	=> get_post_meta( $post_id, '_drsa_ad_target_url', true ),
					'slug'		=> sanitize_title( get_the_title() ),
					'id'			=> $post_id
				);
		  endwhile;
		else:
			$return = false;
		endif;

		wp_reset_postdata();
		return $return;
	} // end get_active_ad

	public function get_campaign_ad( $campaign = null, $place = null ) {
		if( is_null( $campaign ) || is_null( $place ) )
			return false;

		$query = new WP_Query( array(
		  'post_type'				=> 'drsa_ad',
		  'post_status'			=> 'public',
			'posts_per_page'	=> 1,
			'no_found_rows' 	=> true,
			'orderby'					=> 'rand',
			'meta_query'			=> array(
				'relation'		=> 'AND',
				array(
					'key'		=> '_drsa_ad_show'
				),
				array(
					'key'		=> '_drsa_ad_placement',
					'value'	=> $place
				)
			),
			'tax_query'				=> array(
				array(
					'taxonomy' => 'drsa_campaigns',
					'terms'    => $campaign,
					'field'		 => 'slug'
				),
			)
		) );

		if( $query->have_posts() ):
		  while( $query->have_posts() ):
		    $query->the_post();
				$post_id = get_the_id();

				$return = array(
					'src'						=> wp_get_attachment_url( get_post_thumbnail_id() ),
					'target'				=> get_post_meta( $post_id, '_drsa_ad_target_url', true ),
					'slug'					=> sanitize_title( get_the_title() ),
					'campaign_slug'	=> $campaign,
					'id'						=> $post_id
				);
		  endwhile;
		else:
			$return = false;
		endif;

		wp_reset_postdata();
		return $return;
	} // end get_campaign_ad

	public function get_active_campaign( $place = null ) {
		if( is_null( $place ) )
			return false;

		$query = new WP_Term_Query( array(
		  'taxonomy'				=> 'drsa_campaigns',
			'number'					=> 1,
			'no_found_rows' 	=> true,
		  'meta_query'			=> array(
				'relation'		=> 'AND',
				array(
					'key'		=> '_drsa_ad_campaign_placement',
					'value'	=> $place
				),
				array(
					'key'			=> '_drsa_ad_campaign_timing_start_date',
          'value' 	=> current_time( 'timestamp' ),
          'compare' => '<',
          'type'		=> 'NUMERIC'
				),
				array(
					'key'			=> '_drsa_ad_campaign_timing_end_date',
          'value' 	=> current_time( 'timestamp' ),
          'compare' => '>',
          'type'		=> 'NUMERIC'
				)
			)
		) );

		if( !empty( $query->terms ) ):
		  foreach( $query->terms as $term ):
				$return = $term->slug;
		  endforeach;
		else:
			$return = false;
		endif;

		wp_reset_postdata();
		return $return;
	} // end get_active_campaign

	public function build_target_with_utm( $ad = null, $from = 'single', $place = null ) {
		if( empty( $ad ) || empty( $place ) )
			return;

		$utm = array();
		$utm['utm_source'] = apply_filters( 'drsa_utm_source', get_bloginfo( 'page_name' ) );
		$utm['utm_medium'] = apply_filters( 'drsa_utm_medium/'.$place, apply_filters( 'drsa_utm_medium', $place ) );

		if( $from === 'campaign' ) {
			$utm['utm_content'] = $ad['slug'];
			$utm['utm_campaign'] = $ad['campaign_slug'];
		} else {
			$utm['utm_campaign'] = $ad['slug'];
		}

		return add_query_arg( $utm, $ad['target'] );

	} // end build_target_with_utm

	public function update_statistics() {
		$ad = sanitize_text_field( $_POST['ad'] );
		check_ajax_referer( 'drsa'.$ad, 'nonce' );

		$meta_key = '_drsa_campaing_show_counter';
		if( $_POST['type'] === 'click' )
			$meta_key = '_drsa_campaing_click_counter';

		$i = get_post_meta( $ad, $meta_key, true );
		if( false === $i )
			$i = 0;

		$i++;
		update_post_meta( $ad, $meta_key, $i );
		wp_send_json_success();
	}
} // end class

if( !function_exists( 'get_the_active_ad' ) ) {
	function get_the_active_ad( $place = null ) {
		$campaign = DRSA_Show_Ads::get_active_campaign( $place );

		$from = 'single';
		if( $campaign ) {
			$ad = DRSA_Show_Ads::get_campaign_ad( $campaign, $place );
			$from = 'campaign';
		} else {
			$ad = DRSA_Show_Ads::get_active_ad( $place );
		}

		if( !empty( $ad ) && empty( $ad['target'] ) )
			$ad['target'] = false;

		if( !$ad ) {
			$ad['src'] = apply_filters( 'drsa_default_ad/'.$place, false );
			$ad['target'] = apply_filters( 'drsa_default_ad_target/'.$place, false );
			$ad['id'] = 0;
		}

		if( apply_filters( 'drsa_use_utm', true ) && !empty( $ad['target'] ) )
			$ad['target'] = DRSA_Show_ads::build_target_with_utm( $ad, $from, $place );

		wp_enqueue_script( 'drsa_ad_tracking' );
		wp_localize_script( 'drsa_ad_tracking', 'drsa', array(
			'ajax_url'								=> admin_url( 'admin-ajax.php' ),
			'counter_cookie_timeout'	=> apply_filters( 'drsa_counter_cookie_timeout', 30000 ),
			'ad'											=> $ad['id'],
			'click_counter_element'		=> apply_filters( 'drsa_click_counter_element', '.drsa-'.$place ),
			'nonce'										=> wp_create_nonce( 'drsa'.$ad['id'] )
		) );

		unset( $ad['id'] );
		unset( $ad['slug'] );
		unset( $ad['campaign_slug'] );
		return $ad;
	} // end get_the_active_ad
}
