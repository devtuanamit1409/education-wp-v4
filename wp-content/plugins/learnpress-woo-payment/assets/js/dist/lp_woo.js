/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/src/js/add-item-to-cart.js":
/*!*******************************************!*\
  !*** ./assets/src/js/add-item-to-cart.js ***!
  \*******************************************/
/***/ (function() {

/**
 * Add course to cart
 *
 * @since 4.1.4
 * @version 1.0.0
 */
let isAddingToCart = [];

// Event click add item to cart
document.addEventListener('submit', function (e) {
  const target = e.target;
  if (target.name === 'form-add-item-to-cart') {
    addItemToCart(e, target);
  }
});

/**
 * Add item to cart
 *
 * @param e
 * @param form
 */
const addItemToCart = function (e, form) {
  e.preventDefault();

  /**
   * For theme Eduma
   * When user not login, click add-to-cart will show popup login
   * Set params submit course
   */
  const elThimLoginPopup = document.querySelector('.thim-login-popup .login');
  if (elThimLoginPopup && 'yes' !== lpWoo.woo_enable_signup_and_login_from_checkout && 'yes' !== lpWoo.woocommerce_enable_guest_checkout) {
    if (parseInt(lpData.user_id) === 0) {
      elThimLoginPopup.click();
      return;
    }
  }
  let elItemId = form.querySelector('input[name="item-id"]');
  if (!elItemId) {
    return;
  }
  const itemID = elItemId.value;
  const indexOf = isAddingToCart.indexOf(itemID);
  if (isAddingToCart.indexOf(itemID) > -1) {
    return;
  }
  isAddingToCart.push(itemID);
  const btnSubmit = form.querySelector('button[type="submit"]');
  let formData = new FormData(form);
  formData.append('action', `lpWooAddItemToCart`);
  formData.append('nonce', lpWoo.nonce);
  btnSubmit.innerText = lpWoo.adding_i18n;
  fetch(lpWoo.url_ajax, {
    method: 'POST',
    body: formData
  }).then(res => res.text()).then(res => {
    const dataObj = JSON.parse(res);
    const {
      data,
      status,
      message
    } = dataObj;
    if (status === 'error') {
      form.innerHTML = message;
      return;
    }
    if ('undefined' !== typeof data.redirect_to && data.redirect_to !== '') {
      btnSubmit.innerText = lpWoo.redirect_i18n;
      window.location = data.redirect_to;
    } else {
      // Find all form item id same set new HTML
      const elInput = document.querySelectorAll(`input[name="item-id"][value="${itemID}"]`);
      elInput.forEach(el => {
        el.closest('form[name=form-add-item-to-cart]').outerHTML = data.button_view_cart;
      });
      const el_mini_cart_count = document.querySelectorAll('.minicart_hover .items-number');
      if (el_mini_cart_count.length) {
        el_mini_cart_count.forEach(el => {
          el.innerHTML = data.count_items;
        });
      }

      // ThimElKit count items cart
      const el_thim_el_kit_cart_count = document.querySelectorAll('.thim-ekits-mini-cart .cart-items-number');
      if (el_thim_el_kit_cart_count.length) {
        el_thim_el_kit_cart_count.forEach(el => {
          el.innerHTML = data.count_items;
        });
      }

      // Update cart widget
      const el_widget_shopping_cart_contents = document.querySelectorAll('.widget_shopping_cart_content');
      if (el_widget_shopping_cart_contents.length) {
        el_widget_shopping_cart_contents.forEach(el => {
          el.innerHTML = data.widget_shopping_cart_content;
        });
      }
    }
  }).catch(err => console.log(err)).finally(() => {
    //isAddingToCart.splice( indexOf, 1 );
  });
};
const check_reload_browser = () => {
  window.addEventListener('pageshow', function (event) {
    const hasCache = event.persisted || typeof window.performance != 'undefined' && String(window.performance.getEntriesByType('navigation')[0].type) === 'back_forward';

    //console.log( hasCache );

    if (hasCache) {
      location.reload();
    }
  });
};

// Fix event browser back - load page to show 'view cart' button if added to cart
check_reload_browser();

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
!function() {
"use strict";
/*!*********************************!*\
  !*** ./assets/src/js/lp_woo.js ***!
  \*********************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _add_item_to_cart_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./add-item-to-cart.js */ "./assets/src/js/add-item-to-cart.js");
/* harmony import */ var _add_item_to_cart_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_add_item_to_cart_js__WEBPACK_IMPORTED_MODULE_0__);
/**
 * Js handle add to cart
 *
 * @version 1.0.2
 * @since 3.0.0
 */


//import {} from './add-package-to-cart.js'
}();
/******/ })()
;
//# sourceMappingURL=lp_woo.js.map