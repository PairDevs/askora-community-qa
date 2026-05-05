<?php
/**
 * Template: single-question.php
 * Full single question page — loads theme header/footer.
 *
 * @package QuestionHub
 */

defined( 'ABSPATH' ) || exit;

use QuestionHub\Frontend\Inc\Comments\Badge;
use QuestionHub\Frontend\Inc\Comments\AnswerRenderer;
use QuestionHub\Frontend\Inc\Questions\ViewCounter;
use QuestionHub\Frontend\Inc\Helpers\Permission;

get_header();

// Core data.
$post_id    = get_the_ID();
$post       = get_post( $post_id );
$author_id  = (int) $post->post_author;
$settings   = get_option( 'questionhub_settings', [] );
$views      = ViewCounter::get( $post_id );
$votes      = (int) get_post_meta( $post_id, '_questionhub_votes', true );
$answers    = (int) get_comments_number( $post_id );
$best_id    = (int) get_post_meta( $post_id, '_questionhub_best_answer', true );
$avatar     = get_avatar( $author_id, 48, '', '', [ 'class' => 'qh-single-avatar' ] );
$author_name = get_the_author_meta( 'display_name', $author_id );
$badge      = Badge::get( $author_id, $post_id );
$categories = get_the_terms( $post_id, 'question_category' );
$tags       = get_the_terms( $post_id, 'question_tag' );
$enable_voting = ! empty( $settings['enable_voting'] );
$enable_best   = ! empty( $settings['enable_best_answer'] );
$can_vote   = $enable_voting && is_user_logged_in();
$has_voted  = $can_vote ? (new \QuestionHub\Frontend\Inc\Questions\VoteManager())->has_voted_question( $post_id, get_current_user_id() ) : false;
$can_reply  = Permission::can_reply();

// Breadcrumb back link.
$archive_url = get_post_type_archive_link( 'questions' );
?>

<div class="qh-single-page">

	<!-- ── BREADCRUMB ── -->
	<div class="qh-breadcrumb">
		<div class="qh-breadcrumb-inner">
			<a href="<?php echo esc_url( $archive_url ); ?>" class="qh-breadcrumb-back">
				<span class="dashicons dashicons-arrow-left-alt"></span>
				<?php esc_html_e( 'All Questions', 'questionhub' ); ?>
			</a>
			<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
			<div class="qh-breadcrumb-cats">
				<?php foreach ( $categories as $cat ) : ?>
				<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="qh-cat-pill">
					<?php echo esc_html( $cat->name ); ?>
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
				<?php if ( $enable_voting ) : ?>
				<div class="qh-vote-col">
					<button class="qh-vote-upbtn <?php echo $has_voted ? 'voted' : ''; ?>"
							id="qh-question-vote-btn"
							data-id="<?php echo esc_attr( $post_id ); ?>"
							data-type="question"
							<?php echo ! is_user_logged_in() ? 'title="' . esc_attr__( 'Login to vote', 'questionhub' ) . '"' : ''; ?>>
						<span class="dashicons dashicons-arrow-up-alt2"></span>
					</button>
					<span class="qh-vote-num" id="qh-question-vote-count"><?php echo esc_html( $votes ); ?></span>
					<button class="qh-vote-label"><?php esc_html_e( 'votes', 'questionhub' ); ?></button>
				</div>
				<?php endif; ?>

				<!-- Question body -->
				<div class="qh-question-body">
					<h1 class="qh-question-title"><?php the_title(); ?></h1>

					<!-- Author meta bar -->
					<div class="qh-question-meta">
						<?php echo $avatar; // phpcs:ignore ?>
						<div class="qh-meta-info">
							<span class="qh-meta-name"><?php echo esc_html( $author_name ); ?></span>
							<?php echo $badge; // phpcs:ignore ?>
						</div>
						<div class="qh-meta-stats">
							<span class="qh-stat-chip">
								<span class="dashicons dashicons-clock"></span>
								<?php echo esc_html( human_time_diff( strtotime( $post->post_date ), current_time( 'U' ) ) . ' ' . __( 'ago', 'questionhub' ) ); ?>
							</span>
							<span class="qh-stat-chip">
								<span class="dashicons dashicons-visibility"></span>
								<?php
								printf(
									/* translators: %d: view count */
									esc_html( _n( '%d view', '%d views', $views, 'questionhub' ) ),
									esc_html( $views )
								);
								?>
							</span>
							<span class="qh-stat-chip">
								<span class="dashicons dashicons-admin-comments"></span>
								<?php
								printf(
									/* translators: %d: answer count */
									esc_html( _n( '%d answer', '%d answers', $answers, 'questionhub' ) ),
									esc_html( $answers )
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
					<?php if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) : ?>
					<div class="qh-question-tags">
						<?php foreach ( $tags as $tag ) : ?>
						<a href="<?php echo esc_url( get_term_link( $tag ) ); ?>" class="qh-tag">
							<?php echo esc_html( $tag->name ); ?>
						</a>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- ── ANSWERS SECTION ── -->
			<?php
			$comments = get_comments( [
				'post_id' => $post_id,
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
							esc_html( _n( '%d Answer', '%d Answers', $answers, 'questionhub' ) ),
							esc_html( $answers )
						);
						?>
					</h2>
					<?php if ( $best_id ) : ?>
					<span class="qh-has-accepted-chip">
						<span class="dashicons dashicons-yes-alt"></span>
						<?php esc_html_e( 'Accepted Answer', 'questionhub' ); ?>
					</span>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $comments ) ) : ?>

				<!-- Sort best-first: accepted answer always on top -->
				<?php
				usort( $comments, function( $a, $b ) use ( $best_id ) {
					if ( (int) $a->comment_ID === $best_id ) return -1;
					if ( (int) $b->comment_ID === $best_id ) return 1;
					return strtotime( $a->comment_date ) - strtotime( $b->comment_date );
				} );
				?>

				<div class="qh-answer-list">
					<?php foreach ( $comments as $comment ) :
						$uid        = (int) $comment->user_id;
						$av         = get_avatar( $comment, 44, '', '', [ 'class' => 'qh-answer-avatar' ] );
						$cb         = Badge::get( $uid, $post_id );
						$is_best    = (int) $comment->comment_ID === $best_id;
						$ans_votes  = (int) get_comment_meta( $comment->comment_ID, '_questionhub_answer_votes', true );
						$can_best   = Permission::can_mark_best_answer( $post_id );
						$is_verified = (bool) get_comment_meta( $comment->comment_ID, '_questionhub_verified', true );
						$is_admin    = current_user_can( 'manage_options' );
					?>
					<div class="qh-answer-item <?php echo $is_best ? 'qh-answer-best' : ''; ?> <?php echo $is_verified ? 'qh-answer-verified' : ''; ?>" id="answer-<?php echo esc_attr( $comment->comment_ID ); ?>">

						<!-- Verified ribbon — top-right corner -->
						<?php if ( $is_verified ) : ?>
						<div class="qh-verified-ribbon" aria-label="<?php esc_attr_e( 'Admin Verified', 'questionhub' ); ?>">
							<span class="dashicons dashicons-yes"></span>
							<?php esc_html_e( 'Verified', 'questionhub' ); ?>
						</div>
						<?php endif; ?>

						<!-- Accepted banner -->
						<?php if ( $is_best ) : ?>
						<div class="qh-accepted-banner">
							<span class="dashicons dashicons-yes-alt"></span>
							<?php esc_html_e( 'Accepted Answer', 'questionhub' ); ?>
						</div>
						<?php endif; ?>

						<div class="qh-answer-inner">

							<!-- Left: vote col -->
							<?php if ( $enable_voting ) : ?>
							<div class="qh-answer-vote-col">
								<button class="qh-vote-upbtn qh-vote-answer-btn"
										data-id="<?php echo esc_attr( $comment->comment_ID ); ?>"
										data-type="answer">
									<span class="dashicons dashicons-arrow-up-alt2"></span>
								</button>
								<span class="qh-vote-num qh-vote-count"><?php echo esc_html( $ans_votes ); ?></span>
								<?php if ( $is_best ) : ?>
								<span class="qh-accepted-check dashicons dashicons-yes-alt" title="<?php esc_attr_e( 'Accepted', 'questionhub' ); ?>"></span>
								<?php endif; ?>
							</div>
							<?php endif; ?>

							<!-- Right: content -->
							<div class="qh-answer-content-col">

								<!-- Author row -->
								<div class="qh-answer-author-row">
									<?php echo $av; // phpcs:ignore ?>
									<div class="qh-answer-author-info">
										<span class="qh-answer-author-name">
											<?php echo esc_html( $comment->comment_author ); ?>
										</span>
										<?php echo $cb; // phpcs:ignore — Badge::get() returns pre-escaped HTML ?>
									</div>
									<span class="qh-answer-date">
										<?php echo esc_html( human_time_diff( strtotime( $comment->comment_date ), current_time( 'U' ) ) . ' ' . __( 'ago', 'questionhub' ) ); ?>
									</span>
								</div>

								<!-- Answer text -->
								<div class="qh-answer-text">
									<?php echo wp_kses_post( $comment->comment_content ); ?>
								</div>

								<!-- Action row -->
								<div class="qh-answer-actions-row">
									<?php if ( $enable_best && $can_best && ! $is_best ) : ?>
									<button class="qh-mark-best-btn questionhub-mark-best-btn"
											data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>"
											data-post-id="<?php echo esc_attr( $post_id ); ?>">
										<span class="dashicons dashicons-yes-alt"></span>
										<?php esc_html_e( 'Accept Answer', 'questionhub' ); ?>
									</button>
									<?php endif; ?>

									<?php if ( $is_admin ) : ?>
									<button class="qh-verify-answer-btn <?php echo $is_verified ? 'qh-verify-active' : ''; ?>"
											data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>"
											title="<?php echo $is_verified ? esc_attr__( 'Remove verification', 'questionhub' ) : esc_attr__( 'Mark as Admin Verified', 'questionhub' ); ?>">
										<span class="dashicons dashicons-<?php echo $is_verified ? 'dismiss' : 'awards'; ?>"></span>
										<span class="qh-verify-label">
											<?php echo $is_verified ? esc_html__( 'Remove Verification', 'questionhub' ) : esc_html__( 'Verify Answer', 'questionhub' ); ?>
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
					<p><?php esc_html_e( 'No answers yet. Be the first to answer!', 'questionhub' ); ?></p>
				</div>
				<?php endif; ?>
			</div><!-- .qh-answers-section -->

			<!-- ── ANSWER FORM ── -->
			<div class="qh-answer-form-wrap" id="qh-answer-form-wrap">
				<h3 class="qh-form-section-title">
					<span class="dashicons dashicons-edit"></span>
					<?php esc_html_e( 'Your Answer', 'questionhub' ); ?>
				</h3>

				<div class="qh-form-alert qh-form-alert-success" id="questionhub-answer-success" style="display:none;"></div>
				<div class="qh-form-alert qh-form-alert-error"   id="questionhub-answer-error"   style="display:none;"></div>

				<?php if ( $can_reply ) : ?>
				<form id="questionhub-answer-form" class="qh-answer-form" novalidate>
					<?php wp_nonce_field( 'questionhub_nonce', 'questionhub_nonce_field' ); ?>
					<input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>">
					<div class="qh-form-group">
						<textarea name="content"
								  class="qh-textarea"
								  rows="7"
								  placeholder="<?php esc_attr_e( 'Write a detailed, helpful answer…', 'questionhub' ); ?>"
								  required></textarea>
					</div>
					<div class="qh-form-footer">
						<?php if ( is_user_logged_in() ) :
							$cu = wp_get_current_user();
							echo get_avatar( $cu->ID, 36, '', '', [ 'class' => 'qh-form-user-avatar' ] ); // phpcs:ignore
						?>
						<span class="qh-form-user-name"><?php echo esc_html( $cu->display_name ); ?></span>
						<?php endif; ?>
						<button type="submit" class="qh-submit-btn" id="qh-submit-answer-btn">
							<span class="qh-btn-text"><?php esc_html_e( 'Post Answer', 'questionhub' ); ?></span>
							<span class="qh-btn-spinner" style="display:none;"></span>
						</button>
					</div>
				</form>
				<?php else : ?>
				<div class="qh-login-wall">
					<span class="dashicons dashicons-lock qh-login-wall-icon"></span>
					<p>
						<?php esc_html_e( 'You must be logged in to post an answer.', 'questionhub' ); ?>
					</p>
					<a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="qh-login-btn">
						<span class="dashicons dashicons-admin-users"></span>
						<?php esc_html_e( 'Log In to Answer', 'questionhub' ); ?>
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
					<?php esc_html_e( 'Question Stats', 'questionhub' ); ?>
				</h4>
				<ul class="qh-stats-list">
					<li>
						<span class="qh-stats-label"><?php esc_html_e( 'Asked', 'questionhub' ); ?></span>
						<span class="qh-stats-value"><?php echo esc_html( get_the_date( 'M j, Y', $post_id ) ); ?></span>
					</li>
					<li>
						<span class="qh-stats-label"><?php esc_html_e( 'Views', 'questionhub' ); ?></span>
						<span class="qh-stats-value"><?php echo esc_html( number_format( $views ) ); ?></span>
					</li>
					<li>
						<span class="qh-stats-label"><?php esc_html_e( 'Answers', 'questionhub' ); ?></span>
						<span class="qh-stats-value"><?php echo esc_html( $answers ); ?></span>
					</li>
					<li>
						<span class="qh-stats-label"><?php esc_html_e( 'Votes', 'questionhub' ); ?></span>
						<span class="qh-stats-value"><?php echo esc_html( $votes ); ?></span>
					</li>
					<?php if ( get_current_user_id() === (int) $post->post_author && $post->post_status !== 'publish' ) : ?>
					<li>
						<span class="qh-stats-label"><?php esc_html_e( 'Status', 'questionhub' ); ?></span>
						<span class="qh-stats-value"><span class="questionhub-status-badge questionhub-status-<?php echo esc_attr( $post->post_status ); ?>"><?php echo esc_html( ucfirst( $post->post_status ) ); ?></span></span>
					</li>
					<?php endif; ?>
				</ul>
			</div>

			<!-- Categories -->
			<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
			<div class="qh-sidebar-widget">
				<h4 class="qh-sidebar-widget-title">
					<span class="dashicons dashicons-category"></span>
					<?php esc_html_e( 'Categories', 'questionhub' ); ?>
				</h4>
				<div class="qh-sidebar-cats">
					<?php foreach ( $categories as $cat ) : ?>
					<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="qh-sidebar-cat">
						<?php echo esc_html( $cat->name ); ?>
						<span class="qh-sidebar-cat-count"><?php echo esc_html( $cat->count ); ?></span>
					</a>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>

			<!-- Tags -->
			<?php if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) : ?>
			<div class="qh-sidebar-widget">
				<h4 class="qh-sidebar-widget-title">
					<span class="dashicons dashicons-tag"></span>
					<?php esc_html_e( 'Tags', 'questionhub' ); ?>
				</h4>
				<div class="qh-sidebar-tags">
					<?php foreach ( $tags as $tag ) : ?>
					<a href="<?php echo esc_url( get_term_link( $tag ) ); ?>" class="qh-tag">
						<?php echo esc_html( $tag->name ); ?>
					</a>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>

			<!-- Related Questions -->
			<?php
			$related = new WP_Query( [
				'post_type'      => 'questions',
				'post_status'    => 'publish',
				'posts_per_page' => 5,
				'post__not_in'   => [ $post_id ],
				'orderby'        => 'rand',
			] );
			if ( $related->have_posts() ) :
			?>
			<div class="qh-sidebar-widget">
				<h4 class="qh-sidebar-widget-title">
					<span class="dashicons dashicons-list-view"></span>
					<?php esc_html_e( 'Related Questions', 'questionhub' ); ?>
				</h4>
				<ul class="qh-related-list">
					<?php while ( $related->have_posts() ) : $related->the_post(); ?>
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
