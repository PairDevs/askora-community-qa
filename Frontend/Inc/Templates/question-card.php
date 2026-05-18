<?php
/**
 * Template: question-card.php
 * Single question card used in list views.
 *
 * @package ASKORA
 * @var int $askora_post_id Post ID.
 */

defined( 'ABSPATH' ) || exit;

use ASKORA\Frontend\Inc\Comments\Badge;
use ASKORA\Frontend\Inc\Questions\ViewCounter;

$askora_post       = get_post( $askora_post_id );
$askora_author_id  = (int) $askora_post->post_author;
$askora_views      = ViewCounter::get( $askora_post_id );
$askora_votes      = (int) get_post_meta( $askora_post_id, '_askora_votes', true );
$askora_answers    = get_comments_number( $askora_post_id );
$askora_categories = get_the_terms( $askora_post_id, 'question_category' );
$askora_tags       = get_the_terms( $askora_post_id, 'question_tag' );
$askora_avatar     = get_avatar( $askora_author_id, 40, '', '', [ 'class' => 'askora-avatar' ] );
$askora_author     = get_the_author_meta( 'display_name', $askora_author_id );
$askora_badge      = Badge::get( $askora_author_id, $askora_post_id );
$askora_best       = (int) get_post_meta( $askora_post_id, '_askora_best_answer', true );
$askora_status     = $askora_status ?? '';
?>
<article class="askora-card askora-question-card" id="question-<?php echo esc_attr( $askora_post_id ); ?>">
	<div class="askora-card-stats">
		<div class="askora-stat askora-stat-votes">
			<span class="askora-stat-count"><?php echo esc_html( $askora_votes ); ?></span>
			<span class="askora-stat-label"><?php esc_html_e( 'votes', 'askora-community-qa' ); ?></span>
		</div>
		<div class="askora-stat askora-stat-answers <?php echo esc_attr( $askora_best ? 'has-best' : '' ); ?>">
			<span class="askora-stat-count"><?php echo esc_html( $askora_answers ); ?></span>
			<span class="askora-stat-label"><?php esc_html_e( 'answers', 'askora-community-qa' ); ?></span>
		</div>
		<div class="askora-stat askora-stat-views">
			<span class="askora-stat-count"><?php echo esc_html( $askora_views ); ?></span>
			<span class="askora-stat-label"><?php esc_html_e( 'views', 'askora-community-qa' ); ?></span>
		</div>
	</div>

	<div class="askora-card-body">
		<h3 class="askora-question-title">
			<a href="<?php echo esc_url( get_permalink( $askora_post_id ) ); ?>"><?php echo esc_html( get_the_title( $askora_post_id ) ); ?></a>
			<?php if ( ! empty( $askora_status ) ) : ?>
				<span class="askora-status-badge askora-status-<?php echo esc_attr( $askora_status ); ?>"><?php echo esc_html( ucfirst( $askora_status ) ); ?></span>
			<?php endif; ?>
		</h3>

		<?php if ( ! empty( $askora_categories ) && ! is_wp_error( $askora_categories ) ) : ?>
		<div class="askora-card-categories">
			<?php foreach ( $askora_categories as $askora_cat ) : ?>
				<a href="<?php echo esc_url( get_term_link( $askora_cat ) ); ?>" class="askora-tag askora-category-tag"><?php echo esc_html( $askora_cat->name ); ?></a>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $askora_tags ) && ! is_wp_error( $askora_tags ) ) : ?>
		<div class="askora-card-tags">
			<?php foreach ( $askora_tags as $askora_tag_term ) : ?>
				<a href="<?php echo esc_url( get_term_link( $askora_tag_term ) ); ?>" class="askora-tag"><?php echo esc_html( $askora_tag_term->name ); ?></a>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<div class="askora-card-meta">
			<?php echo wp_kses_post( $askora_avatar ); ?>
			<span class="askora-meta-author"><?php echo esc_html( $askora_author ); ?></span>
			<?php echo wp_kses_post( $askora_badge ); ?>
			<span class="askora-meta-date"><?php echo esc_html( human_time_diff( get_post_time( 'U', false, $askora_post_id ), current_time( 'U' ) ) . ' ' . __( 'ago', 'askora-community-qa' ) ); ?></span>
		</div>
	</div>
</article>
