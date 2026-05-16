<?php
/**
 * Template: answer-item.php
 * Single answer/reply row — used by shortcode list AND answers-template.php.
 *
 * @package QuestionHub
 * @var WP_Comment $questionhub_comment
 * @var int        $questionhub_post_id
 * @var int        $questionhub_best_id   Comment ID of the best answer (0 if none).
 * @var array      $questionhub_settings  Plugin settings.
 */

defined( 'ABSPATH' ) || exit;

use QuestionHub\Frontend\Inc\Comments\Badge;

$questionhub_user_id     = (int) $questionhub_comment->user_id;
$questionhub_avatar      = get_avatar( $questionhub_comment, 40, '', '', [ 'class' => 'questionhub-avatar' ] );
$questionhub_author      = esc_html( $questionhub_comment->comment_author );
$questionhub_badge       = Badge::get( $questionhub_user_id, $questionhub_post_id );
$questionhub_votes       = (int) get_comment_meta( $questionhub_comment->comment_ID, '_questionhub_answer_votes', true );
$questionhub_is_best     = (int) $questionhub_comment->comment_ID === $questionhub_best_id;
$questionhub_is_verified = (bool) get_comment_meta( $questionhub_comment->comment_ID, '_questionhub_verified', true );
$questionhub_is_admin    = current_user_can( 'manage_options' );
$questionhub_can_best    = \QuestionHub\Frontend\Inc\Helpers\Permission::can_mark_best_answer( $questionhub_post_id );
$questionhub_enable_voting = ! empty( $questionhub_settings['enable_voting'] );
$questionhub_enable_best   = ! empty( $questionhub_settings['enable_best_answer'] );
?>
<div class="questionhub-answer-item qh-answer-item <?php echo esc_attr( $questionhub_is_best ? 'questionhub-best-answer qh-answer-best' : '' ); ?> <?php echo esc_attr( $questionhub_is_verified ? 'qh-answer-verified' : '' ); ?>"
	 id="answer-<?php echo esc_attr( $questionhub_comment->comment_ID ); ?>">

	<!-- Verified ribbon — top-right corner -->
	<?php if ( $questionhub_is_verified ) : ?>
	<div class="qh-verified-ribbon" aria-label="<?php esc_attr_e( 'Admin Verified', 'questionhub' ); ?>">
		<span class="dashicons dashicons-yes"></span>
		<?php esc_html_e( 'Verified', 'questionhub' ); ?>
	</div>
	<?php endif; ?>

	<?php if ( $questionhub_is_best ) : ?>
	<div class="questionhub-best-answer-badge qh-accepted-banner">
		<span class="dashicons dashicons-yes-alt"></span>
		<?php esc_html_e( 'Best Answer', 'questionhub' ); ?>
	</div>
	<?php endif; ?>

	<div class="questionhub-answer-header qh-answer-author-row">
		<?php echo wp_kses_post( $questionhub_avatar ); ?>
		<div class="questionhub-answer-meta qh-answer-author-info">
			<span class="questionhub-meta-author qh-answer-author-name"><?php echo esc_html( $questionhub_author ); ?></span>
			<?php echo wp_kses_post( $questionhub_badge ); ?>
			<span class="questionhub-meta-date qh-answer-date">
				<?php echo esc_html( human_time_diff( strtotime( $questionhub_comment->comment_date ), current_time( 'U' ) ) . ' ' . __( 'ago', 'questionhub' ) ); ?>
			</span>
		</div>
	</div>

	<div class="questionhub-answer-content qh-answer-text">
		<?php echo wp_kses_post( $questionhub_comment->comment_content ); ?>
	</div>

	<div class="questionhub-answer-actions qh-answer-actions-row">
		<?php if ( $questionhub_enable_voting ) : ?>
		<button class="questionhub-vote-answer-btn qh-vote-upbtn qh-vote-answer-btn questionhub-btn-icon"
				data-id="<?php echo esc_attr( $questionhub_comment->comment_ID ); ?>"
				data-type="answer">
			<span class="dashicons dashicons-thumbs-up"></span>
			<span class="questionhub-vote-count qh-vote-count"><?php echo esc_html( $questionhub_votes ); ?></span>
		</button>
		<?php endif; ?>

		<?php if ( $questionhub_enable_best && $questionhub_can_best && ! $questionhub_is_best ) : ?>
		<button class="questionhub-mark-best-btn qh-mark-best-btn"
				data-comment-id="<?php echo esc_attr( $questionhub_comment->comment_ID ); ?>"
				data-post-id="<?php echo esc_attr( $questionhub_post_id ); ?>">
			<span class="dashicons dashicons-yes-alt"></span>
			<?php esc_html_e( 'Mark as Best Answer', 'questionhub' ); ?>
		</button>
		<?php endif; ?>

		<?php if ( $questionhub_is_admin ) : ?>
		<button class="qh-verify-answer-btn <?php echo esc_attr( $questionhub_is_verified ? 'qh-verify-active' : '' ); ?>"
				data-comment-id="<?php echo esc_attr( $questionhub_comment->comment_ID ); ?>"
				title="<?php echo esc_attr( $questionhub_is_verified ? __( 'Remove verification', 'questionhub' ) : __( 'Mark as Admin Verified', 'questionhub' ) ); ?>">
			<span class="dashicons dashicons-<?php echo esc_attr( $questionhub_is_verified ? 'dismiss' : 'awards' ); ?>"></span>
			<span class="qh-verify-label">
				<?php echo esc_html( $questionhub_is_verified ? __( 'Remove Verification', 'questionhub' ) : __( 'Verify Answer', 'questionhub' ) ); ?>
			</span>
		</button>
		<?php endif; ?>
	</div>
</div>
