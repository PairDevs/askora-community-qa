<?php
/**
 * Template: dashboard.php
 * Shortcode: [questionhub_dashboard]
 *
 * @package QuestionHub
 */

defined( 'ABSPATH' ) || exit;

$user     = wp_get_current_user();
$user_id  = $user->ID;
$phone    = \QuestionHub\Frontend\Inc\Auth\UserMeta::get_phone( $user_id );

$my_questions = new WP_Query( [
	'post_type'      => 'questions',
	'post_status'    => [ 'publish', 'pending', 'draft' ],
	'author'         => $user_id,
	'posts_per_page' => 10,
	'orderby'        => 'date',
	'order'          => 'DESC',
] );
?>
<div class="questionhub-wrapper questionhub-dashboard-wrapper">
	<div class="questionhub-dashboard-header questionhub-card">
		<div class="questionhub-dashboard-avatar">
			<?php echo get_avatar( $user_id, 80, '', '', [ 'class' => 'questionhub-avatar questionhub-avatar-lg' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div class="questionhub-dashboard-info">
			<h2><?php echo esc_html( $user->display_name ); ?></h2>
			<?php if ( $phone ) : ?>
				<p class="questionhub-dashboard-phone"><span class="dashicons dashicons-phone"></span> <?php echo esc_html( $phone ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<div class="questionhub-dashboard-section">
		<h3><?php esc_html_e( 'My Questions', 'questionhub' ); ?></h3>
		<?php if ( $my_questions->have_posts() ) : ?>
		<div class="questionhub-questions-list">
			<?php
			while ( $my_questions->have_posts() ) {
				$my_questions->the_post();
				\QuestionHub\Frontend\Inc\Helpers\Template::load( 'question-card.php', [ 'post_id' => get_the_ID() ] );
			}
			wp_reset_postdata();
			?>
		</div>
		<?php else : ?>
		<p class="questionhub-no-results"><?php esc_html_e( 'You have not asked any questions yet.', 'questionhub' ); ?></p>
		<?php endif; ?>
	</div>
</div>
