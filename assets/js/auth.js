/**
 * QuestionHub Auth JavaScript
 * Handles: phone login, phone register, tab switching.
 */
/* global jQuery, QuestionHubData */
(function ($) {
  'use strict';

  var qhAuth = {
    ajaxUrl: QuestionHubData.ajax_url,
    nonce:   QuestionHubData.nonce,

    // ============================
    // Auth Tabs
    // ============================
    initTabs: function () {
      $(document).on('click', '.questionhub-auth-tab', function () {
        var tab = $(this).data('tab');
        $('.questionhub-auth-tab').removeClass('active');
        $(this).addClass('active');
        $('.questionhub-auth-tab-content').hide();
        $('#questionhub-tab-' + tab).show();
      });
    },

    // ============================
    // Phone Login
    // ============================
    initLogin: function () {
      $(document).on('submit', '#questionhub-login-form', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn  = $form.find('button[type="submit"]');
        var $text = $btn.find('.questionhub-btn-text');
        var $spin = $btn.find('.questionhub-spinner');
        var $ok   = $('#questionhub-login-success');
        var $err  = $('#questionhub-login-error');

        $ok.hide(); $err.hide();
        $btn.prop('disabled', true);
        $text.hide(); $spin.show();

        $.ajax({
          url: qhAuth.ajaxUrl,
          type: 'POST',
          data: {
            action:   'questionhub_phone_login',
            nonce:    qhAuth.nonce,
            phone:    $form.find('[name="phone"]').val(),
            password: $form.find('[name="password"]').val()
          },
          success: function (res) {
            if (res.success) {
              $ok.text(res.data.message).show();
              setTimeout(function () {
                window.location.href = res.data.redirect || window.location.href;
              }, 1000);
            } else {
              $err.text(res.data.message).show();
              $btn.prop('disabled', false);
              $text.show(); $spin.hide();
            }
          },
          error: function () {
            $err.text('Something went wrong. Please try again.').show();
            $btn.prop('disabled', false);
            $text.show(); $spin.hide();
          }
        });
      });
    },

    // ============================
    // Phone Register
    // ============================
    initRegister: function () {
      $(document).on('submit', '#questionhub-register-form', function (e) {
        e.preventDefault();
        var $form    = $(this);
        var $btn     = $form.find('button[type="submit"]');
        var $text    = $btn.find('.questionhub-btn-text');
        var $spin    = $btn.find('.questionhub-spinner');
        var $ok      = $('#questionhub-register-success');
        var $err     = $('#questionhub-register-error');
        var password = $form.find('[name="password"]').val();
        var confirm  = $form.find('[name="confirm_password"]').val();

        $ok.hide(); $err.hide();

        if (password !== confirm) {
          $err.text('Passwords do not match.').show();
          return;
        }

        if (password.length < 6) {
          $err.text('Password must be at least 6 characters.').show();
          return;
        }

        $btn.prop('disabled', true);
        $text.hide(); $spin.show();

        $.ajax({
          url: qhAuth.ajaxUrl,
          type: 'POST',
          data: {
            action:           'questionhub_phone_register',
            nonce:            qhAuth.nonce,
            name:             $form.find('[name="name"]').val(),
            phone:            $form.find('[name="phone"]').val(),
            email:            $form.find('[name="email"]').val(),
            password:         password,
            confirm_password: confirm
          },
          success: function (res) {
            if (res.success) {
              $ok.text(res.data.message).show();
              setTimeout(function () {
                window.location.href = res.data.redirect || window.location.href;
              }, 1200);
            } else {
              $err.text(res.data.message).show();
              $btn.prop('disabled', false);
              $text.show(); $spin.hide();
            }
          },
          error: function () {
            $err.text('Something went wrong. Please try again.').show();
            $btn.prop('disabled', false);
            $text.show(); $spin.hide();
          }
        });
      });
    },

    init: function () {
      qhAuth.initTabs();
      qhAuth.initLogin();
      qhAuth.initRegister();
    }
  };

  $(document).ready(function () {
    qhAuth.init();
  });

})(jQuery);
