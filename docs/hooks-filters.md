# Askora Community Q&A — Hooks & Filters

## Actions

### `askora_loaded`
Fires after Askora Community Q&A is fully loaded.
```php
do_action( 'askora_loaded' );
```

### `askora_modules_loaded`
Fires after AdminManager and Frontend are both initialised.
```php
do_action( 'askora_modules_loaded' );
```

### `askora_before_question_form`
Fires before question submission processing.
```php
add_action( 'askora_before_question_form', function() {
    // e.g. spam check
});
```

### `askora_after_question_form`
Fires after a question is successfully created.
```php
add_action( 'askora_after_question_form', function() {
    // e.g. send notification
});
```

### `askora_before_answer_form`
Fires before an answer is inserted.

### `askora_after_answer_form`
Fires after an answer is successfully inserted.

### `askora_auth_before_register`
Fires before phone registration begins.

### `askora_auth_after_register`
Fires after a user is successfully registered.
**Parameters:** `$user_id` (int)
```php
add_action( 'askora_auth_after_register', function( $user_id ) {
    // e.g. assign role
});
```

### `askora_auth_before_login`
Fires before phone login begins.

### `askora_auth_after_login`
Fires after a user is successfully logged in.
**Parameters:** `$user_id` (int)

### `askora_sms_otp_requested`
Future Pro: fires when an OTP is requested.
**Parameters:** `$phone_number` (string)

### `askora_sms_otp_verified`
Future Pro: fires after OTP is verified.
**Parameters:** `$user_id` (int), `$phone_number` (string)

### `askora_pro_loaded`
Future Pro: fires when Askora Pro is loaded.

---

## Filters

### `askora_question_status`
Filter the default question status.
```php
add_filter( 'askora_question_status', function( $status ) {
    return 'publish'; // auto-publish all questions
});
```

### `askora_questions_per_page`
Filter questions per page.
```php
add_filter( 'askora_questions_per_page', fn() => 20 );
```

### `askora_questions_query_args`
Filter the full WP_Query args for question lists.
**Parameters:** `$args` (array)

### `askora_enable_guest_replies`
Filter whether guest replies are allowed.
```php
add_filter( 'askora_enable_guest_replies', '__return_true' );
```

### `askora_badge_label`
Filter the badge label for a user.
**Parameters:** `$label` (string), `$user_id` (int), `$post_id` (int|null)
```php
add_filter( 'askora_badge_label', function( $label, $user_id, $post_id ) {
    // Return a custom label.
    return $label;
}, 10, 3 );
```

### `askora_is_pro_active`
Whether Askora Pro is active. Pro plugin returns `true`.
```php
add_filter( 'askora_is_pro_active', '__return_true' );
```

### `askora_pro_features`
Array of enabled Pro feature keys.
**Parameters:** `$features` (array)

### `askora_enabled_modules`
Future: array of active module slugs.
**Parameters:** `$modules` (array)

### `askora_auth_fields`
Future: filter auth form fields.
**Parameters:** `$fields` (array)

### `askora_phone_required`
Whether phone is required for registration.
Default: `true`

### `askora_email_required`
Whether email is required for registration.
Default: `false`

### `askora_sms_provider`
Key of the active SMS provider.
Default: `'null'`
```php
add_filter( 'askora_sms_provider', fn() => 'twilio' );
```

### `askora_enable_otp_login`
Whether OTP login is enabled (Pro feature).
Default: `false`

### `askora_login_redirect`
URL to redirect to after login.
**Parameters:** `$url` (string), `$user_id` (int)

### `askora_register_redirect`
URL to redirect to after registration.
**Parameters:** `$url` (string), `$user_id` (int)
