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
		add_action( 'add_meta_boxes_drsa_ad', array( $this, 'add_ad_view_details' ) );
	} // end run

	public static function add_ad_metabox() {
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

    if ( true === Dude_Really_Simple_Ads::get_current_ad_end_mode() ) {
      $cmb->add_field(
        [
          'name'				=> __( 'Näyttökertojen yläraja', 'dude-really-simple-ads' ),
          'id'					=> $prefix . '_end_view_count',
          'type'				=> 'text',
          'attributes'  => [
            'type'    => 'number',
            'pattern' => '\d*',
          ],
        ]
        );
    } else {
      $cmb->add_field(
        array(
          'name'				=> __( 'ja loppuu', 'dude-really-simple-ads' ),
          'id'					=> $prefix . '_end_date',
          'type'				=> 'text_datetime_timestamp',
          'date_format'	=> 'd.m.Y',
          'time_format' => 'H:i',
        )
      );
    }

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

    if ( true === Dude_Really_Simple_Ads::allow_alternative_image() ) {
      $cmb->add_field( [
        'name' => __( 'Vaihtoehtoinen kuva', 'dude-really-simple-ads' ),
        'description' => apply_filters( 'drsa_alternative_image_desctiption_text', '' ),
        'id'   => '_drsa_alternative_image',
        'type' => 'file',
        'options' => [
          'url' => false,
        ],
        'text' => [
          'add_upload_file_text' => apply_filters( 'drsa_alternative_image_button_text', __( 'Lisää kuva', 'dude-really-simple-ads' ) ),
        ],
      ] );
    }
	} // end add_ad_metabox

	public static function add_ad_notes_metabox() {
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

	public static function add_ad_campaign_metabox() {
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

	public static function add_ad_campaign_notes_metabox() {
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

  public static function add_ad_view_details( $post ) {
		add_meta_box( 'course-meta-data', 'Mainoksen näyttökerrat', 'DRSA_Metaboxes::ad_meta_data' );
  } // end add_ad_view_details

	public static function ad_meta_data( $post ) {
    global $wpdb;
		$ad_data = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM wp_drsa_ad_data WHERE ad_id=%d', $post->ID ) );

    // Graph for daily ad data
    $ad_data_by_day = [];
		foreach ( $ad_data as $single_data ) {
			$date = wp_date( 'j.n.Y', strtotime( $single_data->date ) );
			if ( isset( $ad_data_by_day[ $date ] ) ) {
				$ad_data_by_day[ $date ]['shows'] = $ad_data_by_day[ $date ]['shows'] += $single_data->show_count;
				$ad_data_by_day[ $date ]['clicks'] = $ad_data_by_day[ $date ]['clicks'] += $single_data->click_count;
			} else {
				$ad_data_by_day[ $date ] = [
					'shows' => absint( $single_data->show_count ),
					'clicks' => absint( $single_data->click_count ),
				];
			}
		}

    $data = [
			'labels' => [],
			'datasets' => [
				'shows' => [
					'name' => 'Näyttökerrat',
					'chartType' => 'bar',
					'values' => [],
				],
				'clicks' => [
					'name' => 'Avauskerrat',
					'chartType' => 'line',
					'values' => [],
				]
			],
		];
		foreach ( $ad_data_by_day as $date => $single_day ) {
      $data['labels'][] = $date;
      $data['datasets']['shows']['values'][] = $single_day['shows'];
      $data['datasets']['clicks']['values'][] = $single_day['clicks'];
		} ?>

    <div id="caravanlehti-ad-statistics-chart"></div>
    <script src="https://cdn.jsdelivr.net/npm/frappe-charts@1.2.4/dist/frappe-charts.min.iife.js"></script>
    <script type="text/javascript">
      const chart = new frappe.Chart( "#caravanlehti-ad-statistics-chart", {
        data: {
          labels: <?php echo json_encode( $data['labels'] ) ?>,
          datasets: <?php echo json_encode( array_values( $data['datasets'] ) ) ?>
        },
        type: 'axis-mixed',
        height: 250,
        colors: ['light-blue', 'red'],
        animate: false,
        truncateLegends: true,
        axisOptions: {
          xAxisMode: 'tick',
          xIsSeries: true,
        },
        barOptions: {
          spaceRatio: 0.2,
        },
        lineOptions: {
          hideDots: true,
        },
      } );
    </script>

    <?php $all_places = DRSA_Places::get_ad_placements(); ?>
		<style type="text/css">
			.ad-data {
				width: 100%;
				font-size: 14px;
			}

			.ad-data tr:nth-child(even) {
				background:#f0f0f0;
			}

			.ad-data th,
			.ad-data td {
				text-align: left;
				padding: 5px;
			}
  	</style>

		<table class="ad-data">
			<tr class="header">
				<th><?php _e( 'Päivämäärä', 'dude-really-simple-ads' ) ?></th>
				<th><?php _e( 'Sivu', 'dude-really-simple-ads' ) ?></th>
				<th><?php _e( 'Paikka', 'dude-really-simple-ads' ) ?></th>
				<th><?php _e( 'Näytöt', 'dude-really-simple-ads' ) ?></th>
				<th><?php _e( 'Avaukset', 'dude-really-simple-ads' ) ?></th>
			</tr>

			<?php foreach ( $ad_data as $row ) :
				$ad_place_and_order = explode( '-order-', $row->place );
        $ad_place_title = isset( $all_places[ $ad_place_and_order[0] ]['name'] ) ? $all_places[ $ad_place_and_order[0] ]['name'] : $ad_place_and_order[0];
        if ( 1 < count( $ad_place_and_order ) ) {
          $place_text = $ad_place_title . ', ' . $ad_place_and_order[1];
        } else {
          $place_text = $ad_place_title;
        }
        ?>
				<tr>
					<td><?php echo esc_html( wp_date( 'j.n.Y', strtotime( $row->date ) ) ) ?></td>
					<td><?php echo esc_html( $row->page ) ?></td>
					<td><?php echo esc_html( $place_text ) ?></td>
					<td><?php echo esc_html( $row->show_count ) ?></td>
					<td><?php echo esc_html( $row->click_count ) ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
  <?php } // end ad_meta_data
} // end class
