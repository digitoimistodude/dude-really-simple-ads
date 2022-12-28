<?php
/**
 * Handle place related things.
 *
 * @package dude-really-simple-ads
 */
class DRSA_Places extends Dude_Really_Simple_Ads {

	public function __construct() {
		parent::__construct();
	} // end __construct

	public static function get_ad_placements() {
		$places = apply_filters( 'drsa_ad_placement_sizes', array() );
		$options = array();

		foreach ( $places as $place_key => $place ) {
			if ( array_key_exists( 'name', $place ) ) {
        if ( is_array( $places[ $place_key ]['height'] ) ) {
          asort( $places[ $place_key ]['height'] );
        }

				$options[ $place['id'] ] = $places[ $place_key ];
			}
		}

		return $options;
	} // end get_ad_placements

	public static function get_ad_placement_options( $show_size = true ) {
		$places = self::get_ad_placements();
		$options = array();

		foreach ( $places as $place_key => $place ) {
			if ( array_key_exists( 'name', $place ) ) {
				if ( $show_size ) {
          if ( is_array( $place['height'] ) ) {
            $place['height'] = implode( '/', $place['height'] );
          }

					$options[ $place_key ] = $place['name'] . ' (' . $place['width'] . 'x' . $place['height'] . 'px)';
				} else {
					$options[ $place_key ] = $place['name'];
				}
			}
		}

		return $options;
	} // end get_ad_placement_options
} // end class
