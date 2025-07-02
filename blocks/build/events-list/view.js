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
/* harmony export */   Content: () => (/* binding */ Content),
/* harmony export */   Date: () => (/* binding */ Date),
/* harmony export */   Excerpt: () => (/* binding */ Excerpt),
/* harmony export */   Image: () => (/* binding */ Image),
/* harmony export */   Location: () => (/* binding */ Location),
/* harmony export */   Seperator: () => (/* binding */ Seperator),
/* harmony export */   Title: () => (/* binding */ Title)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

function Image({
  attributes
}) {
  const {
    showImage,
    prefix
  } = attributes;
  if (!showImage) return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    className: prefix + "-image",
    href: ""
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: "",
    alt: ""
  }));
}
function Title({
  attributes
}) {
  const {
    showTitle,
    prefix
  } = attributes;
  if (!showTitle) return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: prefix + "-title"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("h4", null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    href: ""
  })));
}
function Date({
  attributes
}) {
  const {
    showDate,
    showDateStart,
    showDateEnd,
    showDateIcs,
    prefix
  } = attributes;
  if (!showDate) return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null);
  let start = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null);
  if (showDateStart) {
    start = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      className: prefix + "-start-date"
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      className: prefix + "-start-time"
    }));
  }
  let end = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null);
  if (showDateEnd) {
    end = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      className: prefix + "-end-date"
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      className: prefix + "-end-time"
    }));
  }
  let door = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null);
  if (showDateStart) {
    door = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      className: prefix + "-door-label"
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      className: prefix + "-door-time"
    }));
  }
  let seperator = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null);
  if (showDateStart && showDateEnd) {
    seperator = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      className: prefix + "-date-separator"
    }, "\u2013");
  }
  let ics = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null);
  if (showDateIcs) {
    ics = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
      className: prefix + "-date-ics",
      href: ""
    }, "ics");
  }
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: prefix + "-date"
  }, start, seperator, end, door, ics);
}
function Location({
  attributes
}) {
  const {
    showLocation,
    showLocationWithLink,
    showLocationAddress,
    prefix
  } = attributes;
  if (!showLocation) return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null);
  let title = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null);
  if (showLocationWithLink) {
    title = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      className: prefix + "-location-title-with-link"
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
      href: ""
    }));
  } else {
    title = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      className: prefix + "-location-title"
    });
  }
  let address = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null);
  if (showLocationAddress) {
    address = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      className: prefix + "-location-address"
    });
  }
  let seperator = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null);
  if (showLocationAddress) {
    seperator = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      className: prefix + "-location-seperator"
    }, "//");
  }
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: prefix + "-location"
  }, title, seperator, address);
}
function Excerpt({
  attributes
}) {
  const {
    showExcerpt,
    prefix
  } = attributes;
  if (!showExcerpt) return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: prefix + "-excerpt"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null));
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
function Seperator({
  attributes
}) {
  const {
    showSeperator,
    prefix
  } = attributes;
  if (!showSeperator) return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("hr", {
    className: prefix + "-seperator"
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
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _controls__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./controls */ "./src/events-list/controls.js");






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
  isEditor = false
}) {
  const $ = jQuery;

  // prevent double loading (especially in editor)
  if ($(block).data("loading")) return;
  $(block).data("loading", true);
  const prefix = ".g4rf-eventkrake-events-list";
  const template = "g4rf-eventkrake-events-list-template";
  const list = $(prefix + "-list", block);
  const start = $(prefix + "-list", block).attr("data-start");
  const end = $(prefix + "-list", block).attr("data-end");

  // remove old blocks
  $(".g4rf-eventkrake-events-list-event", list).not("." + template).remove();

  // hide noevents message
  $(".g4rf-eventkrake-noevents", list).hide();

  // show spinner
  $(".g4rf-eventkrake-spinner", list).show();

  // add compatibility with WP Multilang
  let language = "";
  const bodyClasses = $("body").attr("class");
  const languages = bodyClasses.match(/language-(?<language>[a-z]{2})/);
  if (languages) {
    language = "/" + languages.groups.language;
  }
  $.getJSON(language + "/wp-json/eventkrake/v3/events", {
    earliestEnd: start,
    latestStart: end
  }, function (data) {
    // hide spinner
    $(".g4rf-eventkrake-spinner", list).hide();

    // show noevents message
    $(".g4rf-eventkrake-noevents", list).show();

    // crawl events
    $.each(data.events, function (index, eventData) {
      // hide noevents message
      $(".g4rf-eventkrake-noevents", list).hide();
      const eventHtml = $(prefix + "-event." + template, list).clone().removeClass(template).appendTo(list);

      // image
      $(prefix + "-image img", eventHtml).attr("src", eventData.image);
      if (!isEditor) {
        $(prefix + "-image", eventHtml).attr("href", eventData.url);
      }

      // title
      $(prefix + "-title a", eventHtml).append(eventData.title);
      if (!isEditor) {
        $(prefix + "-title a", eventHtml).attr("href", eventData.url);
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
      let door = false;
      if (eventData.door != false) door = new Date(eventData.door);
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
      //door
      if (door != false) {
        $(prefix + "-door-label", eventHtml).append((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Doors:', 'eventkrake'));
        $(prefix + "-door-time", eventHtml).append(door.toLocaleTimeString(undefined, timeOptions));
      } else {
        $(prefix + "-door-label", eventHtml).hide();
        $(prefix + "-door-time", eventHtml).hide();
      }
      // ics
      if (!isEditor) {
        $(prefix + "-ics", eventHtml).attr("href", eventData.icsUrl);
      }

      // classes; show them only in frontend
      if (!isEditor) {
        let classes = [];

        // id
        classes.push(Eventkrake.cssClass(eventData.id, "g4rf-eventkrake-id"));
        // uid
        classes.push(Eventkrake.cssClass(eventData.uid, "g4rf-eventkrake-uid"));
        // title
        classes.push(Eventkrake.cssClass(eventData.title, "g4rf-eventkrake-title"));

        // location id
        classes.push(Eventkrake.cssClass(location.id, "g4rf-eventkrake-location-id"));
        // location name
        classes.push(Eventkrake.cssClass(location.title, "g4rf-eventkrake-location-title"));

        // start day
        classes.push(Eventkrake.cssClass(start.getDate(), "g4rf-eventkrake-start-day"));
        // start month
        classes.push(Eventkrake.cssClass(start.getMonth() + 1, "g4rf-eventkrake-start-month"));
        // start year
        classes.push(Eventkrake.cssClass(start.getFullYear(), "g4rf-eventkrake-start-year"));
        // start weekday
        classes.push(Eventkrake.cssClass(start.getDay(), "g4rf-eventkrake-start-weekday"));
        // start hour
        classes.push(Eventkrake.cssClass(start.getHours(), "g4rf-eventkrake-start-hour"));
        // start minute
        classes.push(Eventkrake.cssClass(start.getMinutes(), "g4rf-eventkrake-start-minute"));

        // end day
        classes.push(Eventkrake.cssClass(end.getDate(), "g4rf-eventkrake-end-day"));
        // end month
        classes.push(Eventkrake.cssClass(end.getMonth() + 1, "g4rf-eventkrake-end-month"));
        // end year
        classes.push(Eventkrake.cssClass(end.getFullYear(), "g4rf-eventkrake-end-year"));
        // end weekday
        classes.push(Eventkrake.cssClass(end.getDay(), "g4rf-eventkrake-end-weekday"));
        // end hour
        classes.push(Eventkrake.cssClass(end.getHours(), "g4rf-eventkrake-end-hour"));
        // end minute
        classes.push(Eventkrake.cssClass(end.getMinutes(), "g4rf-eventkrake-end-minute"));
        // door
        if (door != false) {
          // door hour
          classes.push(Eventkrake.cssClass(door.getHours(), "g4rf-eventkrake-door-hour"));
          // door minute
          classes.push(Eventkrake.cssClass(door.getMinutes(), "g4rf-eventkrake-door-minute"));
        }

        // artists
        if (eventData.artists.length > 0) {
          // has artists
          classes.push("g4rf-eventkrake-has-artists");
          eventData.artists.forEach(function (artistId) {
            const artist = data.artists[artistId];

            // artist id
            classes.push(Eventkrake.cssClass(artist.id, "g4rf-eventkrake-artist-id"));
            // artist name
            classes.push(Eventkrake.cssClass(artist.title, "g4rf-eventkrake-artist-title"));
          });
        }

        // eventkrake categories
        if (eventData.categories.length > 0) {
          // has eventkrake categories
          classes.push("g4rf-eventkrake-has-categories");
          eventData.categories.forEach(function (category) {
            // eventkrake category
            classes.push(Eventkrake.cssClass(category, "g4rf-eventkrake-category"));
          });
        }

        // wp categories
        if (eventData.wpcategories.length > 0) {
          // has wp categories
          classes.push("g4rf-eventkrake-has-wpcategories");
          eventData.wpcategories.forEach(function (wpCategory) {
            // wp category
            classes.push(Eventkrake.cssClass(wpCategory, "g4rf-eventkrake-wpcategory"));
          });
        }

        // wp tags
        if (eventData.wptags.length > 0) {
          // has wp tags
          classes.push("g4rf-eventkrake-has-wptags");
          eventData.wptags.forEach(function (wpTag) {
            // wp tag
            classes.push(Eventkrake.cssClass(wpTag, "g4rf-eventkrake-wptag"));
          });
        }
        eventHtml.addClass(classes);
      }
    });
  }).always(function () {
    $(block).data("loading", false);
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
  const prefix = "g4rf-eventkrake-events-list";
  const template = prefix + "-template";
  attributes.prefix = prefix;
  attributes.template = template;
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: prefix + "-list",
    "data-start": attributes.dateStart,
    "data-end": attributes.dateEnd
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "g4rf-eventkrake-spinner"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "g4rf-eventkrake-noevents"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('No events at this time.', 'eventkrake')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: prefix + "-event " + template
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_controls__WEBPACK_IMPORTED_MODULE_4__.Image, {
    attributes: attributes
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: prefix + "-info"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_controls__WEBPACK_IMPORTED_MODULE_4__.Title, {
    attributes: attributes
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_controls__WEBPACK_IMPORTED_MODULE_4__.Date, {
    attributes: attributes
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_controls__WEBPACK_IMPORTED_MODULE_4__.Location, {
    attributes: attributes
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_controls__WEBPACK_IMPORTED_MODULE_4__.Excerpt, {
    attributes: attributes
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_controls__WEBPACK_IMPORTED_MODULE_4__.Content, {
    attributes: attributes
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_controls__WEBPACK_IMPORTED_MODULE_4__.Seperator, {
    attributes: attributes
  })));
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
/******/ })()
;
//# sourceMappingURL=view.js.map