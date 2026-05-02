<?php
/**
 * User Profile Custom Fields.
 *
 * Adds the QuestionHub phone number to the WordPress admin user profile screen.
 *
 * @package QuestionHub\Admin\Inc\Users
 * @since   1.0.0
 */

namespace QuestionHub\Admin\Inc\Users;

use QuestionHub\Frontend\Inc\Auth\UserMeta;
use QuestionHub\Frontend\Inc\Helpers\Sanitizer;

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
		<h2><?php esc_html_e( 'QuestionHub Profile', 'questionhub' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><label for="questionhub_phone"><?php esc_html_e( 'Phone Number', 'questionhub' ); ?></label></th>
				<td>
					<input type="text" name="questionhub_phone" id="questionhub_phone" value="<?php echo esc_attr( $phone ); ?>" class="regular-text" />
					<p class="description"><?php esc_html_e( 'The phone number used for login and notifications.', 'questionhub' ); ?></p>
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

		if ( isset( $_POST['questionhub_phone'] ) ) {
			$phone = Sanitizer::phone( wp_unslash( $_POST['questionhub_phone'] ) );
			
			// Only update if it's unique or belongs to this user.
			$existing_user = UserMeta::find_user_by_phone( $phone );
			if ( ! $existing_user || $existing_user === $user_id ) {
				UserMeta::set_phone( $user_id, $phone );
			}
		}
	}
}
