<?php
/**
 * Template: answer-item.php
 * Single answer/reply row.
 *
 * @package QuestionHub
 * @var WP_Comment $comment
 * @var int        $post_id
 * @var int        $best_id   Comment ID of the best answer (0 if none).
 * @var array      $settings  Plugin settings.
 */

defined( 'ABSPATH' ) || exit;

use QuestionHub\Frontend\Inc\Comments\Badge;

$user_id    = (int) $comment->user_id;
$avatar     = get_avatar( $comment, 40, '', '', [ 'class' => 'questionhub-avatar' ] );
$author     = esc_html( $comment->comment_author );
$badge      = Badge::get( $user_id, $post_id );
$votes      = (int) get_comment_meta( $comment->comment_ID, '_questionhub_answer_votes', true );
$is_best    = (int) $comment->comment_ID === $best_id;
$can_best   = \QuestionHub\Frontend\Inc\Helpers\Permission::can_mark_best_answer( $post_id );
$enable_voting = ! empty( $settings['enable_voting'] );
$enable_best   = ! empty( $settings['enable_best_answer'] );
?>
<div class="questionhub-answer-item <?php echo $is_best ? 'questionhub-best-answer' : ''; ?>" id="answer-<?php echo esc_attr( $comment->comment_ID ); ?>">

	<?php if ( $is_best ) : ?>
	<div class="questionhub-best-answer-badge">
		<span class="dashicons dashicons-yes-alt"></span>
		<?php esc_html_e( 'Best Answer', 'questionhub' ); ?>
	</div>
	<?php endif; ?>

	<div class="questionhub-answer-header">
		<?php echo $avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="questionhub-answer-meta">
			<span class="questionhub-meta-author"><?php echo $author; ?></span>
			<?php echo $badge; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span class="questionhub-meta-date"><?php echo esc_html( human_time_diff( strtotime( $comment->comment_date ), current_time( 'U' ) ) . ' ' . __( 'ago', 'questionhub' ) ); ?></span>
		</div>
	</div>

	<div class="questionhub-answer-content">
		<?php echo wp_kses_post( $comment->comment_content ); ?>
	</div>

	<div class="questionhub-answer-actions">
		<?php if ( $enable_voting ) : ?>
		<button class="questionhub-vote-answer-btn questionhub-btn-icon" data-id="<?php echo esc_attr( $comment->comment_ID ); ?>" data-type="answer">
			<span class="dashicons dashicons-thumbs-up"></span>
			<span class="questionhub-vote-count"><?php echo esc_html( $votes ); ?></span>
		</button>
		<?php endif; ?>

		<?php if ( $enable_best && $can_best && ! $is_best ) : ?>
		<button class="questionhub-mark-best-btn questionhub-btn-sm" data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>" data-post-id="<?php echo esc_attr( $post_id ); ?>">
			<?php esc_html_e( 'Mark as Best Answer', 'questionhub' ); ?>
		</button>
		<?php endif; ?>
	</div>
</div>
