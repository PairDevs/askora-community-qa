<?php
/**
 * Template: answer-item.php
 * Single answer/reply row — used by shortcode list AND answers-template.php.
 *
 * @package ASKORA
 * @var WP_Comment $askora_comment
 * @var int        $askora_post_id
 * @var int        $askora_best_id   Comment ID of the best answer (0 if none).
 * @var array      $askora_settings  Plugin settings.
 */

defined( 'ABSPATH' ) || exit;

use ASKORA\Frontend\Inc\Comments\Badge;

$askora_user_id     = (int) $askora_comment->user_id;
$askora_avatar      = get_avatar( $askora_comment, 40, '', '', [ 'class' => 'askora-avatar' ] );
$askora_author      = esc_html( $askora_comment->comment_author );
$askora_badge       = Badge::get( $askora_user_id, $askora_post_id );
$askora_votes       = (int) get_comment_meta( $askora_comment->comment_ID, '_askora_answer_votes', true );
$askora_is_best     = (int) $askora_comment->comment_ID === $askora_best_id;
$askora_is_verified = (bool) get_comment_meta( $askora_comment->comment_ID, '_askora_verified', true );
$askora_is_admin    = current_user_can( 'manage_options' );
$askora_can_best    = \ASKORA\Frontend\Inc\Helpers\Permission::can_mark_best_answer( $askora_post_id );
$askora_enable_voting = ! empty( $askora_settings['enable_voting'] );
$askora_enable_best   = ! empty( $askora_settings['enable_best_answer'] );
?>
<div class="askora-answer-item qh-answer-item <?php echo esc_attr( $askora_is_best ? 'askora-best-answer qh-answer-best' : '' ); ?> <?php echo esc_attr( $askora_is_verified ? 'qh-answer-verified' : '' ); ?>"
	 id="answer-<?php echo esc_attr( $askora_comment->comment_ID ); ?>">

	<!-- Verified ribbon — top-right corner -->
	<?php if ( $askora_is_verified ) : ?>
	<div class="qh-verified-ribbon" aria-label="<?php esc_attr_e( 'Admin Verified', 'askora-community-qa' ); ?>">
		<span class="dashicons dashicons-yes"></span>
		<?php esc_html_e( 'Verified', 'askora-community-qa' ); ?>
	</div>
	<?php endif; ?>

	<?php if ( $askora_is_best ) : ?>
	<div class="askora-best-answer-badge qh-accepted-banner">
		<span class="dashicons dashicons-yes-alt"></span>
		<?php esc_html_e( 'Best Answer', 'askora-community-qa' ); ?>
	</div>
	<?php endif; ?>

	<div class="askora-answer-header qh-answer-author-row">
		<?php echo wp_kses_post( $askora_avatar ); ?>
		<div class="askora-answer-meta qh-answer-author-info">
			<span class="askora-meta-author qh-answer-author-name"><?php echo esc_html( $askora_author ); ?></span>
			<?php echo wp_kses_post( $askora_badge ); ?>
			<span class="askora-meta-date qh-answer-date">
				<?php echo esc_html( human_time_diff( strtotime( $askora_comment->comment_date ), current_time( 'U' ) ) . ' ' . __( 'ago', 'askora-community-qa' ) ); ?>
			</span>
		</div>
	</div>

	<div class="askora-answer-content qh-answer-text">
		<?php echo wp_kses_post( $askora_comment->comment_content ); ?>
	</div>

	<div class="askora-answer-actions qh-answer-actions-row">
		<?php if ( $askora_enable_voting ) : ?>
		<button class="askora-vote-answer-btn qh-vote-upbtn qh-vote-answer-btn askora-btn-icon"
				data-id="<?php echo esc_attr( $askora_comment->comment_ID ); ?>"
				data-type="answer">
			<span class="dashicons dashicons-thumbs-up"></span>
			<span class="askora-vote-count qh-vote-count"><?php echo esc_html( $askora_votes ); ?></span>
		</button>
		<?php endif; ?>

		<?php if ( $askora_enable_best && $askora_can_best && ! $askora_is_best ) : ?>
		<button class="askora-mark-best-btn qh-mark-best-btn"
				data-comment-id="<?php echo esc_attr( $askora_comment->comment_ID ); ?>"
				data-post-id="<?php echo esc_attr( $askora_post_id ); ?>">
			<span class="dashicons dashicons-yes-alt"></span>
			<?php esc_html_e( 'Mark as Best Answer', 'askora-community-qa' ); ?>
		</button>
		<?php endif; ?>

		<?php if ( $askora_is_admin ) : ?>
		<button class="qh-verify-answer-btn <?php echo esc_attr( $askora_is_verified ? 'qh-verify-active' : '' ); ?>"
				data-comment-id="<?php echo esc_attr( $askora_comment->comment_ID ); ?>"
				title="<?php echo esc_attr( $askora_is_verified ? __( 'Remove verification', 'askora-community-qa' ) : __( 'Mark as Admin Verified', 'askora-community-qa' ) ); ?>">
			<span class="dashicons dashicons-<?php echo esc_attr( $askora_is_verified ? 'dismiss' : 'awards' ); ?>"></span>
			<span class="qh-verify-label">
				<?php echo esc_html( $askora_is_verified ? __( 'Remove Verification', 'askora-community-qa' ) : __( 'Verify Answer', 'askora-community-qa' ) ); ?>
			</span>
		</button>
		<?php endif; ?>
	</div>
</div>
