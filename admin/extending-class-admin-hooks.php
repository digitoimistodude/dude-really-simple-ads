<?php
/**
 * Admin hooks.
 *
 * @package dude-really-simple-ads
 */
class DRSA_Admin_Hooks extends Dude_Really_Simple_Ads {

	/**
	 * Re-position WordPress default meta boxes, in order to make ad managing
	 * view to focus on important things.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	public static function reorder_metaboxes() {
		add_meta_box( 'postimagediv', __( 'Mainos', 'dude-really-simple-ads' ), 'post_thumbnail_meta_box', 'drsa_ad', 'normal', 'high' );
		add_meta_box( 'authordiv', __( 'Author', 'dude-really-simple-ads' ), 'post_author_meta_box', 'drsa_ad', 'side', 'low' );
	} // end reorder_metaboxes

	/**
	 * Small, small thing. Change featured image meta box texts.
	 *
	 * @param   string $content meta box default content.
	 * @return  string          meta box content with changed strings.
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	public static function change_featured_image_links( $content ) {
		// @codingStandardsIgnoreStart
		$content = str_replace(
      [ __( 'Set featured image' ), 'Aseta artikkelikuva' ],
      __( 'Lisää mainosbanneri', 'dude-really-simple-ads' ),
      $content
    );

    $content = str_replace(
      [ __( 'Remove featured image' ), 'Poista artikkelikuva' ],
      __( 'Poista mainosbanneri', 'dude-really-simple-ads' ),
      $content
    );

    $content = str_replace( __( 'Click the image to edit or update' ), '', $content );
		// @codingStandardsIgnoreEnd

		return $content;
	} // end change_featured_image_links

	/**
	 * Show error notice if theme author have not set any possible places for ads.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	public static function admin_notice_no_ad_placements() {
		?>
    <div class="notice notice-error">
			<p><b><?php esc_attr_e( 'Hei moi!', 'dude-really-simple-ads' ); ?></b><br><?php esc_attr_e( 'Näyttää siltä että teemassasi ei ole yhtään rekisteröityä mainospaikkaa. Jotta lisäämäsi mainokset toimisivat, rekisteröi teeman mainospaikat ja valitse jo luoduille mainoksille oikea paikka.', 'dude-really-simple-ads' ); ?></p>
    </div>
		<?php
	} // end admin_notice_no_ad_placements

	/**
	 * Run image size validation when saving the drsa_ad post type.
	 *
	 * @param   integer $post_id post id.
	 * @param   object  $post    post object before changes saved.
	 * @param   boolean $update  are we updating existing post.
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	public static function validate_feature_image_size( $post_id, $post, $update ) {
		if ( 'drsa_ad' !== $post->post_type ) {
			return;
    }

    // @codingStandardsIgnoreStart
		// Invalidate ad if there's no image
		if ( ! array_key_exists( '_thumbnail_id', $_POST ) || ! is_numeric( $_POST['_thumbnail_id'] ) ) {
      update_post_meta( $post_id, '_drsa_ad_size_valid', false );
			return;
		}

		// Invalidate ad if there's no place selected
		if ( ! array_key_exists( '_drsa_ad_placement', $_POST ) ) {
      update_post_meta( $post_id, '_drsa_ad_size_valid', false );
			return;
		}

		// Get possible and selected ad place
		$places = DRSA_Places::get_ad_placements();
		$ad_placement = $_POST['_drsa_ad_placement'];

		// Get image id and metadata for checking size
		$image_id = $_POST['_thumbnail_id'];
		$image_metadata = get_post_meta( $image_id, '_wp_attachment_metadata', true );
		// @codingStandardsIgnoreEnd

    /**
     * Check image size against selected ad place size definitions.
     * Validate the ad if size matches.
     * First check if place has multiple allowed heights and run checks
     * against each one of those. If one matches, validate the ad.
     */
    if ( is_array( $places[ $ad_placement ]['height'] ) ) {
      foreach ( $places[ $ad_placement ]['height'] as $height ) {
        if ( $places[ $ad_placement ]['width'] === $image_metadata['width'] && $height === $image_metadata['height'] ) {
          update_post_meta( $post_id, '_drsa_ad_size_valid', true );
          return;
        }
      }
    } else {
      if ( $places[ $ad_placement ]['width'] === $image_metadata['width'] && $places[ $ad_placement ]['height'] === $image_metadata['height'] ) {
        update_post_meta( $post_id, '_drsa_ad_size_valid', true );
        return;
      }
    }

		// Checks didn't pass, invalidate ad
    update_post_meta( $post_id, '_drsa_ad_size_valid', false );
	} // end validate_feature_image_size

  public static function update_show_status( $meta_id, $post_id, $meta_key, $meta_value ) {
    if ( '_drsa_ad_timing_end_view_count' !== $meta_key ) {
      return;
    }

    $valid = get_post_meta( $post_id, '_drsa_ad_size_valid', true );
    if ( ! $valid ) {
      delete_post_meta( $post_id, '_drsa_ad_show' );
      return;
    }

    if ( Dude_Really_Simple_Ads::ad_visibility_by_show_count() ) {
      $post_show_count = get_post_meta( $post_id, '_drsa_campaing_show_counter', true );
      $post_show_count_limit = $meta_value;

      if ( $post_show_count >= $post_show_count_limit ) {
        delete_post_meta( $post_id, '_drsa_ad_show' );
        return;
      }
    }

    update_post_meta( $post_id, '_drsa_ad_show', 'true' );
  } // end update_show_status

  public static function create_empty_meta_show_counter( $post_id ) {
    $exists = get_post_meta( $post_id, '_drsa_campaing_show_counter', true );
    if ( $exists || '0' === $exists  ) {
      return;
    }

    add_post_meta( $post_id, '_drsa_campaing_show_counter', '0' );
  } // end create_empty_meta_show_counter

	/**
	 * Maybe show admin notice when editing drsa_ad.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	public static function admin_notices() {

		// If ad is valid, do not continue
		$show_ad = get_post_meta( get_the_id(), '_drsa_ad_show', true );
		if ( ! empty( $show_ad ) ) {
			return;
   	}

    $valid = get_post_meta( get_the_id(), '_drsa_ad_size_valid', true );
    if ( $valid ) {
      return;
    }

		// Get possible ad places and selected one
		$places = DRSA_Places::get_ad_placements();
		$ad_placement = get_post_meta( get_the_id(), '_drsa_ad_placement', true );

    if ( is_array( $places[ $ad_placement ]['height'] ) ) {
      $places[ $ad_placement ]['height'] = implode( '/', $places[ $ad_placement ]['height'] );
    }

		// Show error notice containing right dimensions ?>
	 	<div class="notice notice-error">
			<p><b><?php esc_attr_e( 'Kuva on väärän kokoinen, mainosta ei näytetä!', 'dude-really-simple-ads' ); ?></b><br><?php printf( __( 'Lataamasi mainoskuva ei täytyä valitun mainospaikan kokovaatimusta, ole hyvä ja lataa kuva jonka koko on %1$s x %2$s pikseliä.', 'dude-really-simple-ads' ), $places[ $ad_placement ]['width'], $places[ $ad_placement ]['height'] ); ?></p>
		</div>
	<?php } // end admin_notices

	/**
	 * Remove quikd edit link in drsa_ad post listing.
	 *
	 * @param   array $actions possible actions to perform for post.
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	public static function remove_quick_edit( $actions ) {
		global $post;

		if ( 'drsa_ad' === $post->post_type ) {
			unset( $actions['inline hide-if-no-js'] );
		}

		return $actions;
	} // end remove_quick_edit

  public static function init_data_table() {
    if ( ! empty( get_option( 'wp_drsa_ad_data_created' ) ) ) {
      return;
    }

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE wp_drsa_ad_data  (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `ad_id` bigint(20) DEFAULT NULL,
      `date` date DEFAULT NULL,
      `page` varchar(128) DEFAULT NULL,
      `place` varchar(128) DEFAULT NULL,
      `show_count` bigint(20) DEFAULT NULL,
      `click_count` bigint(20) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) {$charset_collate};";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    update_option( 'wp_drsa_ad_data_created', wp_date( 'Y-m-d H:i:s' ) );
  } // end init_data_table
} // end class
