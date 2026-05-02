# QuestionHub

**QuestionHub** is a modern, premium-quality Question & Answer plugin for WordPress. It provides a seamless, AJAX-powered frontend interface for users to ask questions, post replies, vote on answers, and engage with community content. It is designed with a future-proof, highly extensible architecture ready for Pro features, while maintaining an uncompromising, modern UI aesthetic out of the box.

---

## 🌟 Core Features

### Modern Frontend Experience
*   **High-Fidelity UI:** Mobile-first design with responsive grids, subtle gradients, and micro-animations.
*   **AJAX-Powered Everything:** Question submission, answer posting, upvoting, live searching, and pagination all happen instantly without page reloads.
*   **Template Override System:** Automatically routes the `questions` Custom Post Type to beautifully designed, custom-built archive and single templates, bypassing clunky theme defaults.

### Community & Engagement
*   **Voting System:** Users can upvote questions and answers dynamically.
*   **Best Answer Marking:** Question authors can mark an answer as the "Best Answer" (highlights in green and pins to the top).
*   **Admin Verified Answers:** Administrators can officially "Verify" authoritative answers, adding a premium purple diagonal "Verified" ribbon to the answer card.
*   **Dynamic Role Badges:** Visual badges (Admin, Moderator, Author, Member, Guest) are integrated seamlessly next to user avatars.
*   **View Tracking:** Built-in view counter utilizing cookie-based deduplication for accurate metrics.

### Administrative Control
*   **Premium Admin Dashboard:** A custom-built, React-style administrative interface featuring live statistics, quick action buttons, and an intuitive, modern settings panel.
*   **Access Control:** Fine-grained settings to require logins for asking/replying, or toggle to allow guest replies.
*   **Feature Toggles:** Enable or disable voting, views, and best answers via clean UI toggle switches.

---

## 🛠️ Architecture & Developer Guidelines

QuestionHub is built following modern WordPress development standards.

*   **Object-Oriented Design:** PSR-4 autoloading via Composer, utilizing Singletons and structured namespaces (`QuestionHub\Frontend\Inc\...`).
*   **Security First:** Strict adherence to WordPress sanitization (`sanitize_text_field`), escaping (`esc_html`, `esc_url`), and nonces for all form submissions and AJAX requests.
*   **Modular Assets:** CSS is compartmentalized with custom variables (`--questionhub-primary`) for easy theming. JavaScript uses standard jQuery patterns bound to `$(document).on()` for robust event delegation on dynamically loaded AJAX elements.
*   **Extensibility:** Pro features are built on top of the free version strictly via filters, action hooks, and interfaces. No core files need to be modified. See `hooks-filters.md` for a comprehensive list.

---

## 📦 Included Shortcodes

Embed QuestionHub functionality anywhere on your site using these shortcodes:

*   `[questionhub_submit_form]` - The frontend question submission form. (Logged-in users only by default).
*   `[questionhub_questions]` - Lists published questions with AJAX sort/filter controls. Attributes: `category`, `tag`, `orderby`, `per_page`.
*   `[questionhub_search]` - AJAX live search form. Results appear instantly as you type.
*   `[questionhub_auth]` - Combined tabbed login + registration form.
*   `[questionhub_login]` - Standalone phone number + password login form.
*   `[questionhub_register]` - Standalone phone number registration form.
*   `[questionhub_popular_questions]` - Shows top-voted questions.
*   `[questionhub_unanswered_questions]` - Shows questions with zero answers.
*   `[questionhub_dashboard]` - User dashboard showing their own submitted questions.

*For full shortcode documentation, refer to `docs/shortcodes.md`.*

---

## 🚀 Installation & Setup

1.  Upload the `QuestionHub` folder to your `/wp-content/plugins/` directory.
2.  Activate the plugin through the 'Plugins' menu in WordPress.
3.  Navigate to **QuestionHub -> Settings** in the admin dashboard.
4.  **Set up the Ask Page:** 
    * Create a new WordPress page (e.g., "Ask a Question").
    * Add the `[questionhub_submit_form]` shortcode to the page.
    * Go to **QuestionHub -> Settings -> General** and select this page from the "Ask a Question Page" dropdown.
5.  View your questions live by visiting `yoursite.com/questions/`.

---

## 🔮 Roadmap & Pro Version

The architecture includes a framework for the upcoming **QuestionHub Pro** extension. Planned Pro features include:

*   **Auth & Security:** SMS OTP login/register (no password), Multi-provider SMS gateway (Twilio, Vonage), 2FA.
*   **Advanced Content:** Private questions, Paid question unlocking (WooCommerce), File attachments, Code syntax highlighting.
*   **Community Expansion:** User reputation points system, Expert answers, Question bookmarks.
*   **Analytics & Integrations:** Dashboard metrics, Email/SMS notifications, REST API endpoints, Elementor widgets.

*For the full roadmap and Pro architecture details, refer to `docs/roadmap.md` and `docs/pro-plan.md`.*

---

## 📄 License
GPLv2 or later.
