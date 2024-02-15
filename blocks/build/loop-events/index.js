/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/loop-events/edit.js":
/*!*********************************!*\
  !*** ./src/loop-events/edit.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Edit)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _list_events__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./list-events */ "./src/loop-events/list-events.js");




function Edit({
  attributes,
  setAttributes,
  isSelected,
  clientId,
  context: {
    postType,
    postId,
    queryId
  }
}) {
  return _list_events__WEBPACK_IMPORTED_MODULE_3__.HTML((0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.useBlockProps)());
}

/***/ }),

/***/ "./src/loop-events/index.js":
/*!**********************************!*\
  !*** ./src/loop-events/index.js ***!
  \**********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./block.json */ "./src/loop-events/block.json");
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./style.scss */ "./src/loop-events/style.scss");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./edit */ "./src/loop-events/edit.js");
/* harmony import */ var _save__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./save */ "./src/loop-events/save.js");
/* harmony import */ var _list_events__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./list-events */ "./src/loop-events/list-events.js");







const icon = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
  version: "1.1",
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("g", null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("g", {
  transform: "translate(0 .44974)"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("circle", {
  cx: "6.1416",
  cy: "6.1019",
  r: "5.1081",
  fill: "#8a0"
}), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("circle", {
  cx: "6.1814",
  cy: "16.954",
  r: "5.1081",
  fill: "#08a"
})), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  d: "m19.739 17.323h-8.366c-1.3182 0-2.3903 0.897-2.3903 2s1.072 2 2.3903 2h8.366c1.3182 0 2.3903-0.897 2.3903-2s-1.072-2-2.3903-2z",
  strokeWidth: "1.0932"
}), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  d: "m19.739 10.323h-8.366c-1.3182 0-2.3903 0.897-2.3903 2s1.072 2 2.3903 2h8.366c1.3182 0 2.3903-0.897 2.3903-2s-1.072-2-2.3903-2z",
  strokeWidth: "1.0932"
}), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  d: "m19.739 3.323h-8.366c-1.3182 0-2.3903 0.897-2.3903 2s1.072 2 2.3903 2h8.366c1.3182 0 2.3903-0.897 2.3903-2s-1.072-2-2.3903-2z",
  strokeWidth: "1.0932"
}), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("circle", {
  cx: "5.414",
  cy: "19.323",
  r: "2.5"
}), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("circle", {
  cx: "5.414",
  cy: "12.323",
  r: "2.5"
}), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("circle", {
  cx: "5.414",
  cy: "5.323",
  r: "2.5"
})));
(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_2__.name, {
  icon: icon,
  edit: _edit__WEBPACK_IMPORTED_MODULE_4__["default"],
  save: _save__WEBPACK_IMPORTED_MODULE_5__["default"]
});

/***/ }),

/***/ "./src/loop-events/list-events.js":
/*!****************************************!*\
  !*** ./src/loop-events/list-events.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   HTML: () => (/* binding */ HTML),
/* harmony export */   loadEvents: () => (/* binding */ loadEvents)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

/**
 * Loads the event data.
 */
function loadEvents() {
  console.log("load events fom list-events.js");
}

/**
 * Creates the HTML to put the event data in.
 */
function HTML(blockProps) {
  const cssPrefix = "g4rf-eventkrake-events-list";
  const cssTemplate = cssPrefix + "-template";
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("ul", {
    ...blockProps
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", {
    className: cssPrefix + "-event " + cssTemplate
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: "",
    alt: ""
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: cssPrefix + "-title"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: cssPrefix + "-excerpt"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: cssPrefix + "-content"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: cssPrefix + "-dates"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: cssPrefix + "-date " + cssTemplate
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: cssPrefix + "-start-date"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: cssPrefix + "-start-hour"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: cssPrefix + "-end-date"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: cssPrefix + "-end-hour"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    className: cssPrefix + "-ics",
    href: ""
  }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
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

/***/ "./src/loop-events/save.js":
/*!*********************************!*\
  !*** ./src/loop-events/save.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Save)
/* harmony export */ });
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _list_events__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./list-events */ "./src/loop-events/list-events.js");


function Save({
  attributes
}) {
  return _list_events__WEBPACK_IMPORTED_MODULE_1__.HTML(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0__.useBlockProps.save());
}

/***/ }),

/***/ "./src/loop-events/style.scss":
/*!************************************!*\
  !*** ./src/loop-events/style.scss ***!
  \************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


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

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ ((module) => {

module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "./src/loop-events/block.json":
/*!************************************!*\
  !*** ./src/loop-events/block.json ***!
  \************************************/
/***/ ((module) => {

module.exports = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"g4rf-eventkrake/events-list","version":"0.1.0","title":"Eventkrake Events list","category":"design","description":"Shows events in a list.","example":{},"attributes":{},"supports":{"html":false},"textdomain":"g4rf-eventkrake","editorScript":"file:./index.js","style":"file:./style-index.css","script":"file:./frontend.js"}');

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
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
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
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"loop-events/index": 0,
/******/ 			"loop-events/style-index": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = globalThis["webpackChunkg4rf_eventkrake_blocks"] = globalThis["webpackChunkg4rf_eventkrake_blocks"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["loop-events/style-index"], () => (__webpack_require__("./src/loop-events/index.js")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
//# sourceMappingURL=index.js.map