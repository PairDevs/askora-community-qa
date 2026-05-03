<?php
/**
 * Top-level admin menu for QuestionHub.
 *
 * @package QuestionHub\Admin\Inc\Dashboard\Menu
 * @since   1.0.0
 */

namespace QuestionHub\Admin\Inc\Dashboard\Menu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class QuestionHub
 *
 * Registers the top-level "QuestionHub" admin menu page with a modern dashboard.
 */
class QuestionHub {

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_menu_page' ] );
	}

	public function add_menu_page() {
		add_menu_page(
			esc_html__( 'QuestionHub', 'questionhub' ),
			esc_html__( 'QuestionHub', 'questionhub' ),
			'manage_options',
			'questionhub_home',
			[ $this, 'render_home_page' ],
			'dashicons-editor-help',
			25
		);
	}

	/**
	 * Gather dashboard stats.
	 *
	 * @return array
	 */
	private function get_stats(): array {
		$counts = wp_count_posts( 'questions' );

		$total_published = (int) ( $counts->publish ?? 0 );
		$total_pending   = (int) ( $counts->pending ?? 0 );
		$total_draft     = (int) ( $counts->draft ?? 0 );
		$total           = $total_published + $total_pending + $total_draft;

		// Total approved answers (comments).
		global $wpdb;
		$total_answers = (int) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->comments} c
				 INNER JOIN {$wpdb->posts} p ON c.comment_post_ID = p.ID
				 WHERE p.post_type = %s AND c.comment_approved = %s",
				'questions',
				'1'
			)
		);

		// Total registered users.
		$user_count = count_users();
		$total_users = (int) ( $user_count['total_users'] ?? 0 );

		return compact( 'total', 'total_published', 'total_pending', 'total_answers', 'total_users' );
	}

	/**
	 * Get the 5 most recent questions.
	 *
	 * @return array
	 */
	private function get_recent_questions(): array {
		return get_posts( [
			'post_type'      => 'questions',
			'post_status'    => [ 'publish', 'pending', 'draft' ],
			'posts_per_page' => 5,
			'orderby'        => 'date',
			'order'          => 'DESC',
		] );
	}

	public function render_home_page() {
		$version  = defined( 'QUESTIONHUB_VERSION' ) ? QUESTIONHUB_VERSION : '1.0.0';
		$stats    = $this->get_stats();
		$recents  = $this->get_recent_questions();
		$settings = get_option( 'questionhub_settings', [] );
		?>
		<div class="qh-dash-wrap">

			<!-- ══════════ HERO ══════════ -->
			<div class="qh-hero">
				<div class="qh-hero-left">
					<div class="qh-hero-logo">
						<span class="dashicons dashicons-editor-help"></span>
					</div>
					<div>
						<h1 class="qh-hero-title">QuestionHub</h1>
						<p class="qh-hero-subtitle">
							<?php
							printf(
								/* translators: %s: plugin version */
								esc_html__( 'Modern Q&A Platform · v%s', 'questionhub' ),
								esc_html( $version )
							);
							?>
						</p>
					</div>
				</div>
				<div class="qh-hero-actions">
					<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=questions' ) ); ?>" class="qh-btn qh-btn-white">
						<span class="dashicons dashicons-plus-alt2"></span>
						<?php esc_html_e( 'New Question', 'questionhub' ); ?>
					</a>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=questionhub_settings' ) ); ?>" class="qh-btn qh-btn-ghost">
						<span class="dashicons dashicons-admin-settings"></span>
						<?php esc_html_e( 'Settings', 'questionhub' ); ?>
					</a>
				</div>
			</div>

			<!-- ══════════ STAT CARDS ══════════ -->
			<div class="qh-stats-grid">
				<div class="qh-stat-card qh-stat-total">
					<div class="qh-stat-icon"><span class="dashicons dashicons-editor-help"></span></div>
					<div class="qh-stat-body">
						<span class="qh-stat-num"><?php echo esc_html( number_format( $stats['total'] ) ); ?></span>
						<span class="qh-stat-label"><?php esc_html_e( 'Total Questions', 'questionhub' ); ?></span>
					</div>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=questions' ) ); ?>" class="qh-stat-link">
						<?php esc_html_e( 'View all', 'questionhub' ); ?> →
					</a>
				</div>

				<div class="qh-stat-card qh-stat-published">
					<div class="qh-stat-icon"><span class="dashicons dashicons-yes-alt"></span></div>
					<div class="qh-stat-body">
						<span class="qh-stat-num"><?php echo esc_html( number_format( $stats['total_published'] ) ); ?></span>
						<span class="qh-stat-label"><?php esc_html_e( 'Published', 'questionhub' ); ?></span>
					</div>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=questions&post_status=publish' ) ); ?>" class="qh-stat-link">
						<?php esc_html_e( 'View all', 'questionhub' ); ?> →
					</a>
				</div>

				<div class="qh-stat-card qh-stat-pending">
					<div class="qh-stat-icon"><span class="dashicons dashicons-clock"></span></div>
					<div class="qh-stat-body">
						<span class="qh-stat-num"><?php echo esc_html( number_format( $stats['total_pending'] ) ); ?></span>
						<span class="qh-stat-label"><?php esc_html_e( 'Pending Review', 'questionhub' ); ?></span>
					</div>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=questions&post_status=pending' ) ); ?>" class="qh-stat-link">
						<?php esc_html_e( 'Review', 'questionhub' ); ?> →
					</a>
				</div>

				<div class="qh-stat-card qh-stat-answers">
					<div class="qh-stat-icon"><span class="dashicons dashicons-admin-comments"></span></div>
					<div class="qh-stat-body">
						<span class="qh-stat-num"><?php echo esc_html( number_format( $stats['total_answers'] ) ); ?></span>
						<span class="qh-stat-label"><?php esc_html_e( 'Total Answers', 'questionhub' ); ?></span>
					</div>
					<a href="<?php echo esc_url( admin_url( 'edit-comments.php' ) ); ?>" class="qh-stat-link">
						<?php esc_html_e( 'View all', 'questionhub' ); ?> →
					</a>
				</div>

				<div class="qh-stat-card qh-stat-users">
					<div class="qh-stat-icon"><span class="dashicons dashicons-groups"></span></div>
					<div class="qh-stat-body">
						<span class="qh-stat-num"><?php echo esc_html( number_format( $stats['total_users'] ) ); ?></span>
						<span class="qh-stat-label"><?php esc_html_e( 'Total Users', 'questionhub' ); ?></span>
					</div>
					<a href="<?php echo esc_url( admin_url( 'users.php' ) ); ?>" class="qh-stat-link">
						<?php esc_html_e( 'Manage', 'questionhub' ); ?> →
					</a>
				</div>
			</div>

			<!-- ══════════ CONTENT GRID ══════════ -->
			<div class="qh-content-grid">

				<!-- Recent Questions -->
				<div class="qh-panel">
					<div class="qh-panel-header">
						<h2 class="qh-panel-title">
							<span class="dashicons dashicons-list-view"></span>
							<?php esc_html_e( 'Recent Questions', 'questionhub' ); ?>
						</h2>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=questions' ) ); ?>" class="qh-panel-link">
							<?php esc_html_e( 'View all', 'questionhub' ); ?>
						</a>
					</div>
					<div class="qh-panel-body">
						<?php if ( ! empty( $recents ) ) : ?>
						<div class="qh-recent-list">
							<?php foreach ( $recents as $q ) :
								$status      = get_post_status( $q->ID );
								$status_map  = [
									'publish' => [ 'label' => __( 'Published', 'questionhub' ), 'class' => 'qh-status-publish' ],
									'pending' => [ 'label' => __( 'Pending', 'questionhub' ), 'class' => 'qh-status-pending' ],
									'draft'   => [ 'label' => __( 'Draft', 'questionhub' ), 'class' => 'qh-status-draft' ],
								];
								$s = $status_map[ $status ] ?? $status_map['draft'];
								$answers  = (int) get_comments_number( $q->ID );
								$views    = (int) get_post_meta( $q->ID, '_questionhub_views', true );
							?>
							<div class="qh-recent-row">
								<div class="qh-recent-main">
									<a href="<?php echo esc_url( get_edit_post_link( $q->ID ) ); ?>" class="qh-recent-title">
										<?php echo esc_html( $q->post_title ); ?>
									</a>
									<div class="qh-recent-meta">
										<span class="qh-status-badge <?php echo esc_attr( $s['class'] ); ?>"><?php echo esc_html( $s['label'] ); ?></span>
										<span class="qh-meta-pill">
											<span class="dashicons dashicons-admin-comments"></span>
											<?php echo esc_html( $answers ); ?>
										</span>
										<span class="qh-meta-pill">
											<span class="dashicons dashicons-visibility"></span>
											<?php echo esc_html( $views ); ?>
										</span>
										<span class="qh-meta-date"><?php echo esc_html( human_time_diff( strtotime( $q->post_date ), current_time( 'U' ) ) . ' ago' ); ?></span>
									</div>
								</div>
								<a href="<?php echo esc_url( get_edit_post_link( $q->ID ) ); ?>" class="qh-row-action">
									<?php esc_html_e( 'Edit', 'questionhub' ); ?>
								</a>
							</div>
							<?php endforeach; ?>
						</div>
						<?php else : ?>
						<div class="qh-empty-panel">
							<span class="dashicons dashicons-editor-help qh-empty-icon"></span>
							<p><?php esc_html_e( 'No questions yet. Create the first one!', 'questionhub' ); ?></p>
							<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=questions' ) ); ?>" class="qh-btn qh-btn-primary">
								<?php esc_html_e( '+ Add Question', 'questionhub' ); ?>
							</a>
						</div>
						<?php endif; ?>
					</div>
				</div>

				<!-- Right Column -->
				<div class="qh-sidebar-col">

					<!-- Quick Actions -->
					<div class="qh-panel">
						<div class="qh-panel-header">
							<h2 class="qh-panel-title">
								<span class="dashicons dashicons-superhero-alt"></span>
								<?php esc_html_e( 'Quick Actions', 'questionhub' ); ?>
							</h2>
						</div>
						<div class="qh-panel-body">
							<div class="qh-quick-actions">
								<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=questions' ) ); ?>" class="qh-quick-btn">
									<span class="dashicons dashicons-plus-alt2"></span>
									<?php esc_html_e( 'Add Question', 'questionhub' ); ?>
								</a>
								<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=questions&post_status=pending' ) ); ?>" class="qh-quick-btn">
									<span class="dashicons dashicons-clock"></span>
									<?php esc_html_e( 'Review Pending', 'questionhub' ); ?>
									<?php if ( $stats['total_pending'] > 0 ) : ?>
									<span class="qh-badge-count"><?php echo esc_html( $stats['total_pending'] ); ?></span>
									<?php endif; ?>
								</a>
								<a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=question_category&post_type=questions' ) ); ?>" class="qh-quick-btn">
									<span class="dashicons dashicons-category"></span>
									<?php esc_html_e( 'Manage Categories', 'questionhub' ); ?>
								</a>
								<a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=question_tag&post_type=questions' ) ); ?>" class="qh-quick-btn">
									<span class="dashicons dashicons-tag"></span>
									<?php esc_html_e( 'Manage Tags', 'questionhub' ); ?>
								</a>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=questionhub_settings' ) ); ?>" class="qh-quick-btn">
									<span class="dashicons dashicons-admin-settings"></span>
									<?php esc_html_e( 'Plugin Settings', 'questionhub' ); ?>
								</a>
								<a href="<?php echo esc_url( admin_url( 'edit-comments.php' ) ); ?>" class="qh-quick-btn">
									<span class="dashicons dashicons-admin-comments"></span>
									<?php esc_html_e( 'Moderate Answers', 'questionhub' ); ?>
								</a>
							</div>
						</div>
					</div>

					<!-- Shortcode Reference -->
					<div class="qh-panel">
						<div class="qh-panel-header">
							<h2 class="qh-panel-title">
								<span class="dashicons dashicons-shortcode"></span>
								<?php esc_html_e( 'Shortcode Reference', 'questionhub' ); ?>
							</h2>
						</div>
						<div class="qh-panel-body">
							<div class="qh-shortcode-list">
								<?php
								$shortcodes = [
									[ 'code' => '[questionhub_questions]',            'desc' => __( 'Questions list', 'questionhub' ) ],
									[ 'code' => '[questionhub_submit_form]',          'desc' => __( 'Submit question form', 'questionhub' ) ],
									[ 'code' => '[questionhub_search]',               'desc' => __( 'Live search', 'questionhub' ) ],
									[ 'code' => '[questionhub_auth]',                 'desc' => __( 'Login & register tabs', 'questionhub' ) ],
									[ 'code' => '[questionhub_dashboard]',            'desc' => __( 'User dashboard', 'questionhub' ) ],
									[ 'code' => '[questionhub_popular_questions]',    'desc' => __( 'Popular questions', 'questionhub' ) ],
									[ 'code' => '[questionhub_unanswered_questions]', 'desc' => __( 'Unanswered questions', 'questionhub' ) ],
								];
								foreach ( $shortcodes as $sc ) :
								?>
								<div class="qh-shortcode-row">
									<code class="qh-shortcode-code" title="<?php esc_attr_e( 'Click to copy', 'questionhub' ); ?>" data-copy="<?php echo esc_attr( $sc['code'] ); ?>">
										<?php echo esc_html( $sc['code'] ); ?>
									</code>
									<span class="qh-shortcode-desc"><?php echo esc_html( $sc['desc'] ); ?></span>
								</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>

					<!-- Current Settings Overview -->
					<div class="qh-panel">
						<div class="qh-panel-header">
							<h2 class="qh-panel-title">
								<span class="dashicons dashicons-admin-settings"></span>
								<?php esc_html_e( 'Active Settings', 'questionhub' ); ?>
							</h2>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=questionhub_settings' ) ); ?>" class="qh-panel-link">
								<?php esc_html_e( 'Edit', 'questionhub' ); ?>
							</a>
						</div>
						<div class="qh-panel-body">
							<div class="qh-settings-overview">
								<?php
								$overview = [
									[
										'label'  => __( 'Default Status', 'questionhub' ),
										'value'  => ucfirst( $settings['question_status'] ?? 'pending' ),
										'active' => true,
									],
									[
										'label'  => __( 'Voting', 'questionhub' ),
										'value'  => ! empty( $settings['enable_voting'] ) ? __( 'Enabled', 'questionhub' ) : __( 'Disabled', 'questionhub' ),
										'active' => ! empty( $settings['enable_voting'] ),
									],
									[
										'label'  => __( 'Best Answer', 'questionhub' ),
										'value'  => ! empty( $settings['enable_best_answer'] ) ? __( 'Enabled', 'questionhub' ) : __( 'Disabled', 'questionhub' ),
										'active' => ! empty( $settings['enable_best_answer'] ),
									],
									[
										'label'  => __( 'View Counter', 'questionhub' ),
										'value'  => ! empty( $settings['enable_question_views'] ) ? __( 'Enabled', 'questionhub' ) : __( 'Disabled', 'questionhub' ),
										'active' => ! empty( $settings['enable_question_views'] ),
									],
									[
										'label'  => __( 'Phone Auth', 'questionhub' ),
										'value'  => ! empty( $settings['enable_phone_auth'] ) ? __( 'Enabled', 'questionhub' ) : __( 'Disabled', 'questionhub' ),
										'active' => ! empty( $settings['enable_phone_auth'] ),
									],
									[
										'label'  => __( 'Guest Replies', 'questionhub' ),
										'value'  => ! empty( $settings['allow_guest_replies'] ) ? __( 'Allowed', 'questionhub' ) : __( 'Blocked', 'questionhub' ),
										'active' => ! empty( $settings['allow_guest_replies'] ),
									],
								];
								foreach ( $overview as $row ) :
								?>
								<div class="qh-settings-row">
									<span class="qh-settings-label"><?php echo esc_html( $row['label'] ); ?></span>
									<span class="qh-settings-val <?php echo $row['active'] ? 'qh-val-on' : 'qh-val-off'; ?>">
										<?php echo esc_html( $row['value'] ); ?>
									</span>
								</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>

				</div><!-- .qh-sidebar-col -->
			</div><!-- .qh-content-grid -->

			<!-- ══════════ PRO BANNER ══════════ -->
			<div class="qh-pro-banner">
				<div class="qh-pro-banner-left">
					<span class="qh-pro-badge">PRO</span>
					<div>
						<h3 class="qh-pro-title"><?php esc_html_e( 'Unlock QuestionHub Pro', 'questionhub' ); ?></h3>
						<p class="qh-pro-desc"><?php esc_html_e( 'SMS OTP login, analytics, private questions, expert answers, and more.', 'questionhub' ); ?></p>
					</div>
				</div>
				<a href="https://github.com/PairDevs/QuestionHub" target="_blank" class="qh-btn qh-btn-white">
					<?php esc_html_e( 'Learn More', 'questionhub' ); ?> →
				</a>
			</div>

		</div><!-- .qh-dash-wrap -->

		<script>
		(function(){
			document.querySelectorAll('.qh-shortcode-code[data-copy]').forEach(function(el){
				el.style.cursor = 'pointer';
				el.addEventListener('click', function(){
					var text = el.getAttribute('data-copy');
					if (navigator.clipboard) {
						navigator.clipboard.writeText(text).then(function(){
							var orig = el.textContent;
							el.textContent = '✓ Copied!';
							setTimeout(function(){ el.textContent = orig; }, 1200);
						});
					}
				});
			});
		})();
		</script>
		<?php
	}
}
