/**
 * QuestionHub Frontend JavaScript
 * Handles: question submission, answer submission, load more, sort/filter, vote, best answer.
 */
/* global jQuery, QuestionHubData */
(function ($) {
  'use strict';

  var qh = {
    ajaxUrl: QuestionHubData.ajax_url,
    nonce: QuestionHubData.nonce,
    i18n: QuestionHubData.i18n,

    // ============================
    // Question Submit Form
    // ============================
    initQuestionForm: function () {
      $(document).on('submit', '#questionhub-submit-form', function (e) {
        e.preventDefault();
        var $form   = $(this);
        var $btn    = $form.find('#questionhub-submit-btn');
        var $text   = $btn.find('.questionhub-btn-text');
        var $spin   = $btn.find('.questionhub-spinner');
        var $ok     = $('#questionhub-submit-success');
        var $err    = $('#questionhub-submit-error');

        $ok.hide(); $err.hide();
        $btn.prop('disabled', true);
        $text.hide(); $spin.show();

        $.ajax({
          url: qh.ajaxUrl,
          type: 'POST',
          data: {
            action: 'questionhub_submit_question',
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
      $(document).on('submit', '#questionhub-answer-form', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn  = $form.find('button[type="submit"]');
        var $spin = $btn.find('.questionhub-spinner');
        var $text = $btn.find('.questionhub-btn-text');
        var $ok   = $('#questionhub-answer-success');
        var $err  = $('#questionhub-answer-error');

        $ok.hide(); $err.hide();
        $btn.prop('disabled', true);
        $text.hide(); $spin.show();

        $.ajax({
          url: qh.ajaxUrl,
          type: 'POST',
          data: {
            action:   'questionhub_submit_answer',
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
    // Load More / Filter / Sort
    // ============================
    initQuestionList: function () {
      // Load More button.
      $(document).on('click', '.questionhub-load-more', function () {
        var $btn      = $(this);
        var $wrapper  = $btn.closest('.questionhub-list-wrapper');
        var $spinner  = $wrapper.find('.questionhub-spinner').first();
        var page      = parseInt($btn.data('page')) || 2;
        var maxPages  = parseInt($btn.data('max')) || 1;
        var category  = $wrapper.data('category') || '';
        var tag       = $wrapper.data('tag') || '';
        var orderby   = $wrapper.data('orderby') || 'date';

        $btn.prop('disabled', true).text(qh.i18n.loading);
        $spinner.show();

        $.ajax({
          url: qh.ajaxUrl,
          type: 'POST',
          data: {
            action:   'questionhub_load_questions',
            nonce:    qh.nonce,
            page:     page,
            category: category,
            tag:      tag,
            orderby:  orderby
          },
          success: function (res) {
            if (res.success && res.data.html) {
              $wrapper.find('.questionhub-questions-list').append(res.data.html);
              if (res.data.has_more) {
                $btn.data('page', page + 1).prop('disabled', false).text(qh.i18n.load_more);
              } else {
                $btn.closest('.questionhub-load-more-wrap').html('<p class="questionhub-no-results">' + qh.i18n.no_more + '</p>');
              }
            } else {
              $btn.closest('.questionhub-load-more-wrap').html('<p class="questionhub-no-results">' + qh.i18n.no_more + '</p>');
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

      // Sort change.
      $(document).on('change', '.questionhub-sort', function () {
        var $wrapper = $(this).closest('.questionhub-list-wrapper');
        $wrapper.data('orderby', $(this).val()).data('page', 1);
        qh.reloadList($wrapper);
      });

      // Category filter change.
      $(document).on('change', '.questionhub-filter-category', function () {
        var $wrapper = $(this).closest('.questionhub-list-wrapper');
        $wrapper.data('category', $(this).val()).data('page', 1);
        qh.reloadList($wrapper);
      });
    },

    reloadList: function ($wrapper) {
      var $list    = $wrapper.find('.questionhub-questions-list');
      var category = $wrapper.data('category') || '';
      var orderby  = $wrapper.data('orderby') || 'date';

      $list.css('opacity', 0.5);

      $.ajax({
        url: qh.ajaxUrl,
        type: 'POST',
        data: {
          action:   'questionhub_load_questions',
          nonce:    qh.nonce,
          page:     1,
          category: category,
          orderby:  orderby
        },
        success: function (res) {
          if (res.success) {
            $list.html(res.data.html || '');
            var $btn = $wrapper.find('.questionhub-load-more');
            if ($btn.length) {
              $btn.data('page', 2).prop('disabled', false).text(qh.i18n.load_more);
              if (!res.data.has_more) {
                $btn.closest('.questionhub-load-more-wrap').hide();
              } else {
                $btn.closest('.questionhub-load-more-wrap').show();
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

      $(document).on('input', '#questionhub-search-input', function () {
        var keyword  = $(this).val();
        var $results = $('#questionhub-search-results');
        var $list    = $('#questionhub-results-list');
        var $spin    = $('#questionhub-search-spinner');
        var category = $('.questionhub-search-category').val() || '';

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
              action:   'questionhub_search_questions',
              nonce:    qh.nonce,
              keyword:  keyword,
              category: category
            },
            success: function (res) {
              if (res.success) {
                $list.html(res.data.html || '<p class="questionhub-no-results">' + qh.i18n.no_more + '</p>');
              }
            },
            complete: function () {
              $spin.hide();
            }
          });
        }, 350);
      });

      $(document).on('submit', '#questionhub-search-form', function (e) {
        e.preventDefault();
        $('#questionhub-search-input').trigger('input');
      });
    },

    // ============================
    // Voting
    // ============================
    initVoting: function () {
      $(document).on('click', '.questionhub-vote-btn, .questionhub-vote-answer-btn', function () {
        if (!QuestionHubData.is_logged_in) {
          alert(qh.i18n.login_required);
          return;
        }

        var $btn  = $(this);
        var id    = $btn.data('id');
        var type  = $btn.data('type');

        $btn.prop('disabled', true);

        $.ajax({
          url: qh.ajaxUrl,
          type: 'POST',
          data: {
            action: 'questionhub_vote',
            nonce:  qh.nonce,
            id:     id,
            type:   type
          },
          success: function (res) {
            if (res.success) {
              $btn.find('.questionhub-vote-count').text(res.data.votes);
              $btn.addClass('voted');
            } else {
              alert(res.data.message);
            }
          },
          complete: function () {
            $btn.prop('disabled', false);
          }
        });
      });
    },

    // ============================
    // Best Answer
    // ============================
    initBestAnswer: function () {
      $(document).on('click', '.questionhub-mark-best-btn', function () {
        var $btn       = $(this);
        var commentId  = $btn.data('comment-id');
        var postId     = $btn.data('post-id');

        $btn.prop('disabled', true);

        $.ajax({
          url: qh.ajaxUrl,
          type: 'POST',
          data: {
            action:     'questionhub_best_answer',
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
    // Init All
    // ============================
    init: function () {
      qh.initQuestionForm();
      qh.initAnswerForm();
      qh.initQuestionList();
      qh.initSearch();
      qh.initVoting();
      qh.initBestAnswer();
    }
  };

  $(document).ready(function () {
    qh.init();
  });

})(jQuery);
