<?php
/**
 * Template: dashboard.php
 * Shortcode: [askora_dashboard]
 *
 * @package ASKORA
 */

defined( 'ABSPATH' ) || exit;

$askora_user     = wp_get_current_user();
$askora_user_id  = $askora_user->ID;
$askora_phone    = \ASKORA\Frontend\Inc\Auth\UserMeta::get_phone( $askora_user_id );

$askora_my_questions = new WP_Query( [
	'post_type'      => 'questions',
	'post_status'    => [ 'publish', 'pending', 'draft' ],
	'author'         => $askora_user_id,
	'posts_per_page' => 10,
	'orderby'        => 'date',
	'order'          => 'DESC',
] );
?>
<div class="askora-wrapper askora-dashboard-wrapper">
	<div class="askora-dashboard-header askora-card">
		<div class="askora-dashboard-avatar">
			<?php echo wp_kses_post( get_avatar( $askora_user_id, 80, '', '', [ 'class' => 'askora-avatar askora-avatar-lg' ] ) ); ?>
		</div>
		<div class="askora-dashboard-info">
			<h2><?php echo esc_html( $askora_user->display_name ); ?></h2>
			<?php if ( $askora_phone ) : ?>
				<p class="askora-dashboard-phone"><span class="dashicons dashicons-phone"></span> <?php echo esc_html( $askora_phone ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<div class="askora-dashboard-section">
		<h3><?php esc_html_e( 'My Questions', 'askora-community-qa' ); ?></h3>
		<?php if ( $askora_my_questions->have_posts() ) : ?>
		<div class="askora-questions-list">
			<?php
			while ( $askora_my_questions->have_posts() ) {
				$askora_my_questions->the_post();
				\ASKORA\Frontend\Inc\Helpers\Template::load( 'question-card.php', [ 'post_id' => get_the_ID(), 'status' => get_post_status() ] );
			}
			wp_reset_postdata();
			?>
		</div>
		<?php else : ?>
		<p class="askora-no-results"><?php esc_html_e( 'You have not asked any questions yet.', 'askora-community-qa' ); ?></p>
		<?php endif; ?>
	</div>
</div>
