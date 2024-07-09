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
  }, start, seperator, end, ics);
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

/***/ "./src/events-list/edit.js":
/*!*********************************!*\
  !*** ./src/events-list/edit.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Edit)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _list_events__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./list-events */ "./src/events-list/list-events.js");
/* harmony import */ var _controls__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./controls */ "./src/events-list/controls.js");







function Edit({
  attributes,
  setAttributes
}) {
  const {
    dateStart,
    dateEnd,
    showImage,
    showTitle,
    showExcerpt,
    showContent,
    showSeperator,
    showDate,
    showDateStart,
    showDateEnd,
    showDateIcs,
    showLocation,
    showLocationWithLink,
    showLocationAddress
  } = attributes;

  // load events
  const list = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    _list_events__WEBPACK_IMPORTED_MODULE_4__.load({
      block: list.current,
      isEditor: true
    });
  });
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    ...(0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.useBlockProps)(),
    ref: list
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.InspectorControls, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Date Range', 'eventkrake')
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.TextControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Start date', 'eventkrake'),
    value: dateStart,
    onChange: value => {
      setAttributes({
        dateStart: value
      });
      _list_events__WEBPACK_IMPORTED_MODULE_4__.load({
        block: list.current,
        isEditor: true
      });
    }
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.TextControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('End date', 'eventkrake'),
    value: dateEnd,
    onChange: value => {
      setAttributes({
        dateEnd: value
      });
      _list_events__WEBPACK_IMPORTED_MODULE_4__.load({
        block: list.current,
        isEditor: true
      });
    }
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Show', 'eventkrake')
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
    checked: !!showTitle,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Show title', 'eventkrake'),
    onChange: () => {
      setAttributes({
        showTitle: !showTitle
      });
      _list_events__WEBPACK_IMPORTED_MODULE_4__.load({
        block: list.current,
        isEditor: true
      });
    }
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
    checked: !!showExcerpt,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Show excerpt', 'eventkrake'),
    onChange: () => {
      setAttributes({
        showExcerpt: !showExcerpt
      });
      _list_events__WEBPACK_IMPORTED_MODULE_4__.load({
        block: list.current,
        isEditor: true
      });
    }
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
    checked: !!showContent,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Show content', 'eventkrake'),
    onChange: () => {
      setAttributes({
        showContent: !showContent
      });
      _list_events__WEBPACK_IMPORTED_MODULE_4__.load({
        block: list.current,
        isEditor: true
      });
    }
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Date', 'eventkrake')
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
    checked: !!showDate,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Show date', 'eventkrake'),
    onChange: () => {
      setAttributes({
        showDate: !showDate
      });
      _list_events__WEBPACK_IMPORTED_MODULE_4__.load({
        block: list.current,
        isEditor: true
      });
    }
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
    checked: !!showDateStart,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Show start date', 'eventkrake'),
    onChange: () => {
      setAttributes({
        showDateStart: !showDateStart
      });
      _list_events__WEBPACK_IMPORTED_MODULE_4__.load({
        block: list.current,
        isEditor: true
      });
    }
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
    checked: !!showDateEnd,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Show end date', 'eventkrake'),
    onChange: () => {
      setAttributes({
        showDateEnd: !showDateEnd
      });
      _list_events__WEBPACK_IMPORTED_MODULE_4__.load({
        block: list.current,
        isEditor: true
      });
    }
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
    checked: !!showDateIcs,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Show ics link', 'eventkrake'),
    onChange: () => {
      setAttributes({
        showDateIcs: !showDateIcs
      });
      _list_events__WEBPACK_IMPORTED_MODULE_4__.load({
        block: list.current,
        isEditor: true
      });
    }
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Location', 'eventkrake')
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
    checked: !!showLocation,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Show location', 'eventkrake'),
    onChange: () => {
      setAttributes({
        showLocation: !showLocation
      });
      _list_events__WEBPACK_IMPORTED_MODULE_4__.load({
        block: list.current,
        isEditor: true
      });
    }
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
    checked: !!showLocationWithLink,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Link location to location page', 'eventkrake'),
    onChange: () => {
      setAttributes({
        showLocationWithLink: !showLocationWithLink
      });
      _list_events__WEBPACK_IMPORTED_MODULE_4__.load({
        block: list.current,
        isEditor: true
      });
    }
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
    checked: !!showLocationAddress,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Show location address', 'eventkrake'),
    onChange: () => {
      setAttributes({
        showLocationAddress: !showLocationAddress
      });
      _list_events__WEBPACK_IMPORTED_MODULE_4__.load({
        block: list.current,
        isEditor: true
      });
    }
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Image', 'eventkrake')
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
    checked: !!showImage,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Show image', 'eventkrake'),
    onChange: () => {
      setAttributes({
        showImage: !showImage
      });
      _list_events__WEBPACK_IMPORTED_MODULE_4__.load({
        block: list.current,
        isEditor: true
      });
    }
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Seperator', 'eventkrake')
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
    checked: !!showSeperator,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Show event seperator', 'eventkrake'),
    onChange: () => {
      setAttributes({
        showSeperator: !showSeperator
      });
      _list_events__WEBPACK_IMPORTED_MODULE_4__.load({
        block: list.current,
        isEditor: true
      });
    }
  }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_list_events__WEBPACK_IMPORTED_MODULE_4__.html, {
    attributes: attributes
  }));
}

/***/ }),

/***/ "./src/events-list/index.js":
/*!**********************************!*\
  !*** ./src/events-list/index.js ***!
  \**********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./block.json */ "./src/events-list/block.json");
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./style.scss */ "./src/events-list/style.scss");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./edit */ "./src/events-list/edit.js");
/* harmony import */ var _save__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./save */ "./src/events-list/save.js");






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
  console.log(start, end);

  // remove old blocks
  $(".g4rf-eventkrake-events-list-event", list).not("." + template).remove();

  // hide noevents message
  $(".g4rf-eventkrake-noevents", list).hide();

  // show spinner
  $(".g4rf-eventkrake-spinner", list).show();
  $.getJSON("/wp-json/eventkrake/v3/events", {
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

/***/ "./src/events-list/save.js":
/*!*********************************!*\
  !*** ./src/events-list/save.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Save)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _list_events__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./list-events */ "./src/events-list/list-events.js");



function Save({
  attributes
}) {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    ..._wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.useBlockProps.save()
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_list_events__WEBPACK_IMPORTED_MODULE_2__.html, {
    attributes: attributes
  }));
}

/***/ }),

/***/ "./src/events-list/style.scss":
/*!************************************!*\
  !*** ./src/events-list/style.scss ***!
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

/***/ "./src/events-list/block.json":
/*!************************************!*\
  !*** ./src/events-list/block.json ***!
  \************************************/
/***/ ((module) => {

module.exports = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"g4rf-eventkrake/events-list","version":"0.2.0","title":"Eventkrake Events list","description":"Shows events in a list.","example":{},"category":"design","attributes":{"dateStart":{"type":"string","source":"attribute","selector":".g4rf-eventkrake-events-list-list","attribute":"data-start","default":"now"},"dateEnd":{"type":"string","source":"attribute","selector":".g4rf-eventkrake-events-list-list","attribute":"data-end","default":"+10 years"},"showImage":{"type":"boolean","default":true},"showTitle":{"type":"boolean","default":true},"showExcerpt":{"type":"boolean","default":true},"showContent":{"type":"boolean","default":false},"showSeperator":{"type":"boolean","default":true},"showDate":{"type":"boolean","default":true},"showDateStart":{"type":"boolean","default":true},"showDateEnd":{"type":"boolean","default":false},"showDateIcs":{"type":"boolean","default":true},"showLocation":{"type":"boolean","default":true},"showLocationWithLink":{"type":"boolean","default":true},"showLocationAddress":{"type":"boolean","default":true}},"supports":{"html":false,"anchor":true,"color":{"background":true,"gradients":true,"link":true,"text":true},"spacing":{"margin":true,"padding":true},"__experimentalBorder":{"color":true,"radius":true,"style":true,"width":true}},"textdomain":"eventkrake","editorScript":"file:./index.js","viewScript":"file:./view.js","style":"file:./style-index.css"}');

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
/******/ 			"events-list/index": 0,
/******/ 			"events-list/style-index": 0
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
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["events-list/style-index"], () => (__webpack_require__("./src/events-list/index.js")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
//# sourceMappingURL=index.js.map