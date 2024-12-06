/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

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

/***/ "./assets/src/js/frontend/wishlist/product-list.js":
/*!*********************************************************!*\
  !*** ./assets/src/js/frontend/wishlist/product-list.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/url */ "@wordpress/url");
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _mixins_search__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../mixins/search */ "./assets/src/js/frontend/mixins/search.js");
/* harmony import */ var _toggle_wishlist__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./toggle-wishlist */ "./assets/src/js/frontend/wishlist/toggle-wishlist.js");
/* harmony import */ var _mixins_cookie__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../mixins/cookie */ "./assets/src/js/mixins/cookie.js");




const wishlistNode = document.querySelector('#wcbt-wishlist');
const wishListTitleNode = wishlistNode.querySelector('.wcbt-wishlist-title');
const containerNode = wishlistNode.querySelector('.wcbt-product-container');
const messagePopup = document.querySelector('#wcbt-snackbar');
const {
  __
} = wp.i18n;
let popupTimeout;

//-----------
const restNamespace = WCBT_GLOBAL_OBJECT.rest_namespace || '';
const defaultQuery = {
  orderby: 'name',
  order: 'asc'
};
let query = {};
const userId = WCBT_GLOBAL_OBJECT.user_id || '';
const wcbtListProduct = () => {
  if (!containerNode) {
    return;
  }
  const queryParams = (0,_mixins_search__WEBPACK_IMPORTED_MODULE_1__.getParamsOfUrl)();
  if (Object.keys(queryParams).length) {
    query = {
      ...query,
      ...queryParams
    };
  }
  if (!userId) {
    const productIds = (0,_toggle_wishlist__WEBPACK_IMPORTED_MODULE_2__.getWishListCookie)();
    query = {
      ...query,
      post_in: productIds
    };
  }
  getProducts(query);
  removeWishList();
};
const getProducts = (queryParam = {}, isLoadmore = false) => {
  if (isLoadmore === false) {
    containerNode.style.opacity = 0.4;
  }
  const param = {
    ...defaultQuery,
    ...queryParam
  };
  wp.apiFetch({
    path: (0,_wordpress_url__WEBPACK_IMPORTED_MODULE_0__.addQueryArgs)('/' + restNamespace + '/wishlist', param),
    method: 'GET'
  }).then(res => {
    if (res.data.content !== undefined) {
      if (isLoadmore) {
        containerNode.insertAdjacentHTML('beforeend', res.data.content);
      } else {
        containerNode.innerHTML = res.data.content;
      }
    } else if (res.msg && isLoadmore === false) {
      containerNode.innerHTML = `<p>${res.msg}</p>`;
    }
    pagination(res);
  }).catch(err => {
    console.log(err);
  }).finally(() => {
    const params = query;
    if (!userId) {
      delete params.post_in;
    }
    const urlPush = (0,_mixins_search__WEBPACK_IMPORTED_MODULE_1__.getUrl)(params);
    window.history.pushState('', '', urlPush);
    containerNode.style.opacity = 1;
  });
};
const pagination = res => {
  if (res.data.pagination !== undefined) {
    wishlistNode.querySelector('.wcbt-pagination').innerHTML = res.data.pagination;
  } else {
    wishlistNode.querySelector('.wcbt-pagination').innerHTML = '';
  }
  if (res.data.from_to !== undefined) {
    wishlistNode.querySelector('.wcbt-from-to').innerHTML = res.data.from_to;
  } else {
    wishlistNode.querySelector('.wcbt-from-to').innerHTML = '';
  }
  const paginationNode = wishlistNode.querySelector('.wcbt-pagination');
  if (!!paginationNode) {
    const pageNodes = paginationNode.querySelectorAll('a');
    for (let i = 0; i < pageNodes.length; i++) {
      pageNodes[i].addEventListener('click', function (event) {
        event.preventDefault();
        const page = this.getAttribute('data-page');
        query = {
          ...query,
          page
        };
        if (!userId) {
          query = {
            ...query,
            post_in: (0,_toggle_wishlist__WEBPACK_IMPORTED_MODULE_2__.getWishListCookie)()
          };
        }
        getProducts(query);
        wishlistNode.scrollIntoView({
          behavior: 'smooth'
        });
      });
    }
  }
};
const removeWishList = () => {
  document.addEventListener('click', function (event) {
    const removeNodes = containerNode.querySelectorAll('tr .product-remove');
    if (!removeNodes) {
      return;
    }
    for (let i = 0; i < removeNodes.length; i++) {
      const removeNode = removeNodes[i];
      if (removeNode.contains(event.target)) {
        event.preventDefault();
        let productId = removeNode.closest('tr').getAttribute('data-product-id');
        productId = parseInt(productId);
        removeNode.disabled = true;
        if (userId) {
          const data = {
            product_id: productId
          };
          wp.apiFetch({
            path: '/' + restNamespace + '/wishlist',
            method: 'POST',
            data
          }).then(res => {
            if (res.status === 'success') {
              // wishListNode.classList.toggle( 'active' );
            }
            const query = {
              page: 1
            };
            messagePopup.innerHTML = __('Product WishList removed', 'wcbt');
            displayMessagePopup();
            (0,_toggle_wishlist__WEBPACK_IMPORTED_MODULE_2__.updateTotal)(res.data.total);
            getProducts(query);
          }).catch(err => {
            console.log(err);
          }).finally(() => {
            removeNode.disabled = false;
          });
        } else {
          let productIds = (0,_toggle_wishlist__WEBPACK_IMPORTED_MODULE_2__.getWishListCookie)();
          if (productIds && productIds.includes(productId)) {
            //Remove wishlist product
            productIds = productIds.filter(item => item !== productId);
            messagePopup.innerHTML = __('Product WishList removed', 'wcbt');
          }
          (0,_mixins_cookie__WEBPACK_IMPORTED_MODULE_3__.setCookie)('wcbt_wishlist_product', productIds.join(','), 30);
          displayMessagePopup();
          (0,_toggle_wishlist__WEBPACK_IMPORTED_MODULE_2__.updateTotal)(productIds.length);
          const query = {
            post_in: productIds
          };
          getProducts(query);
        }
      }
    }
  });
};
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
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (wcbtListProduct);

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
/*!**************************************************!*\
  !*** ./assets/src/js/frontend/wcbt-wishlist.tsx ***!
  \**************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wishlist_product_list__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./wishlist/product-list */ "./assets/src/js/frontend/wishlist/product-list.js");

document.addEventListener('DOMContentLoaded', event => {
  (0,_wishlist_product_list__WEBPACK_IMPORTED_MODULE_0__["default"])();
});
})();

/******/ })()
;
//# sourceMappingURL=wcbt-wishlist.js.map