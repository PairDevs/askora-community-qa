<?php
/**
 * User Profile Custom Fields.
 *
 * Adds the Askora phone number to the WordPress admin user profile screen.
 *
 * @package ASKORA\Admin\Inc\Users
 * @since   1.0.0
 */

namespace ASKORA\Admin\Inc\Users;

use ASKORA\Frontend\Inc\Auth\UserMeta;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Profile {

	public function __construct() {
		add_action( 'show_user_profile', [ $this, 'render_phone_field' ] );
		add_action( 'edit_user_profile', [ $this, 'render_phone_field' ] );

		add_action( 'personal_options_update', [ $this, 'save_phone_field' ] );
		add_action( 'edit_user_profile_update', [ $this, 'save_phone_field' ] );
	}

	/**
	 * Renders the phone field in the user profile.
	 *
	 * @param \WP_User $user The user object.
	 */
	public function render_phone_field( $user ): void {
		$phone = UserMeta::get_phone( $user->ID );
		?>
		<h2><?php esc_html_e( 'Askora Profile', 'askora-community-qa' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><label for="askora_phone"><?php esc_html_e( 'Phone Number', 'askora-community-qa' ); ?></label></th>
				<td>
					<input type="text" name="askora_phone" id="askora_phone" value="<?php echo esc_attr( $phone ); ?>" class="regular-text" />
					<p class="description"><?php esc_html_e( 'The phone number used for login and notifications.', 'askora-community-qa' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Saves the phone field.
	 *
	 * @param int $user_id The user ID.
	 */
	public function save_phone_field( $user_id ): void {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		if ( ! isset( $_POST['askora_user_phone_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['askora_user_phone_nonce'] ) ), 'askora_save_user_phone' ) ) {
			return;
		}

		if ( isset( $_POST['askora_phone'] ) ) {
			$phone = preg_replace( '/[^0-9+\-() ]/', '', sanitize_text_field( wp_unslash( $_POST['askora_phone'] ) ) );
			
			// Only update if it's unique or belongs to this user.
			$existing_user = UserMeta::find_user_by_phone( $phone );
			if ( ! $existing_user || $existing_user === $user_id ) {
				UserMeta::set_phone( $user_id, $phone );
			}
		}
	}
}
