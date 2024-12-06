/******/ (() => { // webpackBootstrap
/*!***********************************!*\
  !*** ./assets/src/js/wishlist.js ***!
  \***********************************/
(function ($) {
  'use strict';

  function parseJSON(data) {
    if (typeof data !== 'string') {
      return data;
    }
    const m = String.raw({
      raw: data
    }).match(/<-- LP_AJAX_START -->(.*)<-- LP_AJAX_END -->/s);
    try {
      if (m) {
        data = JSON.parse(m[1].replace(/(?:\r\n|\r|\n)/g, ''));
      } else {
        data = JSON.parse(data);
      }
    } catch (e) {
      data = {};
    }
    return data;
  }
  let timer = null,
    submit = function () {
      const $button = $(this),
        course_id = $button.attr('data-id'),
        nonce = $button.attr('data-nonce'),
        text = $button.data('text');
      if ($button.hasClass('ajaxload_wishlist')) {
        return;
      }
      $button.addClass('ajaxload_wishlist').prop('disabled', true);
      if (text) {
        $button.html(text);
      }
      $.ajax({
        url: window.location.href,
        type: 'post',
        dataType: 'html',
        data: {
          //action   : 'learn_press_toggle_course_wishlist',
          'lp-ajax': 'toggle_course_wishlist',
          course_id,
          nonce
        },
        success(response) {
          var response = parseJSON(response);
          const $b = $('.learn-press-course-wishlist-button-' + response.course_id),
            $p = $b.closest('[data-context="tab-wishlist"]');
          if ($p.length) {
            $p.fadeOut(function () {
              const $siblings = $p.siblings(),
                $parent = $p.closest('#learn-press-profile-tab-course-wishlist');
              $p.remove();
              if ($siblings.length == 0) {
                $parent.removeClass('has-courses');
              }
            });
          } else {
            $b.removeClass('ajaxload_wishlist').toggleClass('on', response.state == 'on').prop('title', response.title).html(response.button_text);
          }
          $b.prop('disabled', false);
        }
      });
    };
  $(document).on('click', '.course-wishlist', function () {
    timer && clearTimeout(timer);
    timer = setTimeout($.proxy(submit, this), 50);
  });
})(jQuery);

//*********************** code new
const wishlistHandle = new Set();
const toggleWishlist = async elWishlist => {
  const itemId = elWishlist.dataset.itemId;
  if (!itemId || wishlistHandle.has(itemId)) {
    return;
  }
  wishlistHandle.add(itemId);
  elWishlist.style.opacity = '0.5';
  try {
    const response = await fetch(`${lpData.lp_rest_url}learnpress/v1/wishlist/add_or_remove`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': lpData.nonce
      },
      body: JSON.stringify({
        id: itemId
      })
    });
    const {
      status,
      message,
      data
    } = await response.json();
    if (status !== 'success') {
      throw new Error(message);
    }
    elWishlist.classList.toggle('active');
  } catch (err) {
    alert(err.message);
  } finally {
    wishlistHandle.delete(itemId);
    elWishlist.style.opacity = '1';
  }
};
document.addEventListener('click', e => {
  if (e.target.classList.contains('lp-item-wishlist')) {
    toggleWishlist(e.target);
  }
});
/******/ })()
;
//# sourceMappingURL=wishlist.js.map