<?php
/**
 * Template: archive-shortcode.php
 * Renders the /questions/ CPT archive using the same output as [questionhub_questions].
 *
 * @package QuestionHub
 */

defined( 'ABSPATH' ) || exit;

get_header();

$atts = [
	'category' => '',
	'tag'      => '',
	'orderby'  => 'date',
	'per_page' => '',
];

// Pass through taxonomy context when visiting a category/tag archive URL.
if ( is_tax( 'question_category' ) ) {
	$term             = get_queried_object();
	$atts['category'] = $term ? $term->term_id : '';
} elseif ( is_tax( 'question_tag' ) ) {
	$term         = get_queried_object();
	$atts['tag']  = $term ? $term->slug : '';
}
?>

<div class="questionhub-archive-page questionhub-archive-shortcode-view">
	<?php \QuestionHub\Frontend\Inc\Helpers\Template::load( 'question-list.php', [ 'atts' => $atts ] ); ?>
</div>

<?php get_footer();
