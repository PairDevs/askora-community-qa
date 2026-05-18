/**
 * Askora Community Q&A Auth JavaScript
 * Handles: phone login, phone register, tab switching.
 */
/* global jQuery, AskoraData */
(function ($) {
  'use strict';

  var qhAuth = {
    ajaxUrl: AskoraData.ajax_url,
    nonce:   AskoraData.nonce,

    // ============================
    // Auth Tabs
    // ============================
    initTabs: function () {
      $(document).on('click', '.askora-auth-tab', function () {
        var tab = $(this).data('tab');
        $('.askora-auth-tab').removeClass('active');
        $(this).addClass('active');
        $('.askora-auth-tab-content').hide();
        $('#askora-tab-' + tab).show();
      });
    },

    // ============================
    // Phone Login
    // ============================
    initLogin: function () {
      $(document).on('submit', '#askora-login-form', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn  = $form.find('button[type="submit"]');
        var $text = $btn.find('.askora-btn-text');
        var $spin = $btn.find('.askora-spinner');
        var $ok   = $('#askora-login-success');
        var $err  = $('#askora-login-error');

        $ok.hide(); $err.hide();
        $btn.prop('disabled', true);
        $text.hide(); $spin.show();

        $.ajax({
          url: qhAuth.ajaxUrl,
          type: 'POST',
          data: {
            action:   'askora_phone_login',
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
      $(document).on('submit', '#askora-register-form', function (e) {
        e.preventDefault();
        var $form    = $(this);
        var $btn     = $form.find('button[type="submit"]');
        var $text    = $btn.find('.askora-btn-text');
        var $spin    = $btn.find('.askora-spinner');
        var $ok      = $('#askora-register-success');
        var $err     = $('#askora-register-error');
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
            action:           'askora_phone_register',
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
