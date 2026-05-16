<?php
/**
 * Template: dashboard.php
 * Shortcode: [questionhub_dashboard]
 *
 * @package QuestionHub
 */

defined( 'ABSPATH' ) || exit;

$questionhub_user     = wp_get_current_user();
$questionhub_user_id  = $questionhub_user->ID;
$questionhub_phone    = \QuestionHub\Frontend\Inc\Auth\UserMeta::get_phone( $questionhub_user_id );

$questionhub_my_questions = new WP_Query( [
	'post_type'      => 'questions',
	'post_status'    => [ 'publish', 'pending', 'draft' ],
	'author'         => $questionhub_user_id,
	'posts_per_page' => 10,
	'orderby'        => 'date',
	'order'          => 'DESC',
] );
?>
<div class="questionhub-wrapper questionhub-dashboard-wrapper">
	<div class="questionhub-dashboard-header questionhub-card">
		<div class="questionhub-dashboard-avatar">
			<?php echo wp_kses_post( get_avatar( $questionhub_user_id, 80, '', '', [ 'class' => 'questionhub-avatar questionhub-avatar-lg' ] ) ); ?>
		</div>
		<div class="questionhub-dashboard-info">
			<h2><?php echo esc_html( $questionhub_user->display_name ); ?></h2>
			<?php if ( $questionhub_phone ) : ?>
				<p class="questionhub-dashboard-phone"><span class="dashicons dashicons-phone"></span> <?php echo esc_html( $questionhub_phone ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<div class="questionhub-dashboard-section">
		<h3><?php esc_html_e( 'My Questions', 'questionhub' ); ?></h3>
		<?php if ( $questionhub_my_questions->have_posts() ) : ?>
		<div class="questionhub-questions-list">
			<?php
			while ( $questionhub_my_questions->have_posts() ) {
				$questionhub_my_questions->the_post();
				\QuestionHub\Frontend\Inc\Helpers\Template::load( 'question-card.php', [ 'post_id' => get_the_ID(), 'status' => get_post_status() ] );
			}
			wp_reset_postdata();
			?>
		</div>
		<?php else : ?>
		<p class="questionhub-no-results"><?php esc_html_e( 'You have not asked any questions yet.', 'questionhub' ); ?></p>
		<?php endif; ?>
	</div>
</div>
