<?php
/**
 * Badge renderer — role-based badges for Q&A participants.
 *
 * @package ASKORA\Frontend\Inc\Comments
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Comments;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Badge {

	/**
	 * Badge definitions: type → [ label, icon dashicon class ]
	 *
	 * @var array
	 */
	private static array $types = [
		'guest'    => [ 'label' => 'Guest',    'icon' => 'dashicons-admin-users' ],
		'admin'    => [ 'label' => 'Admin',    'icon' => 'dashicons-shield' ],
		'moderator'=> [ 'label' => 'Moderator','icon' => 'dashicons-shield-alt' ],
		'author'   => [ 'label' => 'Author',   'icon' => 'dashicons-star-filled' ],
		'member'   => [ 'label' => 'Member',   'icon' => 'dashicons-groups' ],
	];

	/**
	 * Returns HTML badge for a user on a given question post.
	 *
	 * Badge hierarchy (checked in order):
	 *   Guest → Admin → Moderator → Question Author → Member
	 *
	 * @param int      $user_id User ID (0 = guest).
	 * @param int|null $post_id Question post ID — used to detect "Author" badge.
	 * @return string Escaped HTML badge element.
	 * @since 1.0.0
	 */
	public static function get( int $user_id, ?int $post_id = null ): string {

		if ( 0 === $user_id ) {
			$type = 'guest';
		} elseif ( user_can( $user_id, 'manage_options' ) ) {
			$type = 'admin';
		} elseif ( user_can( $user_id, 'edit_others_posts' ) ) {
			$type = 'moderator';
		} elseif ( $post_id && (int) get_post_field( 'post_author', $post_id ) === $user_id ) {
			$type = 'author';
		} else {
			$type = 'member';
		}

		$def   = self::$types[ $type ] ?? self::$types['member'];
		$label = __( $def['label'], 'askora-community-qa' ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
		$icon  = $def['icon'];

		/**
		 * Filters the badge label.
		 *
		 * @param string   $label   Badge label.
		 * @param string   $type    Badge type slug.
		 * @param int      $user_id User ID.
		 * @param int|null $post_id Post ID.
		 * @since 1.0.0
		 */
		$label = apply_filters( 'askora_badge_label', $label, $type, $user_id, $post_id );

		/**
		 * Filters the badge icon dashicon class.
		 *
		 * @param string $icon    Dashicon class.
		 * @param string $type    Badge type slug.
		 * @since 1.0.0
		 */
		$icon = apply_filters( 'askora_badge_icon', $icon, $type );

		return sprintf(
			'<span class="qh-badge qh-badge-%s"><span class="dashicons %s"></span>%s</span>',
			esc_attr( $type ),
			esc_attr( $icon ),
			esc_html( $label )
		);
	}

	/**
	 * Returns all available badge type slugs.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_types(): array {
		return array_keys( self::$types );
	}
}
