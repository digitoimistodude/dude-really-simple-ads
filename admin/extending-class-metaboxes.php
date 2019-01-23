<?php
/**
 * Add metaboxes for ad and campaign.
 *
 * @package dude-really-simple-ads
 */
class DRSA_Metaboxes extends Dude_Really_Simple_Ads {

	public function __construct() {
		parent::__construct();
	} // end __construct

	/**
	 * Add hooks that makes the meta boxes.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	public function run() {
		add_action( 'cmb2_admin_init', array( $this, 'add_ad_metabox' ) );
		add_action( 'cmb2_admin_init', array( $this, 'add_ad_notes_metabox' ) );
		add_action( 'cmb2_admin_init', array( $this, 'add_ad_campaign_metabox' ) );
		add_action( 'cmb2_admin_init', array( $this, 'add_ad_campaign_notes_metabox' ) );
	} // end run

	public function add_ad_metabox() {
		$drsa_places = new DRSA_Places();
    $prefix = '_drsa_ad_timing';

    $cmb = new_cmb2_box(
			array(
				'id'           => $prefix,
				'title'        => __( 'Mainoksen perustiedot', 'dude-really-simple-ads' ),
				'object_types' => array( 'drsa_ad' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);

    $cmb->add_field(
			array(
				'name'				=> __( 'Näyttöaika alkaa', 'dude-really-simple-ads' ),
				'id'					=> $prefix . '_start_date',
				'type'				=> 'text_datetime_timestamp',
				'date_format'	=> 'd.m.Y',
				'time_format'	=> 'H:i',
			)
		);

		$cmb->add_field(
			array(
				'name'				=> __( 'ja loppuu', 'dude-really-simple-ads' ),
				'id'					=> $prefix . '_end_date',
				'type'				=> 'text_datetime_timestamp',
				'date_format'	=> 'd.m.Y',
				'time_format' => 'H:i',
			)
		);

    $prefix = '_drsa_ad_placement';
		$options = $drsa_places->get_ad_placement_options();

		if ( empty( $options ) ) {
			add_action( 'admin_notices', array( 'DRSA_Admin_Hooks', 'admin_notice_no_ad_placements' ) );
		} else {
	    $cmb->add_field(
				array(
					'name'				=> __( 'Käytä mainospaikalla', 'dude-really-simple-ads' ),
					'id'					=> $prefix,
					'type'				=> 'select',
					'options_cb'	=> array( 'DRSA_Places', 'get_ad_placement_options' ),
				)
			);
		}

		$prefix = '_drsa_ad_target_url';
		$cmb->add_field(
			array(
				'name'				=> __( 'Mainoksen kohde', 'dude-really-simple-ads' ),
				'id'					=> $prefix,
				'type'				=> 'text_url',
				'protocols'		=> array( 'http', 'https' ),
			)
		);
	} // end add_ad_metabox

	public function add_ad_notes_metabox() {
    $prefix = '_drsa_ad_notes';

    $cmb = new_cmb2_box(
			array(
				'id'            => $prefix,
				'title'         => __( 'Sisäiset merkinnät', 'dude-really-simple-ads' ),
				'object_types'  => array( 'drsa_ad' ),
				'context'       => 'normal',
				'priority'      => 'low',
				'show_names'    => true,
			)
		);

    $cmb->add_field(
			array(
				'name'				=> __( 'Merkinnät', 'dude-really-simple-ads' ),
				'id'					=> $prefix . '_note',
				'type'				=> 'textarea_small',
				'repeatable'	=> true,
				'options'     => array(
				'add_row_text'    => __( 'Lisää uusi merkintä', 'dude-really-simple-ads' ),
			),
			)
		);
	} // end add_ad_notes_metabox

	public function add_ad_campaign_metabox() {
		$drsa_places = new DRSA_Places();
    $prefix = '_drsa_ad_campaign_timing';

    $cmb = new_cmb2_box(
			array(
				'id'            => $prefix,
				'title'         => __( 'Mainoksen ajatus', 'dude-really-simple-ads' ),
				'object_types'	=> array( 'term' ),
				'taxonomies'		=> array( 'drsa_campaigns' ),
				'context'       => 'normal',
				'priority'      => 'high',
				'show_names'    => true,
			)
		);

    $cmb->add_field(
			array(
				'name'				=> __( 'Näyttöaika alkaa', 'dude-really-simple-ads' ),
				'id'					=> $prefix . '_start_date',
				'type'				=> 'text_datetime_timestamp',
				'date_format'	=> 'd.m.Y',
				'time_format' => 'H:i',
			)
		);

		$cmb->add_field(
			array(
				'name'				=> __( 'Näyttöaika loppuu', 'dude-really-simple-ads' ),
				'id'					=> $prefix . '_end_date',
				'type'				=> 'text_datetime_timestamp',
				'date_format'	=> 'd.m.Y',
				'time_format' => 'H:i',
			)
		);

		$prefix = '_drsa_ad_campaign_placement';
		$options = $drsa_places->get_ad_placement_options();

		if ( empty( $options ) ) {
			add_action( 'admin_notices', array( 'DRSA_Admin_Hooks', 'admin_notice_no_ad_placements' ) );
		} else {
	    $cmb = new_cmb2_box(
				array(
					'id'            => $prefix,
					'title'         => __( 'Mainospaikka', 'dude-really-simple-ads' ),
					'object_types'	=> array( 'term' ),
					'taxonomies'		=> array( 'drsa_campaigns' ),
					'context'       => 'normal',
					'priority'      => 'default',
					'show_names'    => true,
				)
			);

	    $cmb->add_field(
				array(
					'name'				=> __( 'Käytä mainospaikalla', 'dude-really-simple-ads' ),
					'id'					=> $prefix,
					'type'				=> 'select',
					'options_cb'	=> array( 'DRSA_Places', 'get_ad_placement_options' ),
				)
			);
		}
	} // end add_ad_campaign_metabox

	public function add_ad_campaign_notes_metabox() {
    $prefix = '_drsa_ad_campaign_notes';

    $cmb = new_cmb2_box(
			array(
				'id'            => $prefix,
				'title'         => __( 'Sisäiset merkinnät', 'dude-really-simple-ads' ),
				'object_types'	=> array( 'term' ),
				'taxonomies'		=> array( 'drsa_campaigns' ),
				'context'       => 'normal',
				'priority'      => 'low',
				'show_names'    => true,
			)
		);

    $cmb->add_field(
			array(
				'name'				=> __( 'Merkinnät', 'dude-really-simple-ads' ),
				'id'					=> $prefix . '_note',
				'type'				=> 'textarea_small',
				'repeatable'	=> true,
				'options'     => array(
					'add_row_text'    => __( 'Lisää uusi merkintä', 'dude-really-simple-ads' ),
				),
			)
		);
	} // end add_ad_campaign_notes_metabox
} // end class
