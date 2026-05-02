# QuestionHub — Architecture

## Overview

QuestionHub follows a **layered, PrimeKit-matched OOP architecture** with strict separation of concerns.

## Bootstrap Flow

```
questionhub.php
  └── final class QuestionHub (singleton)
        ├── define_constants()    → QUESTIONHUB_VERSION, PATH, URL, FILE, BASENAME
        ├── include_files()       → vendor/autoload.php (Composer PSR-4)
        └── init_hooks()
              ├── plugins_loaded → plugin_loaded() → new Manager()
              ├── init           → register_textdomain()
              ├── activation     → Activate::activate()
              └── deactivation   → Deactivate::deactivate()
```

## Composer PSR-4 Namespaces

| Namespace | Directory |
|---|---|
| `QuestionHub\` | `Inc/` |
| `QuestionHub\Admin\` | `Admin/` |
| `QuestionHub\Frontend\` | `Frontend/` |

## Manager Layer (`Inc/`)

```
Manager
  ├── new AdminManager()
  └── new Frontend()
```

## Admin Layer (`Admin/`)

```
AdminManager
  ├── set_constants()   → QUESTIONHUB_ADMIN_ASSETS
  └── init()
        ├── Dashboard/Menu/QuestionHub.php     → top-level admin menu
        ├── Dashboard/Settings/Settings.php    → settings + sub-menu
        ├── Assets/Assets.php                  → admin CSS/JS
        ├── Hooks/ActionHooks.php              → plugin row meta, action links
        ├── Hooks/FilterHooks.php              → admin filter hooks
        ├── PostTypes/QuestionPostType.php     → `questions` CPT
        ├── Taxonomies/QuestionCategory.php   → `question_category`
        ├── Taxonomies/QuestionTag.php        → `question_tag`
        ├── Columns/QuestionColumns.php       → admin list columns
        └── Notices/AdminNotices.php          → dismissible welcome notice
```

## Frontend Layer (`Frontend/`)

```
Frontend
  └── initialize()
        ├── Inc/Assets/Assets.php             → wp_enqueue_scripts
        ├── Inc/Shortcodes/Shortcodes.php     → all [questionhub_*] shortcodes
        ├── Inc/Ajax/AjaxManager.php          → all wp_ajax_* handlers
        ├── Inc/Auth/AuthForms.php            → auth form renderer
        ├── Inc/Questions/QuestionService.php → business logic
        ├── Inc/Questions/ViewCounter.php     → view tracking
        ├── Inc/Questions/VoteManager.php     → voting
        └── Inc/Comments/AnswerRenderer.php   → answers
```

## Security

- All AJAX: `check_ajax_referer()` + nonce
- All output: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`
- All input: `sanitize_text_field()`, `absint()`, `wp_unslash()`
- DB queries: `WP_Query`, `wp_insert_post()`, `wp_insert_comment()` — no raw SQL
- Passwords: only via `wp_create_user()`, `wp_check_password()`

## Template Override

Developers can override any template by placing a file in `{active-theme}/questionhub/`:

```
theme/
└── questionhub/
    ├── question-card.php
    ├── single-question.php
    └── answer-item.php
```

## Pro Extension Points

- `questionhub_is_pro_active` filter → return `true` from Pro plugin
- `questionhub_pro_features` filter → return array of enabled feature slugs
- `FeatureGate::can_use('feature_slug')` → gated code blocks
- `SmsProviderInterface` → implement for real SMS providers
- `AuthProviderInterface` → implement for alternative auth providers
