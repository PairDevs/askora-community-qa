<?php
/**
 * Template: question-card.php
 * Single question card used in list views.
 *
 * @package QuestionHub
 * @var int $post_id Post ID.
 */

defined( 'ABSPATH' ) || exit;

use QuestionHub\Frontend\Inc\Comments\Badge;
use QuestionHub\Frontend\Inc\Questions\ViewCounter;

$post       = get_post( $post_id );
$author_id  = (int) $post->post_author;
$views      = ViewCounter::get( $post_id );
$votes      = (int) get_post_meta( $post_id, '_questionhub_votes', true );
$answers    = get_comments_number( $post_id );
$categories = get_the_terms( $post_id, 'question_category' );
$tags       = get_the_terms( $post_id, 'question_tag' );
$avatar     = get_avatar( $author_id, 40, '', '', [ 'class' => 'questionhub-avatar' ] );
$author     = get_the_author_meta( 'display_name', $author_id );
$badge      = Badge::get( $author_id, $post_id );
$best       = (int) get_post_meta( $post_id, '_questionhub_best_answer', true );
?>
<article class="questionhub-card questionhub-question-card" id="question-<?php echo esc_attr( $post_id ); ?>">
	<div class="questionhub-card-stats">
		<div class="questionhub-stat questionhub-stat-votes">
			<span class="questionhub-stat-count"><?php echo esc_html( $votes ); ?></span>
			<span class="questionhub-stat-label"><?php esc_html_e( 'votes', 'questionhub' ); ?></span>
		</div>
		<div class="questionhub-stat questionhub-stat-answers <?php echo $best ? 'has-best' : ''; ?>">
			<span class="questionhub-stat-count"><?php echo esc_html( $answers ); ?></span>
			<span class="questionhub-stat-label"><?php esc_html_e( 'answers', 'questionhub' ); ?></span>
		</div>
		<div class="questionhub-stat questionhub-stat-views">
			<span class="questionhub-stat-count"><?php echo esc_html( $views ); ?></span>
			<span class="questionhub-stat-label"><?php esc_html_e( 'views', 'questionhub' ); ?></span>
		</div>
	</div>

	<div class="questionhub-card-body">
		<h3 class="questionhub-question-title">
			<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a>
		</h3>

		<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
		<div class="questionhub-card-categories">
			<?php foreach ( $categories as $cat ) : ?>
				<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="questionhub-tag questionhub-category-tag"><?php echo esc_html( $cat->name ); ?></a>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) : ?>
		<div class="questionhub-card-tags">
			<?php foreach ( $tags as $tag_term ) : ?>
				<a href="<?php echo esc_url( get_term_link( $tag_term ) ); ?>" class="questionhub-tag"><?php echo esc_html( $tag_term->name ); ?></a>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<div class="questionhub-card-meta">
			<?php echo $avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span class="questionhub-meta-author"><?php echo esc_html( $author ); ?></span>
			<?php echo $badge; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span class="questionhub-meta-date"><?php echo esc_html( human_time_diff( get_post_time( 'U', false, $post_id ), current_time( 'U' ) ) . ' ' . __( 'ago', 'questionhub' ) ); ?></span>
		</div>
	</div>
</article>
