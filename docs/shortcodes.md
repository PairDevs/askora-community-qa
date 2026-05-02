# QuestionHub — Shortcodes

## [questionhub_submit_form]
**Purpose:** Frontend question submission form.
**Auth:** Logged-in users only. Guests see the auth prompt template.
**Attributes:** None (future: `class`, `redirect`)
**Example:** `[questionhub_submit_form]`
**Output:** Form with title, details, category, tags, and submit button. Submits via AJAX.

---

## [questionhub_questions]
**Purpose:** Lists published questions with sort/filter controls.
**Attributes:**
- `category` — term ID to pre-filter by category (default: `""`)
- `tag` — tag slug to pre-filter (default: `""`)
- `orderby` — `date` | `comment_count` | `meta_value_num` (default: `date`)
- `per_page` — number of questions per page (default: from settings)

**Example:** `[questionhub_questions orderby="comment_count" per_page="5"]`
**Output:** Responsive question card grid with load-more and AJAX filter/sort.

---

## [questionhub_search]
**Purpose:** AJAX live search form.
**Attributes:** None
**Example:** `[questionhub_search]`
**Output:** Search input with category filter. Results appear instantly as you type.

---

## [questionhub_login]
**Purpose:** Phone number + password login form.
**Attributes:** None
**Example:** `[questionhub_login]`
**Output:** Login form. Shows "already logged in" message for authenticated users.

---

## [questionhub_register]
**Purpose:** Phone number registration form.
**Attributes:** None
**Example:** `[questionhub_register]`
**Output:** Registration form with name, phone, optional email, password fields.

---

## [questionhub_auth]
**Purpose:** Combined tabbed login + registration form.
**Attributes:** None
**Example:** `[questionhub_auth]`
**Output:** Tabbed card switching between Sign In and Create Account.

---

## [questionhub_popular_questions]
**Purpose:** Shows top-voted questions.
**Attributes:** None (inherits per_page from settings)
**Example:** `[questionhub_popular_questions]`
**Output:** Question list ordered by vote count descending.

---

## [questionhub_unanswered_questions]
**Purpose:** Shows questions with zero answers.
**Attributes:** None
**Example:** `[questionhub_unanswered_questions]`
**Output:** Question list filtered to `comment_count = 0`.

---

## [questionhub_dashboard]
**Purpose:** User dashboard showing their own questions.
**Auth:** Logged-in users only.
**Attributes:** None
**Example:** `[questionhub_dashboard]`
**Output:** User avatar, name, phone, and a list of their own questions.
