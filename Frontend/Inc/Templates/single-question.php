<?php
/**
 * Template: single-question.php
 * Full single question page — loads theme header/footer.
 *
 * @package ASKORA
 */

defined( 'ABSPATH' ) || exit;

use ASKORA\Frontend\Inc\Comments\Badge;
use ASKORA\Frontend\Inc\Comments\AnswerRenderer;
use ASKORA\Frontend\Inc\Questions\ViewCounter;
use ASKORA\Frontend\Inc\Helpers\Permission;

get_header();

// Core data.
$askora_post_id    = get_the_ID();
$askora_post       = get_post( $askora_post_id );
$askora_author_id  = (int) $askora_post->post_author;
$askora_settings   = get_option( 'askora_settings', [] );
$askora_views      = ViewCounter::get( $askora_post_id );
$askora_votes      = (int) get_post_meta( $askora_post_id, '_askora_votes', true );
$askora_answers    = (int) get_comments_number( $askora_post_id );
$askora_best_id    = (int) get_post_meta( $askora_post_id, '_askora_best_answer', true );
$askora_avatar     = get_avatar( $askora_author_id, 48, '', '', [ 'class' => 'qh-single-avatar' ] );
$askora_author_name = get_the_author_meta( 'display_name', $askora_author_id );
$askora_badge      = Badge::get( $askora_author_id, $askora_post_id );
$askora_categories = get_the_terms( $askora_post_id, 'question_category' );
$askora_tags       = get_the_terms( $askora_post_id, 'question_tag' );
$askora_enable_voting = ! empty( $askora_settings['enable_voting'] );
$askora_enable_best   = ! empty( $askora_settings['enable_best_answer'] );
$askora_can_vote   = $askora_enable_voting && is_user_logged_in();
$askora_has_voted  = $askora_can_vote ? (new \ASKORA\Frontend\Inc\Questions\VoteManager())->has_voted_question( $askora_post_id, get_current_user_id() ) : false;
$askora_can_reply  = Permission::can_reply();

// Breadcrumb back link — use admin-configured Questions List Page, fallback to CPT archive.
$askora_questions_list_page_id = (int) ( $askora_settings['questions_list_page_id'] ?? 0 );
if ( $askora_questions_list_page_id > 0 && 'publish' === get_post_status( $askora_questions_list_page_id ) ) {
	$askora_archive_url = get_permalink( $askora_questions_list_page_id );
} else {
	$askora_archive_url = get_post_type_archive_link( 'questions' );
}
?>

<div class="qh-single-page">

	<!-- ── BREADCRUMB ── -->
	<div class="qh-breadcrumb">
		<div class="qh-breadcrumb-inner">
			<a href="<?php echo esc_url( $askora_archive_url ); ?>" class="qh-breadcrumb-back">
				<span class="dashicons dashicons-arrow-left-alt"></span>
				<?php esc_html_e( 'All Questions', 'askora-community-qa' ); ?>
			</a>
			<?php if ( ! empty( $askora_categories ) && ! is_wp_error( $askora_categories ) ) : ?>
			<div class="qh-breadcrumb-cats">
				<?php foreach ( $askora_categories as $askora_cat ) : ?>
				<a href="<?php echo esc_url( get_term_link( $askora_cat ) ); ?>" class="qh-cat-pill">
					<?php echo esc_html( $askora_cat->name ); ?>
				</a>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>

	<div class="qh-single-layout">

		<!-- ── MAIN COLUMN ── -->
		<main class="qh-single-main">

			<!-- QUESTION CARD -->
			<div class="qh-question-card">

				<!-- Vote sidebar -->
				<?php if ( $askora_enable_voting ) : ?>
				<div class="qh-vote-col">
					<button class="qh-vote-upbtn <?php echo esc_attr( $askora_has_voted ? 'voted' : '' ); ?>"
							id="qh-question-vote-btn"
							data-id="<?php echo esc_attr( $askora_post_id ); ?>"
							data-type="question"
							<?php echo ! is_user_logged_in() ? 'title="' . esc_attr__( 'Login to vote', 'askora-community-qa' ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Attribute is escaped above. ?>>
						<span class="dashicons dashicons-arrow-up-alt2"></span>
					</button>
					<span class="qh-vote-num" id="qh-question-vote-count"><?php echo esc_html( $askora_votes ); ?></span>
					<button class="qh-vote-label"><?php esc_html_e( 'votes', 'askora-community-qa' ); ?></button>
				</div>
				<?php endif; ?>

				<!-- Question body -->
				<div class="qh-question-body">
					<h1 class="qh-question-title"><?php the_title(); ?></h1>

					<!-- Author meta bar -->
					<div class="qh-question-meta">
						<?php echo wp_kses_post( $askora_avatar ); ?>
						<div class="qh-meta-info">
							<span class="qh-meta-name"><?php echo esc_html( $askora_author_name ); ?></span>
							<?php echo wp_kses_post( $askora_badge ); ?>
						</div>
						<div class="qh-meta-stats">
							<span class="qh-stat-chip">
								<span class="dashicons dashicons-clock"></span>
								<?php echo esc_html( human_time_diff( strtotime( $askora_post->post_date ), current_time( 'U' ) ) . ' ' . __( 'ago', 'askora-community-qa' ) ); ?>
							</span>
							<span class="qh-stat-chip">
								<span class="dashicons dashicons-visibility"></span>
								<?php
								printf(
									/* translators: %d: view count */
									esc_html( _n( '%d view', '%d views', $askora_views, 'askora-community-qa' ) ),
									esc_html( $askora_views )
								);
								?>
							</span>
							<span class="qh-stat-chip">
								<span class="dashicons dashicons-admin-comments"></span>
								<?php
								printf(
									/* translators: %d: answer count */
									esc_html( _n( '%d answer', '%d answers', $askora_answers, 'askora-community-qa' ) ),
									esc_html( $askora_answers )
								);
								?>
							</span>
						</div>
					</div>

					<!-- Question content -->
					<div class="qh-question-content">
						<?php the_content(); ?>
					</div>

					<!-- Tags -->
					<?php if ( ! empty( $askora_tags ) && ! is_wp_error( $askora_tags ) ) : ?>
					<div class="qh-question-tags">
						<?php foreach ( $askora_tags as $askora_tag ) : ?>
						<a href="<?php echo esc_url( get_term_link( $askora_tag ) ); ?>" class="qh-tag">
							<?php echo esc_html( $askora_tag->name ); ?>
						</a>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- ── ANSWERS SECTION ── -->
			<?php
			$askora_comments = get_comments( [
				'post_id' => $askora_post_id,
				'status'  => 'approve',
				'orderby' => 'comment_date',
				'order'   => 'ASC',
			] );
			?>

			<div class="qh-answers-section">
				<div class="qh-answers-header">
					<h2 class="qh-answers-title">
						<span class="dashicons dashicons-admin-comments"></span>
						<?php
						printf(
							/* translators: %d: answer count */
							esc_html( _n( '%d Answer', '%d Answers', $askora_answers, 'askora-community-qa' ) ),
							esc_html( $askora_answers )
						);
						?>
					</h2>
					<?php if ( $askora_best_id ) : ?>
					<span class="qh-has-accepted-chip">
						<span class="dashicons dashicons-yes-alt"></span>
						<?php esc_html_e( 'Accepted Answer', 'askora-community-qa' ); ?>
					</span>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $askora_comments ) ) : ?>

				<!-- Sort best-first: accepted answer always on top -->
				<?php
				usort( $askora_comments, function( $askora_a, $askora_b ) use ( $askora_best_id ) {
					if ( (int) $askora_a->comment_ID === $askora_best_id ) return -1;
					if ( (int) $askora_b->comment_ID === $askora_best_id ) return 1;
					return strtotime( $askora_a->comment_date ) - strtotime( $askora_b->comment_date );
				} );
				?>

				<div class="qh-answer-list">
					<?php foreach ( $askora_comments as $askora_comment ) :
						$askora_uid        = (int) $askora_comment->user_id;
						$askora_av         = get_avatar( $askora_comment, 44, '', '', [ 'class' => 'qh-answer-avatar' ] );
						$askora_cb         = Badge::get( $askora_uid, $askora_post_id );
						$askora_is_best    = (int) $askora_comment->comment_ID === $askora_best_id;
						$askora_ans_votes  = (int) get_comment_meta( $askora_comment->comment_ID, '_askora_answer_votes', true );
						$askora_can_best   = Permission::can_mark_best_answer( $askora_post_id );
						$askora_is_verified = (bool) get_comment_meta( $askora_comment->comment_ID, '_askora_verified', true );
						$askora_is_admin    = current_user_can( 'manage_options' );
					?>
					<div class="qh-answer-item <?php echo esc_attr( $askora_is_best ? 'qh-answer-best' : '' ); ?> <?php echo esc_attr( $askora_is_verified ? 'qh-answer-verified' : '' ); ?>" id="answer-<?php echo esc_attr( $askora_comment->comment_ID ); ?>">

						<!-- Verified ribbon — top-right corner -->
						<?php if ( $askora_is_verified ) : ?>
						<div class="qh-verified-ribbon" aria-label="<?php esc_attr_e( 'Admin Verified', 'askora-community-qa' ); ?>">
							<span class="dashicons dashicons-yes"></span>
							<?php esc_html_e( 'Verified', 'askora-community-qa' ); ?>
						</div>
						<?php endif; ?>

						<!-- Accepted banner -->
						<?php if ( $askora_is_best ) : ?>
						<div class="qh-accepted-banner">
							<span class="dashicons dashicons-yes-alt"></span>
							<?php esc_html_e( 'Accepted Answer', 'askora-community-qa' ); ?>
						</div>
						<?php endif; ?>

						<div class="qh-answer-inner">

							<!-- Left: vote col -->
							<?php if ( $askora_enable_voting ) : ?>
							<div class="qh-answer-vote-col">
								<button class="qh-vote-upbtn qh-vote-answer-btn"
										data-id="<?php echo esc_attr( $askora_comment->comment_ID ); ?>"
										data-type="answer">
									<span class="dashicons dashicons-arrow-up-alt2"></span>
								</button>
								<span class="qh-vote-num qh-vote-count"><?php echo esc_html( $askora_ans_votes ); ?></span>
								<?php if ( $askora_is_best ) : ?>
								<span class="qh-accepted-check dashicons dashicons-yes-alt" title="<?php esc_attr_e( 'Accepted', 'askora-community-qa' ); ?>"></span>
								<?php endif; ?>
							</div>
							<?php endif; ?>

							<!-- Right: content -->
							<div class="qh-answer-content-col">

								<!-- Author row -->
								<div class="qh-answer-author-row">
									<?php echo wp_kses_post( $askora_av ); ?>
									<div class="qh-answer-author-info">
										<span class="qh-answer-author-name">
											<?php echo esc_html( $askora_comment->comment_author ); ?>
										</span>
										<?php echo wp_kses_post( $askora_cb ); ?>
									</div>
									<span class="qh-answer-date">
										<?php echo esc_html( human_time_diff( strtotime( $askora_comment->comment_date ), current_time( 'U' ) ) . ' ' . __( 'ago', 'askora-community-qa' ) ); ?>
									</span>
								</div>

								<!-- Answer text -->
								<div class="qh-answer-text">
									<?php echo wp_kses_post( $askora_comment->comment_content ); ?>
								</div>

								<!-- Action row -->
								<div class="qh-answer-actions-row">
									<?php if ( $askora_enable_best && $askora_can_best && ! $askora_is_best ) : ?>
									<button class="qh-mark-best-btn askora-mark-best-btn"
											data-comment-id="<?php echo esc_attr( $askora_comment->comment_ID ); ?>"
											data-post-id="<?php echo esc_attr( $askora_post_id ); ?>">
										<span class="dashicons dashicons-yes-alt"></span>
										<?php esc_html_e( 'Accept Answer', 'askora-community-qa' ); ?>
									</button>
									<?php endif; ?>

									<?php if ( $askora_is_admin ) : ?>
									<button class="qh-verify-answer-btn <?php echo esc_attr( $askora_is_verified ? 'qh-verify-active' : '' ); ?>"
											data-comment-id="<?php echo esc_attr( $askora_comment->comment_ID ); ?>"
											title="<?php echo esc_attr( $askora_is_verified ? __( 'Remove verification', 'askora-community-qa' ) : __( 'Mark as Admin Verified', 'askora-community-qa' ) ); ?>">
										<span class="dashicons dashicons-<?php echo esc_attr( $askora_is_verified ? 'dismiss' : 'awards' ); ?>"></span>
										<span class="qh-verify-label">
											<?php echo esc_html( $askora_is_verified ? __( 'Remove Verification', 'askora-community-qa' ) : __( 'Verify Answer', 'askora-community-qa' ) ); ?>
										</span>
									</button>
									<?php endif; ?>
								</div>

							</div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>

				<?php else : ?>
				<div class="qh-no-answers">
					<span class="dashicons dashicons-admin-comments qh-no-answers-icon"></span>
					<p><?php esc_html_e( 'No answers yet. Be the first to answer!', 'askora-community-qa' ); ?></p>
				</div>
				<?php endif; ?>
			</div><!-- .qh-answers-section -->

			<!-- ── ANSWER FORM ── -->
			<div class="qh-answer-form-wrap" id="qh-answer-form-wrap">
				<h3 class="qh-form-section-title">
					<span class="dashicons dashicons-edit"></span>
					<?php esc_html_e( 'Your Answer', 'askora-community-qa' ); ?>
				</h3>

				<div class="qh-form-alert qh-form-alert-success" id="askora-answer-success" style="display:none;"></div>
				<div class="qh-form-alert qh-form-alert-error"   id="askora-answer-error"   style="display:none;"></div>

				<?php if ( $askora_can_reply ) : ?>
				<form id="askora-answer-form" class="qh-answer-form" novalidate>
					<?php wp_nonce_field( 'askora_nonce', 'askora_nonce_field' ); ?>
					<input type="hidden" name="post_id" value="<?php echo esc_attr( $askora_post_id ); ?>">
					<div class="qh-form-group">
						<textarea name="content"
								  class="qh-textarea"
								  rows="7"
								  placeholder="<?php esc_attr_e( 'Write a detailed, helpful answer…', 'askora-community-qa' ); ?>"
								  required></textarea>
					</div>
					<div class="qh-form-footer">
						<?php if ( is_user_logged_in() ) :
							$askora_cu = wp_get_current_user();
							echo wp_kses_post( get_avatar( $askora_cu->ID, 36, '', '', [ 'class' => 'qh-form-user-avatar' ] ) );
						?>
						<span class="qh-form-user-name"><?php echo esc_html( $askora_cu->display_name ); ?></span>
						<?php endif; ?>
						<button type="submit" class="qh-submit-btn" id="qh-submit-answer-btn">
							<span class="qh-btn-text"><?php esc_html_e( 'Post Answer', 'askora-community-qa' ); ?></span>
							<span class="qh-btn-spinner" style="display:none;"></span>
						</button>
					</div>
				</form>
				<?php else : ?>
				<div class="qh-login-wall">
					<span class="dashicons dashicons-lock qh-login-wall-icon"></span>
					<p>
						<?php esc_html_e( 'You must be logged in to post an answer.', 'askora-community-qa' ); ?>
					</p>
					<a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="qh-login-btn">
						<span class="dashicons dashicons-admin-users"></span>
						<?php esc_html_e( 'Log In to Answer', 'askora-community-qa' ); ?>
					</a>
				</div>
				<?php endif; ?>
			</div>

		</main><!-- .qh-single-main -->

		<!-- ── SIDEBAR ── -->
		<aside class="qh-single-sidebar">

			<!-- Question Stats -->
			<div class="qh-sidebar-widget">
				<h4 class="qh-sidebar-widget-title">
					<span class="dashicons dashicons-chart-bar"></span>
					<?php esc_html_e( 'Question Stats', 'askora-community-qa' ); ?>
				</h4>
				<ul class="qh-stats-list">
					<li>
						<span class="qh-stats-label"><?php esc_html_e( 'Asked', 'askora-community-qa' ); ?></span>
						<span class="qh-stats-value"><?php echo esc_html( get_the_date( 'M j, Y', $askora_post_id ) ); ?></span>
					</li>
					<li>
						<span class="qh-stats-label"><?php esc_html_e( 'Views', 'askora-community-qa' ); ?></span>
						<span class="qh-stats-value"><?php echo esc_html( number_format( $askora_views ) ); ?></span>
					</li>
					<li>
						<span class="qh-stats-label"><?php esc_html_e( 'Answers', 'askora-community-qa' ); ?></span>
						<span class="qh-stats-value"><?php echo esc_html( $askora_answers ); ?></span>
					</li>
					<li>
						<span class="qh-stats-label"><?php esc_html_e( 'Votes', 'askora-community-qa' ); ?></span>
						<span class="qh-stats-value"><?php echo esc_html( $askora_votes ); ?></span>
					</li>
					<?php if ( get_current_user_id() === (int) $askora_post->post_author && $askora_post->post_status !== 'publish' ) : ?>
					<li>
						<span class="qh-stats-label"><?php esc_html_e( 'Status', 'askora-community-qa' ); ?></span>
						<span class="qh-stats-value"><span class="askora-status-badge askora-status-<?php echo esc_attr( $askora_post->post_status ); ?>"><?php echo esc_html( ucfirst( $askora_post->post_status ) ); ?></span></span>
					</li>
					<?php endif; ?>
				</ul>
			</div>

			<!-- Categories -->
			<?php if ( ! empty( $askora_categories ) && ! is_wp_error( $askora_categories ) ) : ?>
			<div class="qh-sidebar-widget">
				<h4 class="qh-sidebar-widget-title">
					<span class="dashicons dashicons-category"></span>
					<?php esc_html_e( 'Categories', 'askora-community-qa' ); ?>
				</h4>
				<div class="qh-sidebar-cats">
					<?php foreach ( $askora_categories as $askora_cat ) : ?>
					<a href="<?php echo esc_url( get_term_link( $askora_cat ) ); ?>" class="qh-sidebar-cat">
						<?php echo esc_html( $askora_cat->name ); ?>
						<span class="qh-sidebar-cat-count"><?php echo esc_html( $askora_cat->count ); ?></span>
					</a>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>

			<!-- Tags -->
			<?php if ( ! empty( $askora_tags ) && ! is_wp_error( $askora_tags ) ) : ?>
			<div class="qh-sidebar-widget">
				<h4 class="qh-sidebar-widget-title">
					<span class="dashicons dashicons-tag"></span>
					<?php esc_html_e( 'Tags', 'askora-community-qa' ); ?>
				</h4>
				<div class="qh-sidebar-tags">
					<?php foreach ( $askora_tags as $askora_tag ) : ?>
					<a href="<?php echo esc_url( get_term_link( $askora_tag ) ); ?>" class="qh-tag">
						<?php echo esc_html( $askora_tag->name ); ?>
					</a>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>

			<!-- Related Questions -->
			<?php
			$askora_related_count = 0;
			$askora_related       = new WP_Query( [
				'post_type'      => 'questions',
				'post_status'    => 'publish',
				'posts_per_page' => 6,
				'orderby'        => 'rand',
			] );
			if ( $askora_related->have_posts() ) :
			?>
			<div class="qh-sidebar-widget">
				<h4 class="qh-sidebar-widget-title">
					<span class="dashicons dashicons-list-view"></span>
					<?php esc_html_e( 'Related Questions', 'askora-community-qa' ); ?>
				</h4>
				<ul class="qh-related-list">
					<?php while ( $askora_related->have_posts() ) : $askora_related->the_post(); ?>
					<?php
					if ( get_the_ID() === $askora_post_id || $askora_related_count >= 5 ) {
						continue;
					}
					++$askora_related_count;
					?>
					<li class="qh-related-item">
						<span class="qh-related-count"><?php echo esc_html( get_comments_number() ); ?></span>
						<a href="<?php the_permalink(); ?>" class="qh-related-link"><?php the_title(); ?></a>
					</li>
					<?php endwhile; wp_reset_postdata(); ?>
				</ul>
			</div>
			<?php endif; ?>

		</aside>

	</div><!-- .qh-single-layout -->
</div><!-- .qh-single-page -->

<?php get_footer(); ?>
