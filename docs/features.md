# Askora Community Q&A — Features

## Free Version Features

### Core
- **Custom Post Type** — `questions` CPT with archive, REST, and full label set
- **Question Categories** — hierarchical taxonomy (`question_category`)
- **Question Tags** — flat taxonomy (`question_tag`)

### Frontend Shortcodes
- `[askora_submit_form]` — AJAX question submission (logged-in only)
- `[askora_questions]` — responsive question list with sort/filter
- `[askora_search]` — AJAX live search
- `[askora_login]` — phone number login form
- `[askora_register]` — phone number registration form
- `[askora_auth]` — tabbed login + registration
- `[askora_popular_questions]` — most upvoted questions
- `[askora_unanswered_questions]` — questions with zero answers
- `[askora_dashboard]` — user dashboard with their own questions

### Authentication
- Phone number registration without requiring an email
- Phone number + password login
- Brute-force protection (transient-based attempt limiting)
- Placeholder email auto-generated if email omitted
- Auto-login after registration (configurable)

### Questions
- AJAX question submission with nonce validation
- Configurable default status (pending / publish / draft)
- Question view counter with cookie-based deduplication
- Category and tag assignment on submission

### Answers
- WordPress comment system used as answers
- AJAX answer/reply submission
- Guest replies (configurable)

### Voting
- Upvote questions and answers
- Duplicate vote prevention via user meta
- AJAX voting with instant count update

### Best Answer
- Question author or admin can mark one reply as best answer
- Best answer highlighted with badge
- Stored in post meta + comment meta

### Badges
- Admin, Moderator, Author, Member, Guest role badges
- Filterable via `askora_badge_label` filter

### Admin
- Top-level Askora admin menu
- Settings page with General and Advanced tabs
- Custom admin columns: Views, Replies, Votes
- Dismissible activation notice
- Settings/Rate links in plugin row

### Developer
- 15+ action hooks
- 12+ filters
- PSR-4 Composer autoloading
- Translation-ready (`.pot` file)
- Template override system (theme/askora-community-qa/)
- SMS interface stubs for future Pro
- Feature gate for Pro extension
