# Askora Community Q&A — Shortcodes

## [askora_submit_form]
**Purpose:** Frontend question submission form.
**Auth:** Logged-in users only. Guests see the auth prompt template.
**Attributes:** None (future: `class`, `redirect`)
**Example:** `[askora_submit_form]`
**Output:** Form with title, details, category, tags, and submit button. Submits via AJAX.

---

## [askora_questions]
**Purpose:** Lists published questions with sort/filter controls, an optional inline search bar, and an optional "Ask a Question" button.
**Attributes:**
- `category` — term ID to pre-filter by category (default: `""`)
- `tag` — tag slug to pre-filter (default: `""`)
- `orderby` — `date` | `comment_count` | `meta_value_num` (default: `date`)
- `per_page` — number of questions per page (default: from settings)
- `show_ask_btn` — `true` | `false` — show "Ask a Question" button in the filter bar (default: `true`). URL resolves from the **Ask a Question Page** setting, auto-detected page with `[askora_submit_form]`, or `home_url()`.
- `show_search` — `true` | `false` — show a live keyword search field above the filter bar (default: `true`). Typing debounces 350ms and reloads the list via AJAX.

**Examples:**
```
[askora_questions]
[askora_questions orderby="comment_count" per_page="5"]
[askora_questions show_ask_btn="false"]
[askora_questions show_search="false" show_ask_btn="false"]
```
**Output:** Responsive question card list with load-more, AJAX filter/sort, live search, and Ask button.

---

## [askora_search]
**Purpose:** AJAX live search form.
**Attributes:** None
**Example:** `[askora_search]`
**Output:** Search input with category filter. Results appear instantly as you type.

---

## [askora_login]
**Purpose:** Phone number + password login form.
**Attributes:** None
**Example:** `[askora_login]`
**Output:** Login form. Shows "already logged in" message for authenticated users.

---

## [askora_register]
**Purpose:** Phone number registration form.
**Attributes:** None
**Example:** `[askora_register]`
**Output:** Registration form with name, phone, optional email, password fields.

---

## [askora_auth]
**Purpose:** Combined tabbed login + registration form.
**Attributes:** None
**Example:** `[askora_auth]`
**Output:** Tabbed card switching between Sign In and Create Account.

---

## [askora_popular_questions]
**Purpose:** Shows top-voted questions.
**Attributes:** None (inherits per_page from settings)
**Example:** `[askora_popular_questions]`
**Output:** Question list ordered by vote count descending.

---

## [askora_unanswered_questions]
**Purpose:** Shows questions with zero answers.
**Attributes:** None
**Example:** `[askora_unanswered_questions]`
**Output:** Question list filtered to `comment_count = 0`.

---

## [askora_dashboard]
**Purpose:** User dashboard showing their own questions.
**Auth:** Logged-in users only.
**Attributes:** None
**Example:** `[askora_dashboard]`
**Output:** User avatar, name, phone, and a list of their own questions.
