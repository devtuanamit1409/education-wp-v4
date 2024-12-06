/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/src/js/frontend/compare-product/compare-list.js":
/*!****************************************************************!*\
  !*** ./assets/src/js/frontend/compare-product/compare-list.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _mixins_cookie__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../mixins/cookie */ "./assets/src/js/mixins/cookie.js");
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/url */ "@wordpress/url");
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _toggle_compare__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./toggle-compare */ "./assets/src/js/frontend/compare-product/toggle-compare.js");
/* harmony import */ var _mixins_snackbar__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../mixins/snackbar */ "./assets/src/js/mixins/snackbar.js");
/* harmony import */ var _wishlist_toggle_wishlist__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../wishlist/toggle-wishlist */ "./assets/src/js/frontend/wishlist/toggle-wishlist.js");
/* harmony import */ var _mixins_search__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../mixins/search */ "./assets/src/js/frontend/mixins/search.js");






const compareBoxNode = document.querySelector('.wcbt-compare-box');
const {
  __
} = wp.i18n;
let restUrl, restNamespace;
const wcbtCompareList = () => {
  if (!compareBoxNode) {
    return;
  }
  restUrl = WCBT_GLOBAL_OBJECT.rest_url || '';
  restNamespace = WCBT_GLOBAL_OBJECT.rest_namespace || '';
  fetchProducts();
  removeProduct();
};
const fetchProducts = () => {
  compareBoxNode.style.opacity = 0.4;
  const productIds = (0,_toggle_compare__WEBPACK_IMPORTED_MODULE_2__.getCompareCookie)();
  const param = {
    post_in: productIds
  };
  wp.apiFetch({
    path: (0,_wordpress_url__WEBPACK_IMPORTED_MODULE_1__.addQueryArgs)('/' + restNamespace + '/compare', param),
    method: 'GET'
  }).then(res => {
    if (res.data.content) {
      compareBoxNode.innerHTML = res.data.content;
    }
  }).catch(err => {
    console.log(err);
  }).finally(() => {
    compareBoxNode.style.opacity = 1;
  });
};
const removeProduct = () => {
  document.addEventListener('click', function (event) {
    const target = event.target;
    const removeNode = target.closest('.remove');
    if (removeNode && compareBoxNode.contains(target)) {
      let productId = removeNode.getAttribute('data-product-id');
      if (productId) {
        productId = parseInt(productId);
      }
      let productIds = (0,_toggle_compare__WEBPACK_IMPORTED_MODULE_2__.getCompareCookie)();
      if (productIds.includes(productId)) {
        productIds = productIds.filter(item => item !== productId);
        (0,_mixins_cookie__WEBPACK_IMPORTED_MODULE_0__.setCookie)('wcbt_compare_product', productIds.join(','), 30);
        fetchProducts();
        _mixins_snackbar__WEBPACK_IMPORTED_MODULE_3__.messagePopup.innerHTML = __('Product Compare removed', 'wcbt');
        (0,_mixins_snackbar__WEBPACK_IMPORTED_MODULE_3__.displayMessagePopup)();
      }
    }
  });
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (wcbtCompareList);

/***/ }),

/***/ "./assets/src/js/frontend/compare-product/toggle-compare.js":
/*!******************************************************************!*\
  !*** ./assets/src/js/frontend/compare-product/toggle-compare.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__),
/* harmony export */   getCompareCookie: () => (/* binding */ getCompareCookie)
/* harmony export */ });
/* harmony import */ var _mixins_cookie__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../mixins/cookie */ "./assets/src/js/mixins/cookie.js");
/* harmony import */ var _mixins_snackbar__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../mixins/snackbar */ "./assets/src/js/mixins/snackbar.js");


const {
  __
} = wp.i18n;
const addCompareTooltipText = WCBT_COMPARE_OBJECT.add_compare_tooltip_text || '';
const removeCompareTooltipText = WCBT_COMPARE_OBJECT.remove_compare_tooltip_text || '';
const userId = WCBT_GLOBAL_OBJECT.user_id || '';
const wcbtToggleCompareProduct = () => {
  document.addEventListener('click', function (event) {
    const compareBtnNodes = document.querySelectorAll('.wcbt-product-compare');
    for (let i = 0; i < compareBtnNodes.length; i++) {
      const compareBtnNode = compareBtnNodes[i];
      if (compareBtnNode.contains(event.target)) {
        event.preventDefault();
        handleToggleCompareProduct(compareBtnNode);
      }
    }
  });
  displayActiveCompare();
  displayCompareCount();
};

//Fix for cache page
const displayActiveCompare = () => {
  if (userId) {
    return;
  }
  const compare = getCompareCookie();
  if (!compare) {
    return;
  }
  const compareBtnNodes = document.querySelectorAll('.wcbt-product-compare');
  [...compareBtnNodes].map(compareBtn => {
    const productId = parseInt(compareBtn.getAttribute('data-product-id'));
    if (compare.includes(productId)) {
      compareBtn.classList.add('active');
    } else {
      compareBtn.classList.remove('active');
    }
  });
};

//Fix for cache page
const displayCompareCount = () => {
  if (userId) {
    return;
  }
  const compare = getCompareCookie();
  let count = 0;
  if (compare) {
    count = compare.length;
  }
  const countNodes = document.querySelectorAll('.wcbt-show-compare .count');
  [...countNodes].map(countNode => {
    countNode.innerHTML = count;
  });
};
const handleToggleCompareProduct = (compareBtnNode = null, productId = null) => {
  let productIds = getCompareCookie();
  if (productIds === null || !productIds) {
    productIds = [];
  }
  if (compareBtnNode !== null) {
    productId = compareBtnNode.getAttribute('data-product-id');
    productId = parseInt(productId);
  }
  let added = true;
  if (productIds) {
    //Remove compare product
    if (productIds.includes(productId)) {
      added = false;
      productIds = productIds.filter(item => item !== productId);
    } else {
      productIds.push(productId);
    }
  } else {
    productIds = [productId];
  }
  if (compareBtnNode !== null) {
    if (added === true) {
      compareBtnNode.classList.add('active');
      _mixins_snackbar__WEBPACK_IMPORTED_MODULE_1__.messagePopup.innerHTML = __('Product Compare added', 'wcbt');
    } else {
      compareBtnNode.classList.remove('active');
      _mixins_snackbar__WEBPACK_IMPORTED_MODULE_1__.messagePopup.innerHTML = __('Product Compare removed', 'wcbt');
    }
    compareTooltip(compareBtnNode);
  }
  (0,_mixins_cookie__WEBPACK_IMPORTED_MODULE_0__.setCookie)('wcbt_compare_product', productIds.join(','), 30);
  (0,_mixins_snackbar__WEBPACK_IMPORTED_MODULE_1__.displayMessagePopup)();
  updateTotal(productIds.length);
};
const getCompareCookie = () => {
  const productIds = (0,_mixins_cookie__WEBPACK_IMPORTED_MODULE_0__.getCookie)('wcbt_compare_product');
  if (productIds === null || !productIds) {
    return [];
  }
  return productIds.split(',').map(Number);
};
const updateTotal = total => {
  const compareCountNodes = document.querySelectorAll('.wcbt-show-compare .count');
  [...compareCountNodes].map(compareCountNode => {
    compareCountNode.innerHTML = total;
  });
};
const compareTooltip = productComparetNode => {
  const tooltipNode = productComparetNode.querySelector('span.tooltip');
  if (!tooltipNode) {
    return;
  }
  if (productComparetNode.classList.contains('active')) {
    tooltipNode.innerHTML = removeCompareTooltipText;
  } else {
    tooltipNode.innerHTML = addCompareTooltipText;
  }
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (wcbtToggleCompareProduct);

/***/ }),

/***/ "./assets/src/js/frontend/mixins/search.js":
/*!*************************************************!*\
  !*** ./assets/src/js/frontend/mixins/search.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getParamOfUrl: () => (/* binding */ getParamOfUrl),
/* harmony export */   getParamsOfUrl: () => (/* binding */ getParamsOfUrl),
/* harmony export */   getUrl: () => (/* binding */ getUrl)
/* harmony export */ });
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/url */ "@wordpress/url");
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_0__);

const getParamOfUrl = param => {
  const urlParams = new URLSearchParams(location.search);
  return urlParams.get(param);
};
const getParamsOfUrl = () => {
  const urlParams = new URLSearchParams(location.search);
  const queryParams = {};
  for (const [key, value] of urlParams) {
    queryParams[key] = value;
  }
  return queryParams;
};
const getUrl = args => {
  return (0,_wordpress_url__WEBPACK_IMPORTED_MODULE_0__.addQueryArgs)(document.location.origin + document.location.pathname, args);
};


/***/ }),

/***/ "./assets/src/js/frontend/wishlist/toggle-wishlist.js":
/*!************************************************************!*\
  !*** ./assets/src/js/frontend/wishlist/toggle-wishlist.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__),
/* harmony export */   getWishListCookie: () => (/* binding */ getWishListCookie),
/* harmony export */   updateTotal: () => (/* binding */ updateTotal)
/* harmony export */ });
/* harmony import */ var _mixins_cookie__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../mixins/cookie */ "./assets/src/js/mixins/cookie.js");
/* harmony import */ var _mixins_snackbar__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../mixins/snackbar */ "./assets/src/js/mixins/snackbar.js");


const restNamespace = WCBT_GLOBAL_OBJECT.rest_namespace || '';
const userId = WCBT_GLOBAL_OBJECT.user_id || '';
const {
  __
} = wp.i18n;
const addWishListTooltipText = WCBT_WISHLIST_OBJECT.add_wishlist_tooltip_text || '';
const removeWishListTooltipText = WCBT_WISHLIST_OBJECT.remove_wishlist_tooltip_text || '';
const wcbtToggleWishList = () => {
  const wishlistNode = document.querySelector('#wcbt-wishlist');
  document.addEventListener('click', function (event) {
    //If is wishlist page, return
    if (wishlistNode && wishlistNode.contains(event.target)) {
      return;
    }
    const wishListNodes = document.querySelectorAll('.wcbt-product-wishlist');
    for (let i = 0; i < wishListNodes.length; i++) {
      const wishListNode = wishListNodes[i];
      if (wishListNode.contains(event.target)) {
        event.preventDefault();
        let productId = wishListNode.getAttribute('data-product-id');
        productId = parseInt(productId);
        wishListNode.disabled = true;
        if (userId) {
          const data = {
            product_id: productId
          };
          wp.apiFetch({
            path: '/' + restNamespace + '/wishlist',
            method: 'POST',
            data
          }).then(res => {
            if (res.data.status === 'added') {
              wishListNode.classList.add('active');
              _mixins_snackbar__WEBPACK_IMPORTED_MODULE_1__.messagePopup.innerHTML = __('Product WishList added', 'wcbt');
            } else {
              wishListNode.classList.remove('active');
              _mixins_snackbar__WEBPACK_IMPORTED_MODULE_1__.messagePopup.innerHTML = __('Product WishList removed', 'wcbt');
            }
            (0,_mixins_snackbar__WEBPACK_IMPORTED_MODULE_1__.displayMessagePopup)();
            updateTotal(res.data.total);
          }).catch(err => {
            console.log(err);
          }).finally(() => {
            wishListNode.disabled = false;
            wishListTooltip(wishListNode);
          });
        } else {
          let productIds = getWishListCookie();
          if (!productIds) {
            productIds = [];
          }
          //Remove wishlist product
          if (productIds.includes(productId)) {
            productIds = productIds.filter(item => item !== productId);
            _mixins_snackbar__WEBPACK_IMPORTED_MODULE_1__.messagePopup.innerHTML = __('Product WishList removed', 'wcbt');
            wishListNode.classList.remove('active');
          } else {
            productIds.push(productId);
            _mixins_snackbar__WEBPACK_IMPORTED_MODULE_1__.messagePopup.innerHTML = __('Product WishList added', 'wcbt');
            wishListNode.classList.add('active');
          }
          (0,_mixins_cookie__WEBPACK_IMPORTED_MODULE_0__.setCookie)('wcbt_wishlist_product', productIds.join(','), 30);
          (0,_mixins_snackbar__WEBPACK_IMPORTED_MODULE_1__.displayMessagePopup)();
          updateTotal(productIds.length);
          wishListNode.disabled = false;
          wishListTooltip(wishListNode);
        }
      }
    }
  });
  displayActiveWishList();
  displayWishListCount();
};

//Fix for cache page
const displayActiveWishList = () => {
  if (userId) {
    return;
  }
  const wishList = getWishListCookie();
  if (!wishList) {
    return;
  }
  const wishListBtnNodes = document.querySelectorAll('.wcbt-product-wishlist');
  [...wishListBtnNodes].map(wishListBtn => {
    const productId = parseInt(wishListBtn.getAttribute('data-product-id'));
    if (wishList.includes(productId)) {
      wishListBtn.classList.add('active');
    } else {
      wishListBtn.classList.remove('active');
    }
  });
};

//Fix for cache page
const displayWishListCount = () => {
  if (userId) {
    return;
  }
  const wishList = getWishListCookie();
  let count = 0;
  if (wishList) {
    count = wishList.length;
  }
  const countNodes = document.querySelectorAll('.wcbt-show-wishlist .count');
  [...countNodes].map(countNode => {
    countNode.innerHTML = count;
  });
};
const getWishListCookie = () => {
  const productIds = (0,_mixins_cookie__WEBPACK_IMPORTED_MODULE_0__.getCookie)('wcbt_wishlist_product');
  if (productIds === null || !productIds) {
    return [];
  }
  return productIds.split(',').map(Number);
};
const updateTotal = total => {
  const wishlistCountNodes = document.querySelectorAll('.wcbt-show-wishlist .count');
  [...wishlistCountNodes].map(wishlistCountNode => {
    wishlistCountNode.innerHTML = total;
  });
};
const wishListTooltip = productWishlistNode => {
  const tooltipNode = productWishlistNode.querySelector('span.tooltip');
  if (!tooltipNode) {
    return;
  }
  if (productWishlistNode.classList.contains('active')) {
    tooltipNode.innerHTML = removeWishListTooltipText;
  } else {
    tooltipNode.innerHTML = addWishListTooltipText;
  }
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (wcbtToggleWishList);

/***/ }),

/***/ "./assets/src/js/mixins/cookie.js":
/*!****************************************!*\
  !*** ./assets/src/js/mixins/cookie.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   deleteCookie: () => (/* binding */ deleteCookie),
/* harmony export */   getCookie: () => (/* binding */ getCookie),
/* harmony export */   setCookie: () => (/* binding */ setCookie)
/* harmony export */ });
const isMultisite = WCBT_GLOBAL_OBJECT.is_multisite || false;
const blogId = WCBT_GLOBAL_OBJECT.blog_id || '';
const setCookie = (name, value, expiredDay = 0, expireHour = 0) => {
  const date = new Date();
  date.setTime(date.getTime() + expiredDay * 24 * 60 * 60 * 1000 + expireHour * 60 * 60 * 1000);
  const expires = 'expires=' + date.toUTCString();
  if (isMultisite) {
    name = `${name}_${blogId}`;
  }
  document.cookie = `${name}=${value};${expires};path=/`;
};
const getCookie = name => {
  if (isMultisite) {
    name = `${name}_${blogId}`;
  }
  name += '=';
  const decodedCookie = decodeURIComponent(document.cookie);
  const ca = decodedCookie.split(';');
  for (let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) === ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) === 0) {
      return c.substring(name.length, c.length);
    }
  }
  return '';
};
const deleteCookie = name => {
  const date = new Date();
  date.setTime(date.getTime() - 9999);
  const expires = 'expires=' + date.toUTCString();
  if (isMultisite) {
    name = `${name}_${blogId}`;
  }
  document.cookie = `${name}='';${expires};path=/`;
};

/***/ }),

/***/ "./assets/src/js/mixins/snackbar.js":
/*!******************************************!*\
  !*** ./assets/src/js/mixins/snackbar.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   displayMessagePopup: () => (/* binding */ displayMessagePopup),
/* harmony export */   messagePopup: () => (/* binding */ messagePopup)
/* harmony export */ });
const messagePopup = document.querySelector('#wcbt-snackbar');
let popupTimeout;
const displayMessagePopup = () => {
  messagePopup.classList.add('active');
  if (popupTimeout) {
    clearTimeout(popupTimeout);
  }
  popupTimeout = setTimeout(() => {
    messagePopup.classList.remove('active');
    messagePopup.innerHTML = '';
  }, '3000');
};


/***/ }),

/***/ "@wordpress/url":
/*!*****************************!*\
  !*** external ["wp","url"] ***!
  \*****************************/
/***/ ((module) => {

module.exports = window["wp"]["url"];

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
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!*************************************************!*\
  !*** ./assets/src/js/frontend/wcbt-compare.tsx ***!
  \*************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _compare_product_compare_list__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./compare-product/compare-list */ "./assets/src/js/frontend/compare-product/compare-list.js");

document.addEventListener('DOMContentLoaded', event => {
  (0,_compare_product_compare_list__WEBPACK_IMPORTED_MODULE_0__["default"])();
});
})();

/******/ })()
;
//# sourceMappingURL=wcbt-compare.js.map