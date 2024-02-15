/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/events-list/list-events.js":
/*!****************************************!*\
  !*** ./src/events-list/list-events.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   html: () => (/* binding */ html),
/* harmony export */   load: () => (/* binding */ load)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

/**
 * Loads the events data into the list.
 * @param {string} start PHP date definition
 * @param {string} end PHP date definition
 * @returns {null}
 */
function load(start = 'now', end = '+10 years') {
  const $ = jQuery;
  const prefix = ".g4rf-eventkrake-events-list";
  const template = "g4rf-eventkrake-events-list-template";
  const list = $(".wp-block-g4rf-eventkrake-events-list");
  $.getJSON("/wp-json/eventkrake/v3/events", {
    earliestStart: start,
    latestStart: end
  }, function (data) {
    $.each(data.events, function (index, eventData) {
      let eventHtml = $(prefix + "-event." + template, list).clone().removeClass(template).appendTo(list);
      $(prefix + "-image", eventHtml).attr("src", eventData.image);
      $(prefix + "-title", eventHtml).append(eventData.title);
      $(prefix + "-excerpt", eventHtml).append(eventData.excerpt);
      $(prefix + "-content", eventHtml).append(eventData.content);

      // location
      let location = data.locations[eventData.locationId];
      $(prefix + "-location-title", eventHtml).append(location.title);
      $(prefix + "-location-title-with-link a", eventHtml).attr("href", location.url).append(location.title);
      $(prefix + "-location-address", eventHtml).append(location.address);

      // dates
      const start = new Date(eventData.start);
      const end = new Date(eventData.end);
      const dateOptions = {
        weekday: "short",
        day: "numeric",
        month: "short",
        year: "numeric"
      };
      const timeOptions = {
        hour: "2-digit",
        minute: "2-digit"
      };
      // start
      $(prefix + "-start-date", eventHtml).append(start.toLocaleDateString(undefined, dateOptions));
      $(prefix + "-start-time", eventHtml).append(start.toLocaleTimeString(undefined, timeOptions));
      // end
      if (start.toDateString() !== end.toDateString()) {
        // not on same day
        $(prefix + "-end-date", eventHtml).append(end.toLocaleDateString(undefined, dateOptions));
      }
      $(prefix + "-end-time", eventHtml).append(end.toLocaleTimeString(undefined, timeOptions));
      // ics
      $(prefix + "-ics", eventHtml).attr("href", eventData.icsUrl);
    });
  });
}

/**
 * Creates the HTML to put the event data in.
 * @param {type} blockProps
 * @returns {String}
 */
function html(blockProps) {
  const cssPrefix = "g4rf-eventkrake-events-list";
  const cssTemplate = cssPrefix + "-template";
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("ul", {
    ...blockProps
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", {
    className: cssPrefix + "-event " + cssTemplate
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    className: cssPrefix + "-image",
    src: "",
    alt: ""
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: cssPrefix + "-title"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: cssPrefix + "-excerpt"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: cssPrefix + "-content"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: cssPrefix + "-date"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: cssPrefix + "-start-date"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: cssPrefix + "-start-time"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: cssPrefix + "-end-date"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: cssPrefix + "-end-time"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    className: cssPrefix + "-ics",
    href: ""
  }, "ics")), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: cssPrefix + "-location"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: cssPrefix + "-location-title"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: cssPrefix + "-location-title-with-link"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    href: ""
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: cssPrefix + "-location-address"
  }))));
}

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

module.exports = window["React"];

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
/*!*************************************!*\
  !*** ./src/events-list/frontend.js ***!
  \*************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _list_events__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./list-events */ "./src/events-list/list-events.js");
/**
 * This file is defined in block.json and is loaded in frontend and backend.
 * - In the frontend it fills the html structured list with events.
 * - In the backend it just fills lists, that are already added to the post.
 */

document.addEventListener('DOMContentLoaded', () => {
  _list_events__WEBPACK_IMPORTED_MODULE_0__.load();
});
})();

/******/ })()
;
//# sourceMappingURL=frontend.js.map