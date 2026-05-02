<?php
/**
 * Template: single-question.php
 * Full single question page.
 *
 * @package QuestionHub
 */

defined( 'ABSPATH' ) || exit;

use QuestionHub\Frontend\Inc\Comments\Badge;
use QuestionHub\Frontend\Inc\Comments\AnswerRenderer;
use QuestionHub\Frontend\Inc\Questions\ViewCounter;

$post_id    = get_the_ID();
$author_id  = (int) get_the_author_meta( 'ID' );
$views      = ViewCounter::get( $post_id );
$votes      = (int) get_post_meta( $post_id, '_questionhub_votes', true );
$answers    = get_comments_number( $post_id );
$avatar     = get_avatar( $author_id, 48, '', '', [ 'class' => 'questionhub-avatar' ] );
$author     = get_the_author();
$badge      = Badge::get( $author_id, $post_id );
$categories = get_the_terms( $post_id, 'question_category' );
$tags       = get_the_terms( $post_id, 'question_tag' );
$settings   = get_option( 'questionhub_settings', [] );
$voted      = is_user_logged_in() ? (new \QuestionHub\Frontend\Inc\Questions\VoteManager())->has_voted_question( $post_id, get_current_user_id() ) : false;
?>
<div class="questionhub-wrapper questionhub-single-wrapper">

	<div class="questionhub-single-header questionhub-card">
		<div class="questionhub-single-meta-top">
			<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
				<?php foreach ( $categories as $cat ) : ?>
					<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="questionhub-tag questionhub-category-tag"><?php echo esc_html( $cat->name ); ?></a>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<h1 class="questionhub-question-title"><?php the_title(); ?></h1>

		<div class="questionhub-meta questionhub-single-meta">
			<?php echo $avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="questionhub-meta-info">
				<span class="questionhub-meta-author"><?php echo esc_html( $author ); ?></span>
				<?php echo $badge; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="questionhub-meta-date"><?php echo esc_html( get_the_date() ); ?></span>
			</div>
			<div class="questionhub-meta-stats">
				<span class="questionhub-stat-pill"><span class="dashicons dashicons-visibility"></span> <?php echo esc_html( $views ); ?></span>
				<span class="questionhub-stat-pill"><span class="dashicons dashicons-admin-comments"></span> <?php echo esc_html( $answers ); ?></span>
				<?php if ( ! empty( $settings['enable_voting'] ) ) : ?>
				<button class="questionhub-vote-btn questionhub-stat-pill <?php echo $voted ? 'voted' : ''; ?>" data-id="<?php echo esc_attr( $post_id ); ?>" data-type="question">
					<span class="dashicons dashicons-thumbs-up"></span>
					<span class="questionhub-vote-count"><?php echo esc_html( $votes ); ?></span>
				</button>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="questionhub-single-content questionhub-card">
		<?php the_content(); ?>

		<?php if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) : ?>
		<div class="questionhub-single-tags">
			<?php foreach ( $tags as $tag ) : ?>
				<a href="<?php echo esc_url( get_term_link( $tag ) ); ?>" class="questionhub-tag"><?php echo esc_html( $tag->name ); ?></a>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>

	<?php // Answers list. ?>
	<?php do_action( 'questionhub_before_answer_form' ); ?>
	<div class="questionhub-answers-section">
		<h2 class="questionhub-answers-title">
			<?php
			printf(
				/* translators: %d: number of answers */
				esc_html( _n( '%d Answer', '%d Answers', $answers, 'questionhub' ) ),
				esc_html( $answers )
			);
			?>
		</h2>
		<?php echo AnswerRenderer::render_list( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<?php do_action( 'questionhub_after_answer_form' ); ?>

	<?php // Answer form. ?>
	<div class="questionhub-answer-form-section questionhub-card">
		<h3 class="questionhub-form-title"><?php esc_html_e( 'Your Answer', 'questionhub' ); ?></h3>
		<div class="questionhub-alert questionhub-alert-success" id="questionhub-answer-success" style="display:none;"></div>
		<div class="questionhub-alert questionhub-alert-error"   id="questionhub-answer-error"   style="display:none;"></div>

		<?php if ( \QuestionHub\Frontend\Inc\Helpers\Permission::can_reply() ) : ?>
		<form id="questionhub-answer-form" class="questionhub-form" novalidate>
			<?php wp_nonce_field( 'questionhub_nonce', 'questionhub_nonce_field' ); ?>
			<input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>">
			<div class="questionhub-form-group">
				<textarea name="content" class="questionhub-textarea" rows="6" placeholder="<?php esc_attr_e( 'Write your answer here…', 'questionhub' ); ?>" required></textarea>
			</div>
			<div class="questionhub-form-actions">
				<button type="submit" class="questionhub-button questionhub-button-primary">
					<span class="questionhub-btn-text"><?php esc_html_e( 'Submit Answer', 'questionhub' ); ?></span>
					<span class="questionhub-spinner" style="display:none;"></span>
				</button>
			</div>
		</form>
		<?php else : ?>
		<p class="questionhub-login-notice">
			<?php esc_html_e( 'Please', 'questionhub' ); ?>
			<a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>"><?php esc_html_e( 'log in', 'questionhub' ); ?></a>
			<?php esc_html_e( 'to submit an answer.', 'questionhub' ); ?>
		</p>
		<?php endif; ?>
	</div>
</div>
