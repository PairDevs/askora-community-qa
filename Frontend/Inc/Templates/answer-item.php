<?php
/**
 * Template: answer-item.php
 * Single answer/reply row — used by shortcode list AND answers-template.php.
 *
 * @package QuestionHub
 * @var WP_Comment $comment
 * @var int        $post_id
 * @var int        $best_id   Comment ID of the best answer (0 if none).
 * @var array      $settings  Plugin settings.
 */

defined( 'ABSPATH' ) || exit;

use QuestionHub\Frontend\Inc\Comments\Badge;

$user_id     = (int) $comment->user_id;
$avatar      = get_avatar( $comment, 40, '', '', [ 'class' => 'questionhub-avatar' ] );
$author      = esc_html( $comment->comment_author );
$badge       = Badge::get( $user_id, $post_id );
$votes       = (int) get_comment_meta( $comment->comment_ID, '_questionhub_answer_votes', true );
$is_best     = (int) $comment->comment_ID === $best_id;
$is_verified = (bool) get_comment_meta( $comment->comment_ID, '_questionhub_verified', true );
$is_admin    = current_user_can( 'manage_options' );
$can_best    = \QuestionHub\Frontend\Inc\Helpers\Permission::can_mark_best_answer( $post_id );
$enable_voting = ! empty( $settings['enable_voting'] );
$enable_best   = ! empty( $settings['enable_best_answer'] );
?>
<div class="questionhub-answer-item qh-answer-item <?php echo $is_best ? 'questionhub-best-answer qh-answer-best' : ''; ?> <?php echo $is_verified ? 'qh-answer-verified' : ''; ?>"
	 id="answer-<?php echo esc_attr( $comment->comment_ID ); ?>">

	<!-- Verified ribbon — top-right corner -->
	<?php if ( $is_verified ) : ?>
	<div class="qh-verified-ribbon" aria-label="<?php esc_attr_e( 'Admin Verified', 'questionhub' ); ?>">
		<span class="dashicons dashicons-yes"></span>
		<?php esc_html_e( 'Verified', 'questionhub' ); ?>
	</div>
	<?php endif; ?>

	<?php if ( $is_best ) : ?>
	<div class="questionhub-best-answer-badge qh-accepted-banner">
		<span class="dashicons dashicons-yes-alt"></span>
		<?php esc_html_e( 'Best Answer', 'questionhub' ); ?>
	</div>
	<?php endif; ?>

	<div class="questionhub-answer-header qh-answer-author-row">
		<?php echo $avatar; // phpcs:ignore ?>
		<div class="questionhub-answer-meta qh-answer-author-info">
			<span class="questionhub-meta-author qh-answer-author-name"><?php echo $author; ?></span>
			<?php echo $badge; // phpcs:ignore ?>
			<span class="questionhub-meta-date qh-answer-date">
				<?php echo esc_html( human_time_diff( strtotime( $comment->comment_date ), current_time( 'U' ) ) . ' ' . __( 'ago', 'questionhub' ) ); ?>
			</span>
		</div>
	</div>

	<div class="questionhub-answer-content qh-answer-text">
		<?php echo wp_kses_post( $comment->comment_content ); ?>
	</div>

	<div class="questionhub-answer-actions qh-answer-actions-row">
		<?php if ( $enable_voting ) : ?>
		<button class="questionhub-vote-answer-btn qh-vote-upbtn qh-vote-answer-btn questionhub-btn-icon"
				data-id="<?php echo esc_attr( $comment->comment_ID ); ?>"
				data-type="answer">
			<span class="dashicons dashicons-thumbs-up"></span>
			<span class="questionhub-vote-count qh-vote-count"><?php echo esc_html( $votes ); ?></span>
		</button>
		<?php endif; ?>

		<?php if ( $enable_best && $can_best && ! $is_best ) : ?>
		<button class="questionhub-mark-best-btn qh-mark-best-btn"
				data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>"
				data-post-id="<?php echo esc_attr( $post_id ); ?>">
			<span class="dashicons dashicons-yes-alt"></span>
			<?php esc_html_e( 'Mark as Best Answer', 'questionhub' ); ?>
		</button>
		<?php endif; ?>

		<?php if ( $is_admin ) : ?>
		<button class="qh-verify-answer-btn <?php echo $is_verified ? 'qh-verify-active' : ''; ?>"
				data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>"
				title="<?php echo $is_verified ? esc_attr__( 'Remove verification', 'questionhub' ) : esc_attr__( 'Mark as Admin Verified', 'questionhub' ); ?>">
			<span class="dashicons dashicons-<?php echo $is_verified ? 'dismiss' : 'awards'; ?>"></span>
			<span class="qh-verify-label">
				<?php echo $is_verified ? esc_html__( 'Remove Verification', 'questionhub' ) : esc_html__( 'Verify Answer', 'questionhub' ); ?>
			</span>
		</button>
		<?php endif; ?>
	</div>
</div>
