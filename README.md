# QuestionHub

**QuestionHub** is a modern, premium-quality Question & Answer plugin for WordPress. It provides a seamless, AJAX-powered frontend interface for users to ask questions, post replies, vote on answers, and engage with community content.

## Features

*   **Modern Frontend UI:** High-fidelity, mobile-first design with responsive grids, subtle gradients, and micro-animations.
*   **AJAX-Powered:** Question submission, answer posting, upvoting, searching, and pagination all happen instantly without page reloads.
*   **Template Override System:** Automatically routes the `questions` Custom Post Type to beautifully designed, custom-built archive and single templates, overriding default theme layouts.
*   **Dynamic Badges:** Role-based visual badges (Admin, Moderator, Author, Member, Guest) integrated seamlessly next to user avatars.
*   **Voting System:** Upvote questions and answers dynamically.
*   **Best & Verified Answers:**
    *   **Accepted Answer:** Question authors can mark an answer as the "Best Answer" (highlights in green).
    *   **Admin Verified:** Administrators can verify authoritative answers, adding a premium purple "Verified" ribbon to the answer card.
*   **Shortcode Support:** Easily embed the question submission form (`[questionhub_submit_form]`) or question lists anywhere on your site.
*   **Premium Admin Dashboard:** A custom-built, React-style administrative interface featuring live statistics, quick action buttons, and an intuitive settings panel.
*   **Access Control:** Fine-grained settings to allow or restrict guest replies, require logins for asking/replying, and manage core feature toggles (voting, views, etc.).

## Installation

1.  Upload the `QuestionHub` folder to the `/wp-content/plugins/` directory.
2.  Activate the plugin through the 'Plugins' menu in WordPress.
3.  Navigate to **QuestionHub -> Settings** in the admin dashboard to configure your preferences.

## Usage

### Setting Up the "Ask a Question" Page
1. Create a new WordPress page (e.g., "Ask a Question").
2. Add the shortcode `[questionhub_submit_form]` to the page content.
3. Publish the page.
4. Go to **QuestionHub -> Settings -> General**.
5. Under "Page Setup", select the page you just created from the **Ask a Question Page** dropdown.

### Viewing Questions
The plugin automatically handles the `/questions/` archive URL and individual question URLs, applying its custom, modern templates automatically.

### Admin Verification
Administrators viewing a single question page will see a **Verify Answer** button on all replies. Clicking this instantly applies an "Admin Verified" status and a diagonal ribbon to the answer, signifying it as official or highly accurate.

## Architecture & Codebase
QuestionHub is built following modern WordPress development standards:
*   **Object-Oriented Design:** PSR-4 autoloading via Composer, utilizing Singletons and structured namespaces (e.g., `QuestionHub\Frontend\Inc\...`).
*   **Security First:** Strict adherence to WordPress sanitization (`sanitize_text_field`), escaping (`esc_html`, `esc_url`), and nonces for all form submissions and AJAX requests.
*   **Clean Assets:** CSS is modularized with custom variables (`--questionhub-primary`) for easy theming. JavaScript uses standard jQuery patterns bound to `$(document).on()` for robust event delegation on dynamically loaded AJAX elements.

## License
GPLv2 or later
