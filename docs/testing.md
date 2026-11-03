# Askora Community Q&A — Testing

The test suite is PHPUnit on top of the standard WordPress test library. It lives in `tests/`, `bin/`
and `phpunit.xml.dist`, all of which are excluded from the WordPress.org package by `.distignore`
(the release workflow and the suite itself both verify this).

## One-time setup

```bash
composer install
bin/install-wp-tests.sh askora_tests <db-user> <db-pass> localhost latest
```

> **Use a dedicated, empty database.** The suite drops and recreates every table in it.
> Never point it at a real site's database.

Optionally keep the downloaded WordPress somewhere stable:

```bash
export WP_TESTS_DIR=$HOME/.wp-tests/wordpress-tests-lib
export WP_CORE_DIR=$HOME/.wp-tests/wordpress/
```

## Running

```bash
vendor/bin/phpunit                          # everything (≈ 3 seconds)
vendor/bin/phpunit --filter VoteManager     # one class
vendor/bin/phpunit --group ajax             # only the admin-ajax handlers
composer test                               # same as vendor/bin/phpunit
```

## What is covered

| Area | Tests |
| --- | --- |
| Questions, ordering, votes, permissions | `QuestionRepositoryTest`, `VoteManagerTest`, `PermissionTest` |
| Phone registration and login | `PhoneAuthTest`, `AuthAjaxTest` |
| Answers (moderation, flood, duplicates, HTML) | `SubmitAnswerAjaxTest` |
| Questions, voting, best answer, verify, lists, search | `SubmitQuestionAjaxTest`, `InteractionAjaxTest` |
| Rich editor, colors, settings, profile | `EditorTest`, `AssetsTest`, `SettingsTest`, `ProfileTest` |
| Shortcodes, activation, uninstall | `ShortcodesTest`, `ActivationTest`, `UninstallTest` |
| Release hygiene | `ReleaseHygieneTest` |

`ReleaseHygieneTest` fails the build when the plugin header, `ASKORA_VERSION`, `readme.txt` Stable tag
and `package.json` disagree, when `Tested up to` is not `major.minor`, when the current version has no
changelog entry, or when test/development files would be published to WordPress.org.

## Before a release

1. Run `vendor/bin/phpunit` — it must be green.
2. Bump the version everywhere (`askora-community-qa.php` header, `ASKORA_VERSION`, `readme.txt`
   Stable tag, `package.json`) and add the changelog + upgrade notice.
3. Run the suite again, then tag and publish the GitHub release.

Publishing a release runs the **Tests** workflow first and only then deploys to WordPress.org, so a
failing test blocks the deploy. The deploy job also re-checks the package contents.

## Conventions

* Tests never touch the network (`pre_http_request` is blocked in the base classes).
* AJAX tests extend `AskoraAjaxTestCase`, which emulates WordPress's request slashing so
  backslash handling is tested the way real requests behave.
