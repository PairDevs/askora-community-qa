# QuestionHub — Hooks & Filters

## Actions

### `questionhub_loaded`
Fires after QuestionHub is fully loaded.
```php
do_action( 'questionhub_loaded' );
```

### `questionhub_modules_loaded`
Fires after AdminManager and Frontend are both initialised.
```php
do_action( 'questionhub_modules_loaded' );
```

### `questionhub_before_question_form`
Fires before question submission processing.
```php
add_action( 'questionhub_before_question_form', function() {
    // e.g. spam check
});
```

### `questionhub_after_question_form`
Fires after a question is successfully created.
```php
add_action( 'questionhub_after_question_form', function() {
    // e.g. send notification
});
```

### `questionhub_before_answer_form`
Fires before an answer is inserted.

### `questionhub_after_answer_form`
Fires after an answer is successfully inserted.

### `questionhub_auth_before_register`
Fires before phone registration begins.

### `questionhub_auth_after_register`
Fires after a user is successfully registered.
**Parameters:** `$user_id` (int)
```php
add_action( 'questionhub_auth_after_register', function( $user_id ) {
    // e.g. assign role
});
```

### `questionhub_auth_before_login`
Fires before phone login begins.

### `questionhub_auth_after_login`
Fires after a user is successfully logged in.
**Parameters:** `$user_id` (int)

### `questionhub_sms_otp_requested`
Future Pro: fires when an OTP is requested.
**Parameters:** `$phone_number` (string)

### `questionhub_sms_otp_verified`
Future Pro: fires after OTP is verified.
**Parameters:** `$user_id` (int), `$phone_number` (string)

### `questionhub_pro_loaded`
Future Pro: fires when QuestionHub Pro is loaded.

---

## Filters

### `questionhub_question_status`
Filter the default question status.
```php
add_filter( 'questionhub_question_status', function( $status ) {
    return 'publish'; // auto-publish all questions
});
```

### `questionhub_questions_per_page`
Filter questions per page.
```php
add_filter( 'questionhub_questions_per_page', fn() => 20 );
```

### `questionhub_questions_query_args`
Filter the full WP_Query args for question lists.
**Parameters:** `$args` (array)

### `questionhub_enable_guest_replies`
Filter whether guest replies are allowed.
```php
add_filter( 'questionhub_enable_guest_replies', '__return_true' );
```

### `questionhub_badge_label`
Filter the badge label for a user.
**Parameters:** `$label` (string), `$user_id` (int), `$post_id` (int|null)
```php
add_filter( 'questionhub_badge_label', function( $label, $user_id, $post_id ) {
    // Return a custom label.
    return $label;
}, 10, 3 );
```

### `questionhub_is_pro_active`
Whether QuestionHub Pro is active. Pro plugin returns `true`.
```php
add_filter( 'questionhub_is_pro_active', '__return_true' );
```

### `questionhub_pro_features`
Array of enabled Pro feature keys.
**Parameters:** `$features` (array)

### `questionhub_enabled_modules`
Future: array of active module slugs.
**Parameters:** `$modules` (array)

### `questionhub_auth_fields`
Future: filter auth form fields.
**Parameters:** `$fields` (array)

### `questionhub_phone_required`
Whether phone is required for registration.
Default: `true`

### `questionhub_email_required`
Whether email is required for registration.
Default: `false`

### `questionhub_sms_provider`
Key of the active SMS provider.
Default: `'null'`
```php
add_filter( 'questionhub_sms_provider', fn() => 'twilio' );
```

### `questionhub_enable_otp_login`
Whether OTP login is enabled (Pro feature).
Default: `false`

### `questionhub_login_redirect`
URL to redirect to after login.
**Parameters:** `$url` (string), `$user_id` (int)

### `questionhub_register_redirect`
URL to redirect to after registration.
**Parameters:** `$url` (string), `$user_id` (int)
