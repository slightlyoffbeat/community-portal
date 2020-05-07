<?php
/**
 * Utils
 *
 * Util functions
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

/**
 * Add metabox to campaign admin page
 */
function mozilla_campaign_metabox() {
	add_meta_box(
		'campaign-export-events',
		'Export Events',
		'mozilla_show_campaign_metabox',
		'campaign',
		'side',
		'default'
	);

}

/**
 * Markup for campaign metabox
 *
 * @param object $post post object.
 */
function mozilla_show_campaign_metabox( $post ) {
	$nonce = wp_create_nonce( 'campaign-events' );
	echo wp_kses(
		"<div><a href=\"/wp-admin/admin-ajax.php?action=download_campaign_events&campaign={$post->ID}&nonce={$nonce}\">Export events related to this campaign</a></div>",
		array(
			'a'   => array( 'href' => array() ),
			'div' => array(),
		)
	);
}

/**
 * Add metabox to activity admin page
 */
function mozilla_activity_metabox() {
	add_meta_box(
		'activity-export-events',
		'Export Events',
		'mozilla_show_activity_metabox',
		'activity',
		'side',
		'default'
	);

}

/**
 * Markup for activity metabox
 *
 * @param object $post post object.
 */
function mozilla_show_activity_metabox( $post ) {
	echo wp_kses(
		"<div><a href=\"/wp-admin/admin-ajax.php?action=download_activity_events&activity={$post->ID}\">Export events related to this activity</a></div>",
		array(
			'a'   => array( 'href' => array() ),
			'div' => array(),
		)
	);
}

/**
 * General function for uploading images
 */
function mozilla_upload_image() {

	if ( ! empty( $_FILES ) ) {

		if ( isset( $_REQUEST['my_nonce_field'] ) ) {
			$nonce = trim( sanitize_text_field( wp_unslash( $_REQUEST['my_nonce_field'] ) ) );

			if ( wp_verify_nonce( $nonce, 'protect_content' ) ) {

				if ( isset( $_FILES['file'] ) && isset( $_FILES['file']['tmp_name'] ) ) {
					$image_file = sanitize_text_field( wp_unslash( $_FILES['file']['tmp_name'] ) );

					$image     = getimagesize( $image_file );
					$file_size = filesize( $image_file );

					$file_size_kb           = number_format( $file_size / 1024, 2 );
					$options                = wp_load_alloptions();
					$max_files_size_allowed = isset( $options['image_max_filesize'] ) && intval( $options['image_max_filesize'] ) > 0 ? intval( $options['image_max_filesize'] ) : 500;

					if ( $file_size_kb <= $max_files_size_allowed ) {
						if ( isset( $image[2] ) && in_array( $image[2], array( IMAGETYPE_JPEG, IMAGETYPE_PNG ), true ) ) {
							if ( ! empty( $_FILES['file']['name'] ) ) {
								$file_name = sanitize_text_field( wp_unslash( $_FILES['file']['name'] ) );

								WP_Filesystem();
								global $wp_filesystem;
								$data = $wp_filesystem->get_contents( $image_file );

								$uploaded_bits = wp_upload_bits( $file_name, null, $data );

								if ( false !== $uploaded_bits['error'] ) {
									exit();
								} else {
									$uploaded_file             = $uploaded_bits['file'];
									$_SESSION['uploaded_file'] = $uploaded_bits['file'];

									$uploaded_url      = $uploaded_bits['url'];
									$uploaded_filetype = wp_check_filetype( basename( $uploaded_bits['file'] ), null );

									if ( ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) || ! empty( $_SERVER['SERVER_PORT'] ) && 443 === $_SERVER['SERVER_PORT'] ) {
										$uploaded_url = preg_replace( '/^http:/i', 'https:', $uploaded_url );
									}

									if ( isset( $_REQUEST['profile_image'] ) && 'true' === $_REQUEST['profile_image'] ) {
										// Image size check.
										if ( isset( $image[0] ) && isset( $image[1] ) ) {
											if ( $image[0] >= 175 && $image[1] >= 175 ) {
												print esc_url_raw( trim( str_replace( "\n", '', $uploaded_url ) ) );
											} else {
												print esc_html( 'Image size is too small' );
												unlink( $uploaded_bits['file'] );
											}
										} else {
											print esc_html( 'Invalid image provided' );
											unlink( $uploaded_bits['file'] );
										}
									} elseif ( isset( $_REQUEST['group_image'] ) && 'true' === $_REQUEST['group_image'] || isset( $_REQUEST['event_image'] ) && 'true' === $_REQUEST['event_image'] ) {
										if ( isset( $image[0] ) && isset( $image[1] ) ) {
											if ( $image[0] >= 703 && $image[1] >= 400 ) {
												print esc_url_raw( trime( str_replace( "\n", '', $uploaded_url ) ) );
											} else {
												print esc_html( 'Image size is too small' );
												unlink( $uploaded_bits['file'] );
											}
										} else {
											print 'Invalid image provided';
											unlink( $uploaded_bits['file'] );
										}
									} else {
										print esc_url_raw( trim( str_replace( "\n", '', $uploaded_url ) ) );
										unlink( $uploaded_bits['file'] );
									}
								}
							}
						}
					} else {
						print esc_html( "Image size to large ({$max_files_size_allowed} KB maximum)" );
					}
				}
			}
		}
	}

	die();
}

/**
 * Determines which section a user is on
 */
function mozilla_determine_site_section() {

	if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {
		$path_items = array_filter( explode( '/', esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) );

		if ( count( $path_items ) > 0 ) {
			$section = array_shift( array_values( $path_items ) );
			return $section;
		}
	}

	return false;
}

/**
 * Adds attribute to menu item
 *
 * @param array $attrs element attributes.
 * @param array $item current item.
 * @param array $args argumemnts.
 */
function mozilla_add_menu_attrs( $attrs, $item, $args ) {
	$attrs['class'] = 'menu-item__link';
	return $attrs;
}

/**
 * Initialize scripts
 */
function mozilla_init_scripts() {

	// Vendor scripts.
	wp_enqueue_script( 'dropzonejs', get_stylesheet_directory_uri() . '/js/vendor/dropzone.min.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/vendor/dropzone.min.js' ), false );
	wp_enqueue_script( 'autcomplete', get_stylesheet_directory_uri() . '/js/vendor/autocomplete.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/vendor/autocomplete.js' ), false );
	wp_enqueue_script( 'identicon', get_stylesheet_directory_uri() . '/js/vendor/identicon.js', array(), filemtime( get_template_directory() . '/js/vendor/identicon.js' ), false );
	wp_register_script( 'mapbox', 'https://api.mapbox.com/mapbox-gl-js/v1.4.1/mapbox-gl.js', array(), '1.4.1', false );
	wp_enqueue_script( 'mapbox' );
	wp_register_style( 'mapbox-css', 'https://api.mapbox.com/mapbox-gl-js/v1.4.1/mapbox-gl.css', array(), '1.4.1', false );
	wp_enqueue_style( 'mapbox-css' );

	// Custom scripts.
	wp_enqueue_script( 'groups', get_stylesheet_directory_uri() . '/js/groups.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/groups.js' ), false );
	wp_enqueue_script( 'events', get_stylesheet_directory_uri() . '/js/events.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/events.js' ), false );
	wp_enqueue_script( 'activities', get_stylesheet_directory_uri() . '/js/activities.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/activities.js' ), false );
	wp_enqueue_script( 'cleavejs', get_stylesheet_directory_uri() . '/js/vendor/cleave.min.js', array(), filemtime( get_template_directory() . '/js/vendor/cleave.min.js' ), false );
	wp_enqueue_script( 'nav', get_stylesheet_directory_uri() . '/js/nav.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/nav.js' ), false );
	wp_enqueue_script( 'profile', get_stylesheet_directory_uri() . '/js/profile.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/profile.js' ), false );
	wp_enqueue_script( 'lightbox', get_stylesheet_directory_uri() . '/js/lightbox.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/lightbox.js' ), false );
	wp_enqueue_script( 'gdpr', get_stylesheet_directory_uri() . '/js/gdpr.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/gdpr.js' ), false );
	wp_enqueue_script( 'dropzone', get_stylesheet_directory_uri() . '/js/dropzone.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/dropzone.js' ), false );
	wp_enqueue_script( 'newsletter', get_stylesheet_directory_uri() . '/js/newsletter.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/newsletter.js' ), false );
	wp_enqueue_script( 'mailchimp', get_stylesheet_directory_uri() . '/js/campaigns.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/campaigns.js' ), false );

	$google_analytics_id = get_option( 'google_analytics_id' );
	if ( $google_analytics_id ) {
		$url = esc_url( "https://www.googletagmanager.com/gtag/js?id={$google_analytics_id}" );
		wp_enqueue_script( 'google-analytics', $url, array(), '1.0', false );
		$script = '
		<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag("js", new Date());
		gtag("config", "' . esc_attr( $google_analytics_id ) . '");
		</script>';
	}

	wp_add_inline_script( 'google-analytics', $script, 'after' );
}

/**
 * Initialize admin scripts
 */
function mozilla_init_admin_scripts() {
	$screen = get_current_screen();
	if ( strtolower( $screen->id ) === 'toplevel_page_bp-groups' ) {
		wp_enqueue_style( 'styles', get_stylesheet_directory_uri() . '/style.css', false, '1.0.0' );
		wp_enqueue_script( 'groups', get_stylesheet_directory_uri() . '/js/admin.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/admin.js' ), false );
	}
	if ( strtolower( $screen->id ) === 'toplevel_page_events-export-panel' ) {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-ui-css', '/wp-content/plugins/events-manager/includes/css/jquery-ui.min.css', false, '1.0.0' );
		wp_enqueue_script( 'date', get_stylesheet_directory_uri() . '/js/date.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/date.js' ), false );
	}
}


/**
 * Removes login header
 */
function mozilla_remove_admin_login_header() {
	remove_action( 'wp_head', '_admin_bar_bump_cb' );
}

/**
 * Theme settings
 */
function mozilla_theme_settings() {
	$theme_dir = get_template_directory();

	if ( current_user_can( 'manage_options' ) && ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
		if ( isset( $_POST['admin_nonce_field'] ) ) {
			$nonce = trim( sanitize_text_field( wp_unslash( $_POST['admin_nonce_field'] ) ) );

			if ( wp_verify_nonce( $nonce, 'admin_nonce' ) ) {

				if ( isset( $_POST['github_link'] ) ) {
					$github_link = sanitize_text_field( wp_unslash( $_POST['github_link'] ) );
					update_option( 'github_link', $github_link );
				}

				if ( isset( $_POST['community_discourse'] ) ) {
					$community_discourse = sanitize_text_field( wp_unslash( $_POST['community_discourse'] ) );
					update_option( 'community_discourse', $community_discourse );
				}

				if ( isset( $_POST['google_analytics_id'] ) ) {
					$google_analytics_id = sanitize_text_field( wp_unslash( $_POST['google_analytics_id'] ) );
					update_option( 'google_analytics_id', $google_analytics_id );
				}

				if ( isset( $_POST['google_analytics_sri'] ) ) {
					$google_analytics_sri = sanitize_text_field( wp_unslash( $_POST['google_analytics_sri'] ) );
					update_option( 'google_analytics_sri', $google_analytics_sri );
				}

				if ( isset( $_POST['default_open_graph_title'] ) ) {
					$default_open_graph_title = sanitize_text_field( wp_unslash( $_POST['default_open_graph_title'] ) );
					update_option( 'default_open_graph_title', $default_open_graph_title );
				}

				if ( isset( $_POST['default_open_graph_desc'] ) ) {
					$default_open_graph_desc = sanitize_text_field( wp_unslash( $_POST['default_open_graph_desc'] ) );
					update_option( 'default_open_graph_desc', $default_open_graph_desc );
				}

				if ( isset( $_POST['image_max_filesize'] ) ) {
					$image_max_filesize = sanitize_text_field( wp_unslash( $_POST['image_max_filesize'] ) );
					update_option( 'image_max_filesize', intval( $image_max_filesize ) );
				}

				if ( isset( $_POST['error_404_title'] ) ) {
					$error_404_title = sanitize_text_field( wp_unslash( $_POST['error_404_title'] ) );
					update_option( 'error_404_title', $error_404_title );
				}

				if ( isset( $_POST['error_404_copy'] ) ) {
					$error_404_copy = sanitize_text_field( wp_unslash( $_POST['error_404_copy'] ) );
					update_option( 'error_404_copy', $error_404_copy );
				}

				if ( isset( $_POST['discourse_api_key'] ) ) {
					$discourse_api_key = sanitize_text_field( wp_unslash( $_POST['discourse_api_key'] ) );
					update_option( 'discourse_api_key', $discourse_api_key );
				}

				if ( isset( $_POST['discourse_api_url'] ) ) {
					$discourse_api_url = sanitize_text_field( wp_unslash( $_POST['discourse_api_url'] ) );
					update_option( 'discourse_api_url', $discourse_api_url );
				}

				if ( isset( $_POST['discourse_url'] ) ) {
					$discourse_url = sanitize_text_field( wp_unslash( $_POST['discourse_url'] ) );
					update_option( 'discourse_url', $discourse_url );
				}

				if ( isset( $_POST['mapbox'] ) ) {
					$mapbox = sanitize_text_field( wp_unslash( $_POST['mapbox'] ) );
					update_option( 'mapbox', $mapbox );
				}

				if ( isset( $_POST['report_email'] ) ) {
					$report_email = sanitize_email( wp_unslash( $_POST['report_email'] ) );
					update_option( 'report_email', $report_email );
				}

				if ( isset( $_POST['mailchimp'] ) ) {
					$mailchimp = sanitize_text_field( wp_unslash( $_POST['mailchimp'] ) );
					update_option( 'mailchimp', $mailchimp );
				}

				if ( isset( $_POST['company'] ) ) {
					$company = sanitize_text_field( wp_unslash( $_POST['company'] ) );
					update_option( 'company', $company );
				}

				if ( isset( $_POST['address'] ) ) {
					$address = sanitize_text_field( wp_unslash( $_POST['address'] ) );
					update_option( 'address', $address );
				}

				if ( isset( $_POST['city'] ) ) {
					$city = sanitize_text_field( wp_unslash( $_POST['city'] ) );
					update_option( 'city', $city );
				}

				if ( isset( $_POST['state'] ) ) {
					$state = sanitize_text_field( wp_unslash( $_POST['state'] ) );
					update_option( 'state', $state );
				}

				if ( isset( $_POST['zip'] ) ) {
					$zip = sanitize_text_field( wp_unslash( $_POST['zip'] ) );
					update_option( 'zip', $zip );
				}

				if ( isset( $_POST['country'] ) ) {
					$country = sanitize_text_field( wp_unslash( $_POST['country'] ) );
					update_option( 'country', $country );
				}

				if ( isset( $_POST['phone'] ) ) {
					$phone = sanitize_text_field( wp_unslash( $_POST['phone'] ) );
					update_option( 'phone', $phone );
				}
			}
		}
	}

	$options = wp_load_alloptions();
	include "{$theme_dir}/templates/settings.php";
}


/**
 * Include event export template
 */
function mozilla_export_events_control() {
	$theme_dir = get_template_directory();
	include "{$theme_dir}/templates/event-export.php";
}

/**
 * Add new menu item
 */
function mozilla_add_menu_item() {
	add_menu_page( 'Mozilla Settings', 'Mozilla Settings', 'manage_options', 'theme-panel', 'mozilla_theme_settings', null, 99 );
	add_menu_page( 'Mozilla Export Events', 'Export Events', 'manage_options', 'events-export-panel', 'mozilla_export_events_control', 'dashicons-media-spreadsheet', 99 );
}

/**
 * Check if current user is an admin
 */
function mozilla_is_site_admin() {
	return in_array( 'administrator', wp_get_current_user()->roles, true );
}

/**
 * Update body class of page
 *
 * @param array $classes classes for body.
 */
function mozilla_update_body_class( $classes ) {
	$classes[] = 'body';
	return $classes;
}

/**
 * Add menu classes
 *
 * @param array  $classes classes for item.
 * @param object $item menu item.
 * @param array  $args arguments.
 */
function mozilla_menu_class( $classes, $item, $args ) {

	if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {
		$request_uri = trim( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );

		$path_items = array_filter( explode( '/', $request_uri ) );
		$menu_url   = strtolower( str_replace( '/', '', $item->url ) );

		if ( count( $path_items ) > 0 ) {
			if ( strtolower( $path_items[1] ) === $menu_url ) {
				$item->current = true;
				$classes[]     = 'menu-item--active';
			}
		}
	}

	return $classes;
}

/**
 * Add query variable
 *
 * @param array $vars variables.
 */
function mozilla_add_query_vars_filter( $vars ) {
	$vars[] = 'view';
	$vars[] = 'country';
	$vars[] = 'tag';
	$vars[] = 'a';

	return $vars;
}

/**
 * Match taxonomy
 */
function mozilla_match_categories() {
	$cat_terms = get_terms( EM_TAXONOMY_CATEGORY, array( 'hide_empty' => false ) );
	$wp_terms  = get_terms( 'post_tag', array( 'hide_empty' => false ) );

	$cat_terms_name = array_map(
		function( $n ) {
			return $n->name;
		},
		$cat_terms
	);

	$wp_terms = array_map(
		function( $n ) {
			return $n->name;
		},
		$wp_terms
	);

	foreach ( $wp_terms as $wp_term ) {
		if ( ! in_array( $wp_term, $cat_terms_name, true ) ) {
			wp_insert_term( $wp_term, EM_TAXONOMY_CATEGORY );
		}
	}

	foreach ( $cat_terms as $cat_term ) {
		if ( ! in_array( $cat_term->name, $wp_terms, true ) ) {
			wp_delete_term( $cat_term->term_id, EM_TAXONOMY_CATEGORY );
		}
	}
}

/**
 * Redirect non admins
 */
function mozilla_redirect_admin() {
	if ( ( ! current_user_can( 'manage_options' ) || current_user_can( 'subscriber' ) ) && ! empty( $_SERVER['PHP_SELF'] ) && '/wp-admin/admin-ajax.php' !== sanitize_text_field( wp_unslash( $_SERVER['PHP_SELF'] ) ) ) {
		wp_safe_redirect( '/' );
		exit();
	}
}

/**
 * Verify URL
 *
 * @param string  $url url to verify.
 * @param boolean $secure is secure URL.
 */
function mozilla_verify_url( $url, $secure ) {

	if ( preg_match( '/\.[a-zA-Z]{2,4}\b/', $url ) ) {
		$parts = wp_parse_url( $url );
		if ( ! isset( $parts['scheme'] ) ) {
			if ( $secure ) {
				$url = 'https://' . $url;
			} else {
				$url = 'http://' . $url;
			}
		}
	}

	if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
		return $url;
	}

	return false;
}

/**
 * Add columns to admin
 *
 * @param array $columns table column.
 */
function mozilla_add_group_columns( $columns ) {

	$columns['group_created'] = __( 'Group Created On', 'community-portal' );
	$columns['admins']        = __( 'Admins', 'community-portal' );
	$columns['events']        = __( 'Events', 'community-portal' );
	$columns['verified_date'] = __( 'Group Verified On', 'community-portal' );

	return $columns;

}

/**
 * Add additional info table
 *
 * @param string $retval return value.
 * @param string $column_name name of column.
 * @param array  $item contents of column.
 */
function mozilla_group_addional_column_info( $retval = '', $column_name, $item ) {
	if ( 'group_created' !== $column_name
		&& 'events' !== $column_name
		&& 'admins' !== $column_name
		&& 'verified_date' !== $column_name ) {
		return $retval;
	}

	switch ( $column_name ) {
		case 'group_created':
			if ( isset( $item['date_created'] ) ) {
				if ( strtotime( $item['date_created'] ) < strtotime( '-1 month' ) ) {
					$class = 'admin__group-status--passed';
				} else {
					$class = 'admin__group-status--new';
				}

				return wp_kses( "<div class=\"{$class}\">{$item['date_created']}</div>", array( 'div' => array( 'class' => array() ) ) );
			}

			break;
		case 'events':
			$args = array(
				'group' => $item['id'],
				'scope' => 'all',
			);

			$events = EM_Events::get( $args );
			return count( $events );
		case 'admins':
			$admins = groups_get_group_admins( $item['id'] );
			return count( $admins );
		case 'verified_date':
			$group_meta = groups_get_groupmeta( $item['id'], 'meta' );

			if ( isset( $group_meta['verified_date'] ) ) {
				$date_check = strtotime( '+1 year', $group_meta['verified_date'] );

				if ( $date_check < time() ) {
					$class = 'admin__group-status--red';
				} else {
					$class = 'admin__group-status--new';
				}

				$verified_date = gmdate( 'Y-m-d H:i:s', $group_meta['verified_date'] );
				return wp_kses( "<div class=\"{$class}\">{$verified_date}</div>", array( 'div' => array( 'class' => array() ) ) );
			} else {
				return '-';
			}
	}

	return '-';
}

/**
 * Save post hook
 *
 * @param integer $post_id post ID.
 * @param object  $post post object.
 * @param boolean $update are we updating.
 */
function mozilla_save_post( $post_id, $post, $update ) {

	// @TODO: Add nonce check.
	if ( 'event' === $post->post_type && $update ) {

		$user              = wp_get_current_user();
		$event_update_meta = get_post_meta( $post->ID, 'event-meta' );
		$event             = new stdClass();

		if ( isset( $event_update_meta[0]->discourse_group_id ) ) {
			$event->discourse_group_id = $event_update_meta[0]->discourse_group_id;
		}

		if ( isset( $event_update_meta[0]->discourse_group_name ) ) {
			$event->discourse_group_name = $event_update_meta[0]->discourse_group_name;
		}

		if ( isset( $event_update_meta[0]->discourse_group_description ) ) {
			$event->discourse_group_description = $event_update_meta[0]->discourse_group_description;
		}

		if ( isset( $event_update_meta[0]->discourse_group_users ) ) {
			$event->discourse_group_users = $event_update_meta[0]->discourse_group_users;
		}

		if ( ! empty( $_POST['image_url'] ) ) {
			$event_image_url = esc_url_raw( wp_unslash( $_POST['image_url'] ) );
		} else {
			$event_image_url = '';
		}

		if ( ! empty( $_POST['location-type'] ) ) {
			$event_location_type = sanitize_text_field( wp_unslash( $_POST['location-type'] ) );
		} else {
			$event_location_type = '';
		}

		if ( ! empty( $_POST['event_external_link'] ) ) {
			$event_external_link = esc_url_raw( wp_unslash( $_POST['event_external_link'] ) );
		} else {
			$event_external_link = '';
		}

		$event->image_url           = $event_image_url;
		$event->location_type       = $event_location_type;
		$event->external_url        = $event_external_link;
		$event->language            = isset( $_POST['language'] ) ? sanitize_text_field( wp_unslash( $_POST['language'] ) ) : '';
		$event->goal                = isset( $_POST['goal'] ) ? sanitize_text_field( wp_unslash( $_POST['goal'] ) ) : '';
		$event->projected_attendees = isset( $_POST['projected-attendees'] ) ? intval( sanitize_text_field( wp_unslash( $_POST['projected-attendees'] ) ) ) : '';

		if ( isset( $_POST['initiative_id'] ) ) {
			$initiative_id = intval( sanitize_text_field( wp_unslash( $_POST['initiative_id'] ) ) );

			if ( $initiative_id > 0 ) {
				$initiative = get_post( $initiative_id );
				if ( $initiative && ( 'campaign' === $initiative->post_type || 'activity' === $initiative->post_type ) ) {
					$event->initiative = $initiative_id;
				}
			}
		}

		$discourse_api_data = array();

		$discourse_api_data['name']        = $post->post_name;
		$discourse_api_data['description'] = $post->post_content;

		if ( ! empty( $event_update_meta ) && isset( $event_update_meta[0]->discourse_group_id ) ) {
			$discourse_api_data['group_id'] = $event_update_meta[0]->discourse_group_id;
			$discourse_event                = mozilla_get_discourse_info( $post_id, 'event' );
			$discourse_api_data['users']    = $discourse_event['discourse_group_users'];
			$discourse_group                = mozilla_discourse_api( 'groups', $discourse_api_data, 'patch' );
		}

		if ( $discourse_group ) {
			$event->discourse_log = $discourse_group;
		}

		update_post_meta( $post->ID, 'event-meta', $event );

	}
}

/**
 * Check ACF Field for Mailchimp when saving campaigns
 *
 * @param integer $post_id post ID.
 */
function mozilla_acf_save_post( $post_id ) {

	// Check to see if we are autosaving.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	$post_type = get_post_type( $post_id );

	// First check that we are dealing with campaigns.
	if ( 'campaign' === $post_type ) {

		$prev_published        = get_post_meta( $post_id, 'prev_published', true );
		$mailchimp_integration = get_field( 'mailchimp_integration', $post_id );

		if ( empty( $prev_published ) && $mailchimp_integration ) {
			$post = get_post( $post_id );
			update_post_meta( $post_id, 'prev_published', true );

			mozilla_create_mailchimp_list( $post );
		}
	}

}

/**
 * When changing status for a post
 *
 * @param string $new_status the new status.
 * @param string $old_status the old status.
 * @param object $post post.
 */
function mozilla_post_status_transition( $new_status, $old_status, $post ) {

	// Support for campaigns already published.
	// Set the required meta here if the old status is publish.
	if ( 'campaign' === $post->post_type && 'publish' === $old_status ) {
		update_post_meta( $post->ID, 'prev_published', true );
	}

	if ( 'publish' === $new_status ) {

		if ( 'event' === $post->post_type && 'publish' !== $old_status ) {

			$user                       = wp_get_current_user();
			$event                      = new stdClass();
			$event->image_url           = esc_url_raw( $_POST['image_url'] );
			$event->location_type       = sanitize_text_field( $_POST['location-type'] );
			$event->external_url        = esc_url_raw( $_POST['event_external_link'] );
			$event->language            = $_POST['language'] ? sanitize_text_field( $_POST['language'] ) : '';
			$event->goal                = $_POST['goal'] ? sanitize_text_field( $_POST['goal'] ) : '';
			$event->projected_attendees = $_POST['projected-attendees'] ? intval( $_POST['projected-attendees'] ) : '';

			if ( isset( $_POST['initiative_id'] ) && strlen( $_POST['initiative_id'] ) > 0 ) {
				$initiative_id = intval( $_POST['initiative_id'] );
				$initiative    = get_post( $initiative_id );
				if ( $initiative && ( $initiative->post_type === 'campaign' || $initiative->post_type === 'activity' ) ) {
					$event->initiative = $initiative_id;
				}
			}

			$discourse_api_data                = array();
			$discourse_api_data['name']        = $post->post_name;
			$discourse_api_data['description'] = $post->post_content;
			$auth0Ids                          = array();
			$auth0Ids[]                        = mozilla_get_user_auth0( $user->ID );
			$discourse_api_data['users']       = $auth0Ids;
			$discourse_group                   = mozilla_discourse_api( 'groups', $discourse_api_data, 'post' );

			if ( $discourse_group ) {
				if ( isset( $discourse_group->id ) ) {
					$event->discourse_group_id = $discourse_group->id;
				} else {
					$event->discourse_log = $discourse_group;
				}
			}

			update_post_meta( $post->ID, 'event-meta', $event );

		}
	}
}

function mozilla_export_users() {

	// Only admins.
	if ( ! is_admin() && in_array( 'administrator', wp_get_current_user()->roles ) === false ) {
		return;
	}

	$theme_directory = get_template_directory();
	include "{$theme_directory}/languages.php";
	include "{$theme_directory}/countries.php";

	$users = get_users( array() );

	header( 'Content-Type: text/csv' );
	header( 'Content-Disposition: attachment; filename=users.csv;' );

	// CSV Column Titles.
	print "first name, last name, email,date registered, languages, country\n ";
	foreach ( $users as $user ) {
		$meta             = get_user_meta( $user->ID );
		$community_fields = isset( $meta['community-meta-fields'][0] ) ? unserialize( $meta['community-meta-fields'][0] ) : array();

		$first_name     = isset( $meta['first_name'][0] ) ? $meta['first_name'][0] : '';
		$last_name      = isset( $meta['last_name'][0] ) ? $meta['last_name'][0] : '';
		$user_languages = isset( $community_fields['languages'] ) && sizeof( $community_fields['languages'] ) > 0 ? $community_fields['languages'] : array();

		$language_string = '';
		foreach ( $user_languages as $language_code ) {
			if ( strlen( $language_code ) > 0 ) {
				$language_string .= "{$languages[$language_code]},";
			}
		}

		// Remove ending comma.
		$language_string = rtrim( $language_string, ',' );

		$country = isset( $community_fields['country'] ) && strlen( $community_fields['country'] ) > 0 ? $countries[ $community_fields['country'] ] : '';
		$date    = date( 'd/m/Y', strtotime( $user->data->user_registered ) );

		// Print out CSV row.
		print "{$first_name},{$last_name},{$user->data->user_email},{$date},\"{$language_string}\",{$country}\n";
	}
	die();
}

/**
 * Hide the emails in menus
 *
 * @param array $items items of the menu.
 * @param array $args arguments.
 */
function mozilla_hide_menu_emails( $items, $args ) {

	foreach ( $items as $index => $item ) {
		if ( false !== stripos( $item->url, 'mailto:' ) && ! is_user_logged_in() ) {
			unset( $items[ $index ] );
		}

		$index++;
	}

	return $items;
}

/**
 * Updates the inline google analytics code and adds SRI
 *
 * @param string $html The code.
 * @param string $handle The name of the code.
 */
function mozilla_update_script_attributes( $html, $handle ) {
	if ( 'google-analytics' === $handle ) {
		$google_analytics_sri = esc_attr( get_option( 'google_analytics_sri' ) );

		if ( $google_analytics_sri ) {
			$needle = "type='text/javascript'";
			$pos    = strpos( $html, $needle );
			return substr_replace( $html, "type='text/javascript' async integrity='{$google_analytics_sri}' crossorigin='anonymous'", $pos, strlen( $needle ) );
		}
	}

	return $html;

}


