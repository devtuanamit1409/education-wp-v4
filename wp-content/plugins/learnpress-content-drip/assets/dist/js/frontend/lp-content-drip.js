/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

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
/*!***************************************************!*\
  !*** ./assets/src/js/frontend/lp-content-drip.js ***!
  \***************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/**
 * Js for content drip on frontend
 *
 * @since 4.0.3
 * @version 1.0.0
 */


class lpContentDrip {
  elContentDrip;
  totalTimeRemaining;
  counterTimeToReload() {
    if (!this.totalTimeRemaining) return;
    const inputReload = this.totalTimeRemaining;
    if (inputReload === null) return;
    const timeReload = inputReload.value;
    if (timeReload > 0 && timeReload <= 86400000) {
      setTimeout(function () {
        window.location.reload();
      }, timeReload);
    }
  }
  countDownTime() {
    if (!this.totalTimeRemaining) return;
    let elMinute = this.elContentDrip.querySelector('.minute');
    let elSecond = this.elContentDrip.querySelector('.second');
    let milliseconds = this.totalTimeRemaining.value;
    const count = setInterval(() => {
      milliseconds -= 1000;
      if (milliseconds < 60000) {
        const second = milliseconds / 1000;
        if (elMinute) {
          elMinute.textContent = second;
        } else if (elSecond) {
          elSecond.textContent = second;
        }
        elMinute.textContent += (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('seconds', 'learnpress-content-drip');
      }
      if (milliseconds < 0) {
        clearInterval(count);
      }
    }, 1000);
  }
  domLoaded() {
    this.elContentDrip = document.querySelector('#learn-press-content-item');
    if (!this.elContentDrip) return;
    this.totalTimeRemaining = this.elContentDrip.querySelector('#ctd-time-remaining');
    this.counterTimeToReload();
    this.countDownTime();
  }
}
window.jsContentDrip = new lpContentDrip();
document.addEventListener('DOMContentLoaded', function () {
  window.jsContentDrip.domLoaded();
});
})();

/******/ })()
;
//# sourceMappingURL=lp-content-drip.js.map