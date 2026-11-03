=== Askora Community Q&A ===
Contributors: hmbashar, huzaifaalmesbah
Tags: question answer, Q&A, forum, community, questions
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A modern Q&A plugin for WordPress with frontend question submission, AJAX replies, phone login, search, views, badges, and beautiful UI.

== Description ==

Askora Community Q&A is a powerful, modern Q&A plugin for WordPress. It enables your community to ask questions, browse answers, reply with rich interactions, and discover helpful content — all through a beautiful AJAX-powered frontend.

**Free Features:**

* Custom post type for questions with categories and tags
* Frontend question submission form (shortcode)
* Rich text (TinyMCE) editor for questions and answers, with bold, italic, lists, quotes and links — can be switched off in the settings
* Distinct, customizable colors for categories and tags
* Phone number registration and login (no email required)
* AJAX-powered answers/replies using the WordPress comment system, with core moderation, flood control and duplicate protection
* AJAX search with live results
* AJAX load more and pagination
* Question view counter
* Basic voting/upvote system for questions and answers
* Best answer marking by question author or admin
* Admin/Author/Moderator/Member/Guest role badges
* Responsive, modern card-based UI
* Admin settings page with tabs
* Custom admin columns (Views, Replies, Votes)
* Translation-ready (text domain `askora-community-qa`)
* Developer hooks and filters
* Future Pro architecture (feature gates, SMS stubs, interfaces)
* WordPress.org compliant — no external CDN, no tracking

**Available Shortcodes:**

* `[askora_submit_form]` — Question submission form
* `[askora_questions]` — Question list with AJAX pagination
* `[askora_search]` — AJAX live search
* `[askora_login]` — Phone login form
* `[askora_register]` — Phone registration form
* `[askora_auth]` — Combined login/register form
* `[askora_popular_questions]` — Most upvoted questions
* `[askora_unanswered_questions]` — Questions with no answers

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/askora-community-qa/`, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Go to **Askora → Settings** to configure the plugin.
4. Create pages and add shortcodes as needed.
5. Permalinks are refreshed automatically on activation. If single question pages ever show a 404, go to **Settings → Permalinks** and click **Save Changes**.

== Frequently Asked Questions ==

= Do users need an email to register? =

No. Askora Community Q&A supports phone number registration and login without requiring an email address. An email is optional.

= Can guests ask questions? =

By default, users must be logged in to ask a question. Turn off **Require Login to Ask** in the settings to let guests ask. Guest answers are controlled by **Require Login to Reply** and **Allow Guest Replies**; guests are asked for a name and email, and WordPress comment moderation applies to their answers.

= Can I use a visual editor for questions and answers? =

Yes. Questions and answers use the WordPress visual editor (TinyMCE) by default. You can switch to a plain textarea under **Askora → Settings → Advanced → Rich Text Editor**.

= Can I change the color of categories and tags? =

Yes. Under **Askora → Settings → Advanced** you can set the Category Color and Tag Color. Categories are shown as filled pills and tags as outlined chips with a "#" prefix, so they are easy to tell apart.

= How do I show questions on a page? =

Add the `[askora_questions]` shortcode to any page or post.

= Can I override templates in my theme? =

Yes. Create a folder `askora-community-qa/` in your active theme and copy any template file from `Frontend/Inc/Templates/` into it. The plugin will load your theme version automatically.

= Is it translation-ready? =

Yes. All strings use the `askora-community-qa` text domain, so translations can be contributed through translate.wordpress.org.

== Screenshots ==

1. Question list with card layout
2. Single question page with answers
3. Phone number registration form
4. Admin settings page
5. Question submission form

== Changelog ==

= 1.1.0 =
* New: Rich text (TinyMCE) editor for the question and answer forms, with a simple toolbar (bold, italic, underline, strikethrough, lists, quote, link). Switch it off under Askora → Settings → Advanced → Rich Text Editor.
* New: Categories and tags now have different looks — categories are filled pills, tags are outlined chips with a "#" prefix — and each has its own color setting (Category Color and Tag Color) under Askora → Settings → Advanced.
* New: Guests who are allowed to reply are asked for a name and email, as WordPress core requires for comments.
* New: Filters `askora_enable_rich_editor`, `askora_editor_settings`, `askora_allow_registration`, `askora_registrations_per_hour` and `askora_max_tags`.
* Security: Passwords are no longer run through a text sanitizer, which could silently alter passwords containing tags, `%` sequences or similar characters.
* Security: New phone accounts get a random username. It was previously derived from a hash of the phone number, which exposed the number through author URLs and let attackers bypass the login lockout via wp-login.php.
* Security: Phone registration is limited to 5 accounts per IP per hour, has a hidden honeypot field, and can be closed with the `askora_allow_registration` filter.
* Security: Answers are now submitted through the WordPress comment pipeline, so comment moderation, the disallowed-words list, flood control, duplicate detection, closed comments and unpublished questions are respected, and the commenter's IP and user agent are recorded.
* Security: Voting, best-answer and verify-answer requests now verify that the target is a real, published question or an approved answer on a question. The vote and best-answer switches in the settings are enforced on the server too.
* Fix: Backslashes were removed from question and answer text (for example `C:\Users` became `C:Users`).
* Fix: Single question pages returned a 404 right after activation until permalinks were re-saved.
* Fix: The phone number field on the user profile screen never saved because its security nonce was not output.
* Fix: "Most Viewed" ordering and `[askora_popular_questions]` hid questions that had no view counter yet. Popular questions are now ordered by votes, as documented, and a new "Most Voted" sort option was added.
* Fix: `[askora_questions tag="…"]` ignored the tag on first load, and the tag, as well as the unanswered filter, was dropped after changing the sort, category or search. `[askora_unanswered_questions]` lost its filter on "Load More".
* Fix: The "Require Login to Ask" setting had no effect because guests were always rejected. "Phone Registration/Login" and "Email Required" are now enforced on the server as well.
* Fix: The login prompt now points to the plugin's own phone login page instead of wp-login.php.
* Fix: Author names with `&` or quotes were double-escaped in the answer list.
* Fix: Category and tag IDs submitted with a question are validated, and tags are limited to 5 per question.
* Fix: Display names containing a backslash were corrupted on registration.
* Fix: A missing or invalid color setting no longer produces a broken stylesheet value.
* Fix: Uninstall with "Delete All Data" now really removes questions, answers, categories, tags and the verify-answer data.
* Improvement: Vote totals are derived from the voter list so they cannot drift apart when requests overlap; new questions start with view and vote counters.
* Improvement: `auth.js` now declares its dependency on the main script.
* Improvement: The "Plugin asset and readme update" GitHub workflow now runs on the `main` branch, and the release workflow checks that the release tag, plugin version and Stable tag match before deploying.
* Tested with WordPress 7.1.4.

= 1.0.0 =
* Initial release.
* Question custom post type with categories and tags.
* Frontend shortcodes for questions, answers, search, auth.
* Phone number registration and login.
* AJAX question/answer submission.
* AJAX search, load more, filtering.
* View counter, voting, best answer.
* Role badges (Admin, Author, Moderator, Member, Guest).
* Responsive modern UI.
* Admin settings with tabs.
* Full developer hooks and filters.

== Upgrade Notice ==

= 1.1.0 =
Adds a rich text editor and distinct category/tag colors, and includes important security hardening for answers, voting and phone registration. Recommended for all sites. Answers now follow your WordPress comment moderation settings.

= 1.0.0 =
Initial release. No upgrade needed.
