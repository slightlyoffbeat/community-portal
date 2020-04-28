<?php
/**
 * Featured Image input for events
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>

<div class="event-creator__container">
	<label class="event-creator__label" for="event-image"><?php esc_html_e( 'Event Image', 'community-portal' ); ?></label>

	<?php
	if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( sanitize_key( $_REQUEST['nonce'] ), 'edit-event' ) && isset( $_REQUEST['event_id'] ) ) {
		$event      = em_get_event( sanitize_key( $_REQUEST['event_id'] ) );
		$event_meta = get_post_meta( $event->post_id, 'event-meta' );

		$img_url = $event_meta[0]->image_url;

		if ( ( ! empty( $_SERVER['HTTPS'] ) && ! empty( $_SERVER['SERVER_PORT'] ) && 'off' !== $_SERVER['HTTPS'] ) || 443 === $_SERVER['SERVER_PORT'] ) {
			$img_url = preg_replace( '/^http:/i', 'https:', $img_url );
		}
	}

	?>
	<div id="dropzone-photo-uploader" class="event-creator__image-upload
	<?php
	if ( isset( $img_url ) && strlen( $img_url ) > 0 ) :
		?>
		event-creator__image-upload--done<?php endif; ?>"  style="
		<?php
		if ( isset( $img_url ) ) {
			print esc_attr( "background-image: url('{$img_url}')" );
		} else {
			print esc_attr( "background-size: '75px 75px'" ); }
		?>
		" > 
		<button id="image-delete" type="button" class="btn event-creator__image-delete <?php echo ( ! isset( $img_url ) || strlen( $img_url ) === 0 ) ? esc_attr( 'hidden' ) : null; ?>">
			<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
				<circle cx="20" cy="20" r="20" fill="white"/>
				<path d="M29 11L11 29" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M11 11L29 29" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</button>
		<div class="dz-message" data-dz-message="">
			<div class="event-creator__image-instructions">
				<div class="form__error-container">
					<div class="form__error form__error--image"></div>
				</div>
				<button id="dropzone-trigger" type="button" class="dropzone__image-instructions event-creator__image-instructions">
					<?php esc_html_e( 'Click or drag a .PNG or .JPG above. Min dimensions 703px by 400px', 'community-portal' ); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<input type="hidden" name="image_url" id="image-url" value="
<?php
if ( $img_url ) {
	print esc_attr( $img_url );
} else {
	esc_attr( '' ); }
?>
" />
