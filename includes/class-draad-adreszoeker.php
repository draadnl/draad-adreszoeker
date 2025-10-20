<?php
/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
if ( !class_exists( 'Draad_Adreszoeker' ) ) {
	
	class Draad_Adreszoeker {

		private static $instance = null;

		private $version;

		public static function get_instance() {
			if ( self::$instance === null ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function __construct() {

			$this->import_handler = new Draad_Adreszoeker_Import();

			// Save Plugin data
			add_action( 'wp', [ $this, 'set_plugin_data' ] );
			register_activation_hook( DRAAD_ADRESZOEKER_FILE, [ $this, 'set_plugin_data' ] );

			// Register block
			add_action( 'init', [ $this, 'register_block' ] );

			// Register assets
			add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );

			// Register ajax handler.
			add_action( 'wp_ajax_nopriv_draad_adreszoeker_get_advice', [ $this, 'get_advice' ] );
			add_action( 'wp_ajax_draad_adreszoeker_get_advice', [ $this, 'get_advice' ] );
			add_action( 'wp_ajax_nopriv_draad_adreszoeker_get_advice_react', [ $this, 'get_advice_react' ] );
			add_action( 'wp_ajax_draad_adreszoeker_get_advice_react', [ $this, 'get_advice_react' ] );
			add_action( 'wp_ajax_nopriv_draad_adreszoeker_get_streets', [ $this, 'get_streets' ] );
			add_action( 'wp_ajax_draad_adreszoeker_get_streets', [ $this, 'get_streets' ] );

		}

		public function set_plugin_data() {
			$pluginData = get_plugin_data( DRAAD_ADRESZOEKER_FILE );
			$this->version = $pluginData['Version'];

			if ( get_option( 'draad_az_version' ) !== $this->version ) {
				update_option( 'draad_az_version', $this->version );
			}
		}

		public function register_block() {
			register_block_type( DRAAD_ADRESZOEKER_DIR . '/build/draad-adreszoeker' );
			register_block_type( DRAAD_ADRESZOEKER_DIR . '/build/draad-adreszoeker-formulier' );
			register_block_type( DRAAD_ADRESZOEKER_DIR . '/build/draad-adreszoeker-output' );
		}

		public function register_assets() {
			// Tabs
			wp_register_script( 'draad-tabs-script', DRAAD_ADRESZOEKER_URL . 'build/js/tabs.js', [], $this->version, true );

			// Toggle
			wp_register_script( 'draad-toggle-script', DRAAD_ADRESZOEKER_URL . 'build/js/toggle.js', [], $this->version, true );
		}

		public function get_streets() {

			$streetQuery = filter_input( INPUT_POST,'street', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$streetQuery = preg_replace( '/[^\w\s]/u', '', $streetQuery );

			if (  empty( $streetQuery ) || strlen( $streetQuery ) < 2 ) {
				wp_send_json_error('Straatnaam moet minimaal 2 karakters bevatten.');
			}

			global $wpdb;

			$table_name = $wpdb->prefix . 'draad_az_addresses';
			$query = $wpdb->prepare(
				"SELECT DISTINCT street FROM {$table_name} WHERE street LIKE %s",
				'%' . $wpdb->esc_like($streetQuery) . '%'
			);

			$results = $wpdb->get_results( $query, ARRAY_A );

			wp_send_json_success($results ?: []);

			wp_send_json_success( esc_html__( 'Mooie lijst met straten.', 'draad-adreszoeker' ) );

		}

		public function get_advice() {

			$streetQuery = filter_input( INPUT_POST,'street', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$streetQuery = preg_replace( '/[^\w\s]/u', '', $streetQuery );

			if (  empty( $streetQuery ) || strlen( $streetQuery ) < 2 ) {
				wp_send_json_error( esc_html__( 'Straatnaam moet minimaal 2 karakters bevatten.', 'draad-adreszoeker' ) );
			}

			$number = (int) filter_input( INPUT_POST,'number', FILTER_SANITIZE_NUMBER_INT ) ?: 0;

			if ( !$number ) {
				wp_send_json_error( esc_html__( 'Ongeldig huisnummer opgegeven.', 'draad-adreszoeker' ) );
			}

			global $wpdb;

			$query = $wpdb->prepare(
				'SELECT * FROM '. $wpdb->prefix .'draad_az_addresses WHERE street = "%s" AND huisnummer = "%d" LIMIT 1',
				$wpdb->esc_like($streetQuery),
				$wpdb->esc_like($number)
			);

			$neighbourhood = $wpdb->get_row( $query, ARRAY_A );
			ob_start();
			require_once DRAAD_ADRESZOEKER_DIR . 'templates/grid-container.php';
			$output = ob_get_clean();

			wp_send_json_success($output);

			wp_send_json_success( esc_html__( 'Resultaten successvol opgehaald.', 'draad-adreszoeker' ) );

		}

		public function get_advice_react() {

			$streetQuery = filter_input( INPUT_POST,'street', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$streetQuery = preg_replace( '/[^\w\s]/u', '', $streetQuery );

			if (  empty( $streetQuery ) || strlen( $streetQuery ) < 2 ) {
				wp_send_json_error( esc_html__( 'Straatnaam moet minimaal 2 karakters bevatten.', 'draad-adreszoeker' ) );
			}

			$number = (int) filter_input( INPUT_POST,'number', FILTER_SANITIZE_NUMBER_INT ) ?: 0;

			if ( !$number ) {
				wp_send_json_error( esc_html__( 'Ongeldig huisnummer opgegeven.', 'draad-adreszoeker' ) );
			}

			global $wpdb;

			$query = $wpdb->prepare(
				'SELECT * FROM '. $wpdb->prefix .'draad_az_addresses WHERE street = "%s" AND huisnummer = "%d" LIMIT 1',
				$wpdb->esc_like($streetQuery),
				$wpdb->esc_like($number)
			);

			$neighbourhood_data = $wpdb->get_row( $query, ARRAY_A );
			
			if ( !$neighbourhood_data ) {
				wp_send_json_error( esc_html__( 'Geen gegevens gevonden voor dit adres.', 'draad-adreszoeker' ) );
			}

			$tiles = [];

			// Get neighbourhood post
			$neighbourhoods = get_posts( [
				'post_type' => 'draad_az_area',
				'posts_per_page' => 1,
				'post_status' => 'publish',
				'meta_query' => [
					[
						'key' => 'neigbourhood_code',
						'value' => (int) $neighbourhood_data['buurtcode'] ?: 0,
						'compare' => '='
					]
				]
			] );

			if ( empty( $neighbourhoods ) ) {
				wp_send_json_error( esc_html__( 'Geen buurt informatie gevonden.', 'draad-adreszoeker' ) );
			}

			$neighbourhood = $neighbourhoods[0];
			$neighbourhoodID = $neighbourhood->ID;

			$neighbourhoodTiles = get_field( 'tiles', $neighbourhoodID );
			if ( is_iterable( $neighbourhoodTiles ) && !empty( $neighbourhoodTiles) ) {
				$tiles = array_merge( $tiles, $neighbourhoodTiles );
			}

			// Get heat solution
			$heatSolution = get_field( 'heat_solution_dropdown', $neighbourhoodID );
			$heatSolutionKey = ( $heatSolution ) ? $heatSolution['value'] : '';
			$heatSolutionLabel = ( $heatSolution ) ? $heatSolution['label'] : '';

			// Get base content
			$textNumber = ( get_field( 'text_number', $neighbourhoodID ) ) ? get_field( 'text_number', $neighbourhoodID ) : 0;
			$baseContent = '';
			$anchors = [];

			$base = get_posts( [
				'post_type' => 'draad_az_text',
				'posts_per_page' => 1,
				'post_status' => 'publish',
				'meta_query' => [
					[
						'key' => 'text_number_main',
						'value' => $textNumber,
						'compare' => '='
					]
				]
			] );

			if ( !empty( $base ) ) {
				$base = $base[0];
				$base_ID = $base->ID;
				$content = get_field( 'text', $base_ID );
				
				if ( $content ) {
					// Process anchors for table of contents
					preg_match_all( "/(<h([1-3])(.*?))>(.*?)<\/h[1-3]>/", $content, $matches, PREG_SET_ORDER );
					
					foreach ( $matches as $match ) {
						$title = wp_strip_all_tags( $match[4] );
						$anchor = sanitize_title( $title );
						$new_heading = '<h' . esc_html( $match[2] ) . ' id="' . esc_attr( $anchor ) . '"' . esc_html( $match[3] ) . ' class="utrecht-heading-'. esc_attr( $match[2] ) .'">' . esc_html( $match[4] ) . '</h' . esc_html( $match[2] ) . '>';
						$content = str_replace( $match[0], $new_heading, $content );

						$anchors[] = [
							'anchor' => '#' . $anchor,
							'title'  => $title,
						];
					}
					
					$baseContent = $content;

					$baseTiles = get_field( 'tiles', $base_ID );
					if ( is_iterable( $baseTiles ) && !empty( $baseTiles) ) {
						$tiles = array_merge( $tiles, $baseTiles );
					}
				}
			}

			// Get build period information
			$build_periods = get_terms( [ 'taxonomy' => 'draad_az_build_period', 'hide_empty' => false ] );
			$bouwjaarInt = (int) $neighbourhood_data['bouwjaar'];
			$taxonomies = [];
			$years = [];
			$buildPeriodDescription = '';

			if ( ! is_wp_error( $build_periods ) ) {
				foreach ( $build_periods as $j => $year ) {
					$startYear = (int) get_field( 'start_year', 'draad_az_build_period_' . $year->term_id );
					$endYear = (int) get_field( 'end_year', 'draad_az_build_period_' . $year->term_id );

					if ( ! $startYear || ! $endYear ) {
						continue;
					}

					if ( $bouwjaarInt < $startYear || $bouwjaarInt > $endYear ) {
						continue;
					}

					$taxonomies[$year->term_id] = $year->slug;
					$years[] = $year->name;
					
					if ( $year->description ) {
						$buildPeriodDescription = $year->description;
					}
				}
			}

			// Get tabs data
			$tabs_config = [
				'isolatie' => esc_html__( 'Isolatie', 'draad-adreszoeker' ),
				'ventilatie' => esc_html__( 'Ventileren', 'draad-adreszoeker' ),
				'opwekken' => esc_html__( 'Energie opwekken en opslaan', 'draad-adreszoeker' ),
				'verwarmen' => esc_html__( 'Verwarming', 'draad-adreszoeker' ),
				'koken' => esc_html__( 'Koken op inductie', 'draad-adreszoeker' ),
				'subsidies' => esc_html__( 'Leningen en subsidies', 'draad-adreszoeker' ),
			];

			$tabs = [];
			foreach ( $tabs_config as $index => $tab_label ) {
				$tabGroup = get_field( $index, 'draad_az' );
				$icon = ( $tabGroup && $tabGroup['icon'] ) ? $tabGroup['icon'] : '';
				
				// Get intro content
				$intro = '';
				if ( is_iterable( $tabGroup[ 'repeater' ] ) ) {
					foreach ( $tabGroup[ 'repeater' ] as $repeater ) {
						$periodOutOfTaxonomies = $taxonomies ? array_key_first( $taxonomies ) : null;

						if ( ! $repeater[ 'heat_solution_dropdown' ] && ! $repeater[ 'period' ] && ! $repeater[ 'content' ] ) {
							continue;
						}
				
						if ( in_array( $heatSolutionKey, $repeater[ 'heat_solution_dropdown' ], true ) && in_array( $periodOutOfTaxonomies, $repeater[ 'period' ], true ) ) {	
							if ( isset( $repeater['tiles'] ) && is_iterable( $repeater['tiles'] ) && !empty( $repeater['tiles'] ) ) {
								$tiles = array_merge( $repeater['tiles'], $tiles );
							} 

							if ( isset( $repeater['button'] ) && !empty( $repeater['tiles'] ) ) {
								$button = $repeater['button'];
							} 
	
							$intro = $repeater[ 'content' ];
							break;
						}
					}
				}

				// Get advice items for this tab
				$advice_2_args = [
					'post_type' => 'draad_az_text_2',
					'posts_per_page' => -1,
					'post_status' => 'publish',
					'orderby' => 'menu_order',
					'order' => 'ASC',
					'meta_query' => [
						[
							'key' => 'tab',
							'value' => '"' . $index . '"',
							'compare' => 'LIKE',
						],
					],
					'tax_query' => [
						[
							'taxonomy' => 'draad_az_build_period',
							'field' => 'slug',
							'terms' => $taxonomies,
							'operator' => 'IN',
						],
					],
				];

				$advice_2 = get_posts( $advice_2_args );
				$advice_items = [];

				if ( !empty( $advice_2 ) ) {
					foreach ( $advice_2 as $advice_post ) {
						$id = $advice_post->ID;
						$heatSolutionDropdown = get_field( 'heat_solution_dropdown', $id );

						if ( ! $heatSolutionDropdown ) {
							continue;
						}

						$matches_heat_solution = false;
						foreach ( $heatSolutionDropdown as $key => $value ) {
							$filteredKey = $value['value'];
							if ( $heatSolutionKey === $filteredKey ) {
								$matches_heat_solution = true;
								break;
							}
						}

						if ( $matches_heat_solution ) {
							$link = get_field( 'link', $id );
							$content = get_field( 'content', $id ) ?: get_the_excerpt( $id );
							$thumbnail = get_post_thumbnail_id( $id );
							$thumbnail_data = null;

							if ( $thumbnail ) {
								$thumbnail_data = [
									'url' => wp_get_attachment_image_src( $thumbnail, 'large' )[0],
									'alt' => get_post_meta( $thumbnail, '_wp_attachment_image_alt', true )
								];
							}

							$advice_items[] = [
								'ID' => $id,
								'post_title' => get_the_title( $id ),
								'content' => $content,
								'excerpt' => get_the_excerpt( $id ),
								'link' => $link,
								'thumbnail' => $thumbnail_data
							];
						}
					}
				}

				$tabs[] = [
					'key' => $index,
					'label' => $tab_label,
					'icon' => $icon,
					'intro' => $intro,
					'advice' => $advice_items
				];
			}

			// Get address title
			$adreszoekerAddressTitle = get_field( 'address_title', 'draad_az' );
			$adreszoekerAddressTitleSanitize = ( $adreszoekerAddressTitle ) ? sanitize_title( $adreszoekerAddressTitle ) : '';

			$tilesContent = [];
			$subsidiesKey = array_search( 'subsidies', array_column( $tabs, 'key' ) );
			if ( $subsidiesKey ) {
				$tilesContent = $tabs[$subsidiesKey];
				unset( $tabs[$subsidiesKey] );
			}
			
			$tiles = array_map( function ( $tile ) {
				
				if ( !$tile['post'] || !isset( $tile['post'] ) ) {
					return false;
				}

				$tile['link'] = get_the_permalink( $tile['post'] );
				$tile['title'] = get_the_title( $tile['post'] );
				$tile['description'] = wp_strip_all_tags( get_the_excerpt() , true );

				return $tile;

			}, $tiles );
			$tiles = array_filter( $tiles );
			$tiles = array_slice( $tiles, 0, 2 );

			// Prepare response data
			$response_data = [
				'query' => [
					'street' => $streetQuery,
					'number' => $number
				],
				'neighbourhood' => [
					'ID' => $neighbourhood->ID,
					'post_title' => $neighbourhood->post_title
				],
				'neighbourhoodData' => [
					'bouwjaar' => $neighbourhood_data['bouwjaar'],
					'heatSolution' => $heatSolution,
					'heatSolutionKey' => $heatSolutionKey,
					'heatSolutionLabel' => $heatSolutionLabel,
					'energielabel' => $neighbourhood_data['energielabel'],
					'baseContent' => $baseContent,
					'anchors' => $anchors,
					'addressTitle' => $adreszoekerAddressTitle,
					'addressTitleSanitized' => $adreszoekerAddressTitleSanitize,
					'buildYear' => $years ? array_values( $years )[0] : null,
					'buildPeriodDescription' => $buildPeriodDescription
				],
				'tabs' => $tabs,
				'tiles' => [
					'content' => $tilesContent,
					'tiles' => $tiles,
					'button' => (isset($button)) ? $button : false,
				],
			];

			wp_send_json_success($response_data);

		}

	}

}