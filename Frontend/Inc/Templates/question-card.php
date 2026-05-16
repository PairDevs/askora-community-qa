<?php
/**
 * Template: question-card.php
 * Single question card used in list views.
 *
 * @package QuestionHub
 * @var int $questionhub_post_id Post ID.
 */

defined( 'ABSPATH' ) || exit;

use QuestionHub\Frontend\Inc\Comments\Badge;
use QuestionHub\Frontend\Inc\Questions\ViewCounter;

$questionhub_post       = get_post( $questionhub_post_id );
$questionhub_author_id  = (int) $questionhub_post->post_author;
$questionhub_views      = ViewCounter::get( $questionhub_post_id );
$questionhub_votes      = (int) get_post_meta( $questionhub_post_id, '_questionhub_votes', true );
$questionhub_answers    = get_comments_number( $questionhub_post_id );
$questionhub_categories = get_the_terms( $questionhub_post_id, 'question_category' );
$questionhub_tags       = get_the_terms( $questionhub_post_id, 'question_tag' );
$questionhub_avatar     = get_avatar( $questionhub_author_id, 40, '', '', [ 'class' => 'questionhub-avatar' ] );
$questionhub_author     = get_the_author_meta( 'display_name', $questionhub_author_id );
$questionhub_badge      = Badge::get( $questionhub_author_id, $questionhub_post_id );
$questionhub_best       = (int) get_post_meta( $questionhub_post_id, '_questionhub_best_answer', true );
$questionhub_status     = $questionhub_status ?? '';
?>
<article class="questionhub-card questionhub-question-card" id="question-<?php echo esc_attr( $questionhub_post_id ); ?>">
	<div class="questionhub-card-stats">
		<div class="questionhub-stat questionhub-stat-votes">
			<span class="questionhub-stat-count"><?php echo esc_html( $questionhub_votes ); ?></span>
			<span class="questionhub-stat-label"><?php esc_html_e( 'votes', 'questionhub' ); ?></span>
		</div>
		<div class="questionhub-stat questionhub-stat-answers <?php echo esc_attr( $questionhub_best ? 'has-best' : '' ); ?>">
			<span class="questionhub-stat-count"><?php echo esc_html( $questionhub_answers ); ?></span>
			<span class="questionhub-stat-label"><?php esc_html_e( 'answers', 'questionhub' ); ?></span>
		</div>
		<div class="questionhub-stat questionhub-stat-views">
			<span class="questionhub-stat-count"><?php echo esc_html( $questionhub_views ); ?></span>
			<span class="questionhub-stat-label"><?php esc_html_e( 'views', 'questionhub' ); ?></span>
		</div>
	</div>

	<div class="questionhub-card-body">
		<h3 class="questionhub-question-title">
			<a href="<?php echo esc_url( get_permalink( $questionhub_post_id ) ); ?>"><?php echo esc_html( get_the_title( $questionhub_post_id ) ); ?></a>
			<?php if ( ! empty( $questionhub_status ) ) : ?>
				<span class="questionhub-status-badge questionhub-status-<?php echo esc_attr( $questionhub_status ); ?>"><?php echo esc_html( ucfirst( $questionhub_status ) ); ?></span>
			<?php endif; ?>
		</h3>

		<?php if ( ! empty( $questionhub_categories ) && ! is_wp_error( $questionhub_categories ) ) : ?>
		<div class="questionhub-card-categories">
			<?php foreach ( $questionhub_categories as $questionhub_cat ) : ?>
				<a href="<?php echo esc_url( get_term_link( $questionhub_cat ) ); ?>" class="questionhub-tag questionhub-category-tag"><?php echo esc_html( $questionhub_cat->name ); ?></a>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $questionhub_tags ) && ! is_wp_error( $questionhub_tags ) ) : ?>
		<div class="questionhub-card-tags">
			<?php foreach ( $questionhub_tags as $questionhub_tag_term ) : ?>
				<a href="<?php echo esc_url( get_term_link( $questionhub_tag_term ) ); ?>" class="questionhub-tag"><?php echo esc_html( $questionhub_tag_term->name ); ?></a>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<div class="questionhub-card-meta">
			<?php echo wp_kses_post( $questionhub_avatar ); ?>
			<span class="questionhub-meta-author"><?php echo esc_html( $questionhub_author ); ?></span>
			<?php echo wp_kses_post( $questionhub_badge ); ?>
			<span class="questionhub-meta-date"><?php echo esc_html( human_time_diff( get_post_time( 'U', false, $questionhub_post_id ), current_time( 'U' ) ) . ' ' . __( 'ago', 'questionhub' ) ); ?></span>
		</div>
	</div>
</article>
