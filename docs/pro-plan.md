# Askora Pro — Plan

## Overview

Askora Pro is the premium extension built on top of the free plugin.  
It extends the free version through WordPress filters, action hooks, and interfaces —
no core files are modified.

## Extension Pattern

Pro registers itself via:

```php
add_filter( 'askora_is_pro_active', '__return_true' );
add_filter( 'askora_pro_features', function( $features ) {
    return array_merge( $features, [
        'sms_otp_login',
        'reputation',
        'private_questions',
        'analytics',
    ]);
});
```

Feature gates in free code:

```php
if ( FeatureGate::can_use( 'sms_otp_login' ) ) {
    // Pro only block.
}
```

## Pro Features Planned

### Auth & Security
- SMS OTP login/register (no password)
- Multi-provider SMS gateway (Twilio, Vonage, MessageBird, custom)
- OTP hash storage, expiry, attempt limiting
- Country code picker
- 2FA for existing accounts

### Content
- Private questions
- Paid question unlocking (WooCommerce)
- Attachment uploads (images, PDFs)
- Code snippet formatting with syntax highlighting
- Question revision history

### Community
- User reputation points system
- Badges & achievements
- Expert answer system
- Question bookmarks
- Follow/watch a question

### Admin & Moderation
- Advanced moderation queue
- Spam protection (Akismet integration, rate limiting)
- Bulk actions (approve, reject, spam)
- Custom role-based permissions

### Analytics
- Dashboard: questions per day, top authors, trending topics
- Answer rate metrics
- User engagement reports

### Notifications
- Email notifications (new answer, best answer, vote)
- SMS notifications (new answer alert)
- In-browser push notifications

### Integrations
- Elementor widgets for all shortcodes
- Gutenberg blocks
- REST API endpoints
- BuddyPress/BuddyBoss integration
- Membership plugin integration (MemberPress, Restrict Content Pro)

### Developer
- License system (remote validation)
- White-label mode
- Priority support
