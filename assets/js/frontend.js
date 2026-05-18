/**
 * Askora Community Q&A Frontend JavaScript
 * Handles: question submission, answer submission, load more, sort/filter, vote, best answer.
 */
/* global jQuery, AskoraData */
(function ($) {
  'use strict';

  var qh = {
    ajaxUrl: AskoraData.ajax_url,
    nonce: AskoraData.nonce,
    i18n: AskoraData.i18n,

    // ============================
    // Question Submit Form
    // ============================
    initQuestionForm: function () {
      $(document).on('submit', '#askora-submit-form', function (e) {
        e.preventDefault();
        var $form   = $(this);
        var $btn    = $form.find('#askora-submit-btn');
        var $text   = $btn.find('.askora-btn-text');
        var $spin   = $btn.find('.askora-spinner');
        var $ok     = $('#askora-submit-success');
        var $err    = $('#askora-submit-error');

        $ok.hide(); $err.hide();
        $btn.prop('disabled', true);
        $text.hide(); $spin.show();

        $.ajax({
          url: qh.ajaxUrl,
          type: 'POST',
          data: {
            action: 'askora_submit_question',
            nonce:  qh.nonce,
            title:  $form.find('[name="title"]').val(),
            content: $form.find('[name="content"]').val(),
            'categories[]': $form.find('[name="categories[]"]').val(),
            tags:   $form.find('[name="tags"]').val()
          },
          success: function (res) {
            if (res.success) {
              $ok.text(res.data.message).fadeIn();
              $form[0].reset();
            } else {
              $err.text(res.data.message).fadeIn();
            }
          },
          error: function () {
            $err.text(qh.i18n.error_generic).fadeIn();
          },
          complete: function () {
            $btn.prop('disabled', false);
            $text.show(); $spin.hide();
          }
        });
      });
    },

    // ============================
    // Answer Submit Form
    // ============================
    initAnswerForm: function () {
      $(document).on('submit', '#askora-answer-form', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn  = $form.find('button[type="submit"]');
        // Support both old .askora-spinner and new .qh-btn-spinner
        var $spin = $btn.find('.askora-spinner, .qh-btn-spinner');
        var $text = $btn.find('.askora-btn-text, .qh-btn-text');
        // Support both old and new alert IDs
        var $ok   = $('#askora-answer-success');
        var $err  = $('#askora-answer-error');

        $ok.hide(); $err.hide();
        $btn.prop('disabled', true);
        $text.hide(); $spin.show();

        $.ajax({
          url: qh.ajaxUrl,
          type: 'POST',
          data: {
            action:   'askora_submit_answer',
            nonce:    qh.nonce,
            post_id:  $form.find('[name="post_id"]').val(),
            content:  $form.find('[name="content"]').val()
          },
          success: function (res) {
            if (res.success) {
              $ok.text(res.data.message).fadeIn();
              $form.find('textarea').val('');
              // Reload page after short delay to show new answer.
              setTimeout(function () { location.reload(); }, 1500);
            } else {
              $err.text(res.data.message).fadeIn();
              $btn.prop('disabled', false);
              $text.show(); $spin.hide();
            }
          },
          error: function () {
            $err.text(qh.i18n.error_generic).fadeIn();
            $btn.prop('disabled', false);
            $text.show(); $spin.hide();
          },
          complete: function () {
            // only re-enable if not already handled above (success reloads)
          }
        });
      });
    },

    // ============================
    // Load More / Filter / Sort
    // ============================
    initQuestionList: function () {
      // Load More button.
      $(document).on('click', '.askora-load-more', function () {
        var $btn      = $(this);
        var $wrapper   = $btn.closest('.askora-list-wrapper');
        var $spinner   = $wrapper.find('.askora-spinner').first();
        var page       = parseInt($btn.data('page')) || 2;
        var maxPages   = parseInt($btn.data('max')) || 1;
        var category   = $wrapper.data('category') || '';
        var tag        = $wrapper.data('tag') || '';
        var orderby    = $wrapper.data('orderby') || 'date';
        var keyword    = $wrapper.data('keyword') || '';
        var unanswered = parseInt($wrapper.data('unanswered'), 10) || 0;

        $btn.prop('disabled', true).text(qh.i18n.loading);
        $spinner.show();

        $.ajax({
          url: qh.ajaxUrl,
          type: 'POST',
          data: {
            action:     'askora_load_questions',
            nonce:      qh.nonce,
            page:       page,
            category:   category,
            tag:        tag,
            orderby:    orderby,
            keyword:    keyword,
            unanswered: unanswered
          },
          success: function (res) {
            if (res.success && res.data.html) {
              $wrapper.find('.askora-questions-list').append(res.data.html);
              if (res.data.has_more) {
                $btn.data('page', page + 1).prop('disabled', false).text(qh.i18n.load_more);
              } else {
                $btn.closest('.askora-load-more-wrap').html('<p class="askora-no-results">' + qh.i18n.no_more + '</p>');
              }
            } else {
              $btn.closest('.askora-load-more-wrap').html('<p class="askora-no-results">' + qh.i18n.no_more + '</p>');
            }
          },
          error: function () {
            $btn.prop('disabled', false).text(qh.i18n.load_more);
          },
          complete: function () {
            $spinner.hide();
          }
        });
      });

      // Sort change. The "unanswered" option is a filter rather than a sort,
      // so route it to the unanswered flag and keep orderby on the default.
      $(document).on('change', '.askora-sort', function () {
        var $wrapper = $(this).closest('.askora-list-wrapper');
        var val = $(this).val();
        if (val === 'unanswered') {
          $wrapper.data('orderby', 'date').data('unanswered', 1).data('page', 1);
        } else {
          $wrapper.data('orderby', val).data('unanswered', 0).data('page', 1);
        }
        qh.reloadList($wrapper);
      });

      // Category filter change.
      $(document).on('change', '.askora-filter-category', function () {
        var $wrapper = $(this).closest('.askora-list-wrapper');
        $wrapper.data('category', $(this).val()).data('page', 1);
        qh.reloadList($wrapper);
      });

      // Inline search in list wrapper (show_search="true").
      var listSearchTimeout;
      $(document).on('input', '.askora-list-search .askora-search-input', function () {
        var $input   = $(this);
        var $wrapper = $input.closest('.askora-list-wrapper');
        clearTimeout(listSearchTimeout);
        listSearchTimeout = setTimeout(function () {
          $wrapper.data('keyword', $input.val()).data('page', 1);
          qh.reloadList($wrapper);
        }, 350);
      });
    },

    reloadList: function ($wrapper) {
      var $list      = $wrapper.find('.askora-questions-list');
      var category   = $wrapper.data('category') || '';
      var orderby    = $wrapper.data('orderby') || 'date';
      var keyword    = $wrapper.data('keyword') || '';
      var unanswered = parseInt($wrapper.data('unanswered'), 10) || 0;

      $list.css('opacity', 0.5);

      $.ajax({
        url: qh.ajaxUrl,
        type: 'POST',
        data: {
          action:     'askora_load_questions',
          nonce:      qh.nonce,
          page:       1,
          category:   category,
          orderby:    orderby,
          keyword:    keyword,
          unanswered: unanswered
        },
        success: function (res) {
          if (res.success) {
            $list.html(res.data.html || '');
            var $btn = $wrapper.find('.askora-load-more');
            if ($btn.length) {
              $btn.data('page', 2).prop('disabled', false).text(qh.i18n.load_more);
              if (!res.data.has_more) {
                $btn.closest('.askora-load-more-wrap').hide();
              } else {
                $btn.closest('.askora-load-more-wrap').show();
              }
            }
          }
        },
        complete: function () {
          $list.css('opacity', 1);
        }
      });
    },

    // ============================
    // AJAX Search
    // ============================
    initSearch: function () {
      var searchTimeout;

      $(document).on('input', '#askora-search-input', function () {
        var keyword  = $(this).val();
        var $results = $('#askora-search-results');
        var $list    = $('#askora-results-list');
        var $spin    = $('#askora-search-spinner');
        var category = $('.askora-search-category').val() || '';

        clearTimeout(searchTimeout);

        if (keyword.length < 2) {
          $results.hide();
          return;
        }

        $results.show();
        $spin.show();

        searchTimeout = setTimeout(function () {
          $.ajax({
            url: qh.ajaxUrl,
            type: 'POST',
            data: {
              action:   'askora_search_questions',
              nonce:    qh.nonce,
              keyword:  keyword,
              category: category
            },
            success: function (res) {
              if (res.success) {
                $list.html(res.data.html || '<p class="askora-no-results">' + qh.i18n.no_more + '</p>');
              }
            },
            complete: function () {
              $spin.hide();
            }
          });
        }, 350);
      });

      $(document).on('submit', '#askora-search-form', function (e) {
        e.preventDefault();
        $('#askora-search-input').trigger('input');
      });
    },

    // ============================
    // Voting
    // ============================
    initVoting: function () {
      // Handles old shortcode buttons AND new single-page qh-vote-upbtn buttons.
      $(document).on(
        'click',
        '.askora-vote-btn, .askora-vote-answer-btn, .qh-vote-upbtn, .qh-vote-answer-btn',
        function () {
          if (!AskoraData.is_logged_in) {
            alert(qh.i18n.login_required);
            return;
          }

          var $btn  = $(this);
          var id    = $btn.data('id');
          var type  = $btn.data('type');

          if (!id) return; // safety

          $btn.prop('disabled', true);

          $.ajax({
            url: qh.ajaxUrl,
            type: 'POST',
            data: {
              action: 'askora_vote',
              nonce:  qh.nonce,
              id:     id,
              type:   type
            },
            success: function (res) {
              if (res.success) {
                // Count span may be a CHILD (old shortcode) or SIBLING (new single-page).
                // Try children first, then siblings, then the dedicated ID span.
                var $count = $btn.find('.askora-vote-count, .qh-vote-count, .qh-vote-num');
                if (!$count.length) {
                  $count = $btn.siblings('.qh-vote-count, .qh-vote-num, .askora-vote-count');
                }
                $count.text(res.data.votes);
                // On single-question page the question vote count lives in its own span.
                if (type === 'question') {
                  $('#qh-question-vote-count').text(res.data.votes);
                }
                $btn.addClass('voted');
              } else {
                alert(res.data.message);
              }
            },
            complete: function () {
              $btn.prop('disabled', false);
            }
          });
        }
      );
    },

    // ============================
    // Best Answer
    // ============================
    initBestAnswer: function () {
      $(document).on('click', '.askora-mark-best-btn', function () {
        var $btn       = $(this);
        var commentId  = $btn.data('comment-id');
        var postId     = $btn.data('post-id');

        $btn.prop('disabled', true);

        $.ajax({
          url: qh.ajaxUrl,
          type: 'POST',
          data: {
            action:     'askora_best_answer',
            nonce:      qh.nonce,
            comment_id: commentId,
            post_id:    postId
          },
          success: function (res) {
            if (res.success) {
              location.reload();
            } else {
              alert(res.data.message);
              $btn.prop('disabled', false);
            }
          }
        });
      });
    },

    // ============================
    // Verify Answer (admin only)
    // ============================
    initVerifyAnswer: function () {
      $(document).on('click', '.qh-verify-answer-btn', function () {
        var $btn       = $(this);
        var commentId  = $btn.data('comment-id');
        var $item      = $btn.closest('.qh-answer-item, .askora-answer-item');

        $btn.prop('disabled', true);

        $.ajax({
          url: qh.ajaxUrl,
          type: 'POST',
          data: {
            action:     'askora_verify_answer',
            nonce:      qh.nonce,
            comment_id: commentId
          },
          success: function (res) {
            if (!res.success) {
              alert(res.data.message);
              return;
            }

            var isNowVerified = res.data.verified;

            if (isNowVerified) {
              // Add ribbon
              if (!$item.find('.qh-verified-ribbon').length) {
                $item.prepend(
                  '<div class="qh-verified-ribbon">'
                  + '<span class="dashicons dashicons-yes"></span>'
                  + 'Verified'
                  + '</div>'
                );
              }
              $item.addClass('qh-answer-verified');
              $btn.addClass('qh-verify-active');
              $btn.find('.dashicons').removeClass('dashicons-awards').addClass('dashicons-dismiss');
              $btn.find('.qh-verify-label').text('Remove Verification');
              $btn.attr('title', 'Remove verification');
            } else {
              // Remove ribbon
              $item.find('.qh-verified-ribbon').remove();
              $item.removeClass('qh-answer-verified');
              $btn.removeClass('qh-verify-active');
              $btn.find('.dashicons').removeClass('dashicons-dismiss').addClass('dashicons-awards');
              $btn.find('.qh-verify-label').text('Verify Answer');
              $btn.attr('title', 'Mark as Admin Verified');
            }
          },
          error: function () {
            alert('Something went wrong.');
          },
          complete: function () {
            $btn.prop('disabled', false);
          }
        });
      });
    },

    // ============================
    // Init All
    // ============================
    init: function () {
      qh.initQuestionForm();
      qh.initAnswerForm();
      qh.initQuestionList();
      qh.initSearch();
      qh.initVoting();
      qh.initBestAnswer();
      qh.initVerifyAnswer();
    }
  };

  $(document).ready(function () {
    qh.init();
  });

})(jQuery);
