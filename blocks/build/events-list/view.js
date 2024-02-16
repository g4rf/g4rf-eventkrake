/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/events-list/controls.js":
/*!*************************************!*\
  !*** ./src/events-list/controls.js ***!
  \*************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BackendLabel: () => (/* binding */ BackendLabel),
/* harmony export */   Content: () => (/* binding */ Content)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./block.json */ "./src/events-list/block.json");


function BackendLabel() {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", null, _block_json__WEBPACK_IMPORTED_MODULE_1__.title);
}
function Content({
  attributes
}) {
  const {
    showContent,
    prefix
  } = attributes;
  if (!showContent) return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: prefix + "-content"
  });
}

/***/ }),

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
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _controls__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./controls */ "./src/events-list/controls.js");
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./block.json */ "./src/events-list/block.json");






/**
 * Loads the events data into the list.
 * @param {object} arguments[0]
 *      {HTML} block: Parent block that contains the events list.
 *      {boolean} isEditor: if in block editor or not, default false
 *      {string} start: PHP date definition, default "now"
 *      {string} end: PHP date definition, default "+10 years"
 * @returns {null}
 */
function load({
  block,
  isEditor = false,
  start = 'now',
  end = '+10 years'
}) {
  const $ = jQuery;

  // prevent double loading (especially in editor)
  if ($(block).data("loading")) return;
  $(block).data("loading", true);
  const prefix = ".g4rf-eventkrake-events-list";
  const template = "g4rf-eventkrake-events-list-template";
  const list = $(prefix + "-list", block);

  // remove old blocks
  $(".g4rf-eventkrake-events-list-event", list).not("." + template).remove();
  $.getJSON("/wp-json/eventkrake/v3/events", {
    earliestStart: start,
    latestStart: end
  }, function (data) {
    $.each(data.events, function (index, eventData) {
      const eventHtml = $(prefix + "-event." + template, list).clone().removeClass(template).appendTo(list);

      // image
      $(prefix + "-image img", eventHtml).attr("src", eventData.image);
      if (!isEditor) {
        $(prefix + "-image", eventHtml).attr("href", eventData.url);
      }

      // title
      $(prefix + "-title h3 a", eventHtml).append(eventData.title);
      if (!isEditor) {
        $(prefix + "-title h3 a", eventHtml).attr("href", eventData.url);
      }

      // excerpt
      $(prefix + "-excerpt p", eventHtml).append(eventData.excerpt);

      // content
      $(prefix + "-content", eventHtml).append(eventData.content);

      // location
      const location = data.locations[eventData.locationId];
      $(prefix + "-location-title", eventHtml).append(location.title);
      // location with link
      $(prefix + "-location-title-with-link a", eventHtml).append(location.title);
      if (!isEditor) {
        $(prefix + "-location-title-with-link a", eventHtml).attr("href", location.url);
      }
      // location address
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
      if (start.toDateString() === end.toDateString()) {
        // on same day
        $(prefix + "-end-date", eventHtml).remove();
      } else {
        // not on same day
        $(prefix + "-end-date", eventHtml).append(end.toLocaleDateString(undefined, dateOptions));
      }
      $(prefix + "-end-time", eventHtml).append(end.toLocaleTimeString(undefined, timeOptions));
      // ics
      if (!isEditor) {
        $(prefix + "-ics", eventHtml).attr("href", eventData.icsUrl);
      }
      $(block).data("loading", false);
    });
  });
}

/**
 * Creates the HTML to put the event data in.
 * @param {object} arguments[0]
 *      {object} attributes The settings of the control.
 * @returns {String}
 */
function html({
  attributes
}) {
  const cssPrefix = "g4rf-eventkrake-events-list";
  const cssTemplate = cssPrefix + "-template";
  attributes.prefix = cssPrefix;
  attributes.template = cssTemplate;
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("ul", {
    className: cssPrefix + "-list"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", {
    className: cssPrefix + "-event " + cssTemplate,
    href: ""
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    className: cssPrefix + "-image",
    href: ""
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: "",
    alt: ""
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: cssPrefix + "-title"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("h3", null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    href: ""
  }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: cssPrefix + "-excerpt"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null)), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_controls__WEBPACK_IMPORTED_MODULE_3__.Content, {
    attributes: attributes
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: cssPrefix + "-date"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: cssPrefix + "-start-date"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: cssPrefix + "-start-time"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: cssPrefix + "-date-separator"
  }, "\u2013"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
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

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ ((module) => {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "./src/events-list/block.json":
/*!************************************!*\
  !*** ./src/events-list/block.json ***!
  \************************************/
/***/ ((module) => {

module.exports = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"g4rf-eventkrake/events-list","version":"0.1.0","title":"Eventkrake Events list","description":"Shows events in a list.","example":{},"category":"design","attributes":{"showContent":{"type":"boolean","default":true}},"supports":{"html":false,"anchor":true,"color":{"background":true,"gradients":true,"link":true,"text":true},"spacing":{"margin":true,"padding":true},"__experimentalBorder":{"color":true,"radius":true,"style":true,"width":true}},"textdomain":"g4rf-eventkrake","editorScript":"file:./index.js","viewScript":"file:./view.js","style":"file:./style-index.css"}');

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
/*!*********************************!*\
  !*** ./src/events-list/view.js ***!
  \*********************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _list_events__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./list-events */ "./src/events-list/list-events.js");
/**
 * Use this file for JavaScript code that you want to run in the front-end
 * on posts/pages that contain this block.
 *
 * When this file is defined as the value of the `viewScript` property
 * in `block.json` it will be enqueued on the front end of the site.
 *
 * Example:
 *
 * ```js
 * {
 *   "viewScript": "file:./view.js"
 * }
 * ```
 *
 * If you're not making any changes to this file because your project doesn't need any
 * JavaScript running in the front-end, then you should delete this file and remove
 * the `viewScript` property from `block.json`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script
 */


document.addEventListener('DOMContentLoaded', () => {
  jQuery(".wp-block-g4rf-eventkrake-events-list").each(function () {
    _list_events__WEBPACK_IMPORTED_MODULE_0__.load({
      block: this
    });
  });
});
})();

/******/ })()
;
//# sourceMappingURL=view.js.map