/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "@wordpress/api-fetch":
/*!**********************************!*\
  !*** external ["wp","apiFetch"] ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["wp"]["apiFetch"];

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
/*!*****************************************!*\
  !*** ./src/block-blocksolid-gateway.js ***!
  \*****************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }
var registerPlugin = wp.plugins.registerPlugin;
var useState = wp.element.useState;
registerPlugin('blocksolid-gateway-plugin', {
  render: function render() {
    return /*#__PURE__*/React.createElement(Blocksolid_Gateway_Plugin, null);
  }
});
var __ = wp.i18n.__;
var compose = wp.compose.compose;
var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
var _wp$components = wp.components,
  ToggleControl = _wp$components.ToggleControl,
  PanelRow = _wp$components.PanelRow;

var Blocksolid_Gateway_Plugin = function Blocksolid_Gateway_Plugin(_ref) {
  var postType = _ref.postType,
    postMeta = _ref.postMeta,
    setPostMeta = _ref.setPostMeta;
  var post_metas = wp.data.select('core/editor').getEditedPostAttribute('meta');
  var _useState = useState(post_metas._blocksolid_gateway_members_only),
    _useState2 = _slicedToArray(_useState, 2),
    isChecked = _useState2[0],
    setChecked = _useState2[1];
  var el = wp.element.createElement;
  var member = el('svg', {
    width: 14,
    height: 14,
    viewBox: '-450 0 2000 2000',
    transform: "scale(1.5,1.5)"
  }, el('path', {
    d: "M 1531.308594 1487.601563 C 1531.308594 1208.960938 1305.429688 983.078125 1026.800781 983.078125 L 973.203125 983.078125 C 694.566406 983.078125 468.6875 1208.960938 468.6875 1487.601563 C 468.6875 1533.828125 506.167969 1571.308594 552.40625 1571.308594 L 1447.589844 1571.308594 C 1493.828125 1571.308594 1531.308594 1533.828125 1531.308594 1487.601563z"
  }), el('path', {
    d: "M 973.203125 998.460938 C 703.496094 998.460938 484.070313 1217.890625 484.070313 1487.601563 C 484.070313 1525.28125 514.726563 1555.929688 552.40625 1555.929688 L 1447.589844 1555.929688 C 1485.269531 1555.929688 1515.929688 1525.28125 1515.929688 1487.601563 C 1515.929688 1217.890625 1296.511719 998.460938 1026.800781 998.460938 Z M 1447.589844 1586.699219 L 552.40625 1586.699219 C 497.761719 1586.699219 453.304688 1542.238281 453.304688 1487.601563 C 453.304688 1348.730469 507.382813 1218.171875 605.578125 1119.96875 C 703.777344 1021.78125 834.335938 967.699219 973.203125 967.699219 L 1026.800781 967.699219 C 1165.660156 967.699219 1296.21875 1021.78125 1394.421875 1119.96875 C 1492.609375 1218.171875 1546.691406 1348.730469 1546.691406 1487.601563 C 1546.691406 1542.238281 1502.238281 1586.699219 1447.589844 1586.699219z"
  }), el('path', {
    d: "M 685.464844 745.050781 C 685.464844 918.769531 826.285156 1059.589844 1000 1059.589844 C 1173.710938 1059.589844 1314.539063 918.769531 1314.539063 745.050781 C 1314.539063 571.339844 1173.710938 430.519531 1000 430.519531 C 826.285156 430.519531 685.464844 571.339844 685.464844 745.050781z"
  }), el('path', {
    d: "M 1000 447.730469 C 836.054688 447.730469 702.675781 581.109375 702.675781 745.050781 C 702.675781 909 836.054688 1042.378906 1000 1042.378906 C 1163.941406 1042.378906 1297.320313 909 1297.320313 745.050781 C 1297.320313 581.109375 1163.941406 447.730469 1000 447.730469 Z M 1000 1076.800781 C 817.070313 1076.800781 668.246094 927.980469 668.246094 745.050781 C 668.246094 562.128906 817.070313 413.300781 1000 413.300781 C 1182.929688 413.300781 1331.75 562.128906 1331.75 745.050781 C 1331.75 927.980469 1182.929688 1076.800781 1000 1076.800781z"
  }));
  var anchor = el('svg', {
    width: 20,
    height: 20,
    viewBox: '-30 -20 100 130',
    transform: "scale(1.3,1.3)"
  }, el('path', {
    d: "M 41.863281 19.726563 C 38.070313 19.726563 34.992188 16.652344 34.992188 12.859375 C 34.992188 9.0625 38.070313 5.988281 41.863281 5.988281 C 45.65625 5.988281 48.734375 9.0625 48.734375 12.859375 C 48.734375 16.652344 45.65625 19.726563 41.863281 19.726563 Z M 81.804688 63.132813 C 78.386719 65.878906 74.601563 70.667969 74.601563 70.667969 C 74.601563 70.667969 77.269531 71.140625 76.53125 72.132813 C 65.285156 87.3125 48.105469 85.382813 48.105469 44.214844 C 48.105469 43.191406 48.105469 42.230469 48.105469 41.304688 C 49.320313 41.21875 50.582031 41.160156 51.769531 41.160156 C 56.210938 41.160156 61.675781 41.964844 61.675781 41.964844 L 61.675781 35.332031 C 61.675781 35.332031 56.210938 36.136719 51.769531 36.136719 C 50.582031 36.136719 49.320313 36.078125 48.105469 35.996094 L 47.957031 24.597656 C 52.148438 22.480469 54.722656 17.875 54.722656 12.859375 C 54.722656 5.753906 48.964844 -0.00390625 41.863281 -0.00390625 C 34.761719 -0.00390625 29.003906 5.753906 29.003906 12.859375 C 29.003906 17.875 31.582031 22.480469 35.773438 24.597656 L 35.621094 35.996094 C 34.40625 36.078125 33.148438 36.136719 31.957031 36.136719 C 27.515625 36.136719 22.050781 35.332031 22.050781 35.332031 L 22.050781 41.964844 C 22.050781 41.964844 27.515625 41.160156 31.957031 41.160156 C 33.148438 41.160156 34.40625 41.21875 35.621094 41.304688 C 35.621094 42.230469 35.621094 43.191406 35.621094 44.214844 C 35.621094 85.382813 18.441406 87.3125 7.195313 72.132813 C 6.457031 71.140625 9.125 70.667969 9.125 70.667969 C 9.125 70.667969 5.339844 65.878906 1.921875 63.132813 C -0.589844 68.253906 0.0820313 73.679688 0.0820313 73.679688 C 0.0820313 73.679688 1.488281 73.984375 2.359375 74.117188 C 7.179688 84.566406 17.832031 91.699219 28.582031 96.320313 C 39.492188 101.011719 41.863281 104.660156 41.863281 104.660156 C 41.863281 104.660156 44.234375 101.011719 55.144531 96.320313 C 65.894531 91.699219 76.546875 84.566406 81.367188 74.117188 C 82.238281 73.984375 83.644531 73.679688 83.644531 73.679688 C 83.644531 73.679688 84.316406 68.253906 81.804688 63.132813z"
  }));
  var arch = el('svg', {
    width: 20,
    height: 20,
    viewBox: '-400 0 2010 2010',
    transform: "scale(2.2,2.2)"
  }, el('path', {
    d: "M 709.332031 1342.898438 C 709.332031 1366.121094 690.339844 1385.140625 667.097656 1385.140625 C 643.882813 1385.140625 624.859375 1366.121094 624.859375 1342.898438 L 624.859375 1005.011719 C 624.859375 795.929688 795.929688 624.859375 1005.011719 624.859375 C 1214.070313 624.859375 1385.140625 795.929688 1385.140625 1005.011719 L 1385.140625 1342.898438 C 1385.140625 1366.121094 1366.121094 1385.140625 1342.898438 1385.140625 C 1319.660156 1385.140625 1300.671875 1366.121094 1300.671875 1342.898438 L 1300.671875 1005.011719 C 1300.671875 842.390625 1167.609375 709.328125 1005.011719 709.328125 C 842.390625 709.328125 709.332031 842.390625 709.332031 1005.011719 Z M 1131.71875 1342.898438 L 1131.71875 1005.011719 C 1131.71875 935.308594 1074.691406 878.28125 1005.011719 878.28125 C 935.3125 878.28125 878.277344 935.308594 878.277344 1005.011719 L 878.277344 1342.898438 C 878.277344 1366.121094 859.285156 1385.140625 836.039063 1385.140625 C 812.824219 1385.140625 793.804688 1366.121094 793.804688 1342.898438 L 793.804688 1005.011719 C 793.804688 888.851563 888.851563 793.800781 1005.011719 793.800781 C 1121.148438 793.800781 1216.199219 888.851563 1216.199219 1005.011719 L 1216.199219 1342.898438 C 1216.199219 1366.121094 1197.171875 1385.140625 1173.960938 1385.140625 C 1150.710938 1385.140625 1131.71875 1366.121094 1131.71875 1342.898438z"
  }));
  function pha_toggle_meta() {
    toggle_overlay_feature_active();
    setChecked(!isChecked);
  }
  return el(PluginDocumentSettingPanel, {
    name: 'blocksolid_gateway',
    title: 'Gateway ',
    icon: arch,
    initialOpen: "false"
  }, [el(PanelRow, {
    key: 'blocksolid_gateway_panelrow'
  }, el(ToggleControl, {
    label: __('Members Only'),
    checked: isChecked,
    onChange: function onChange(value) {
      //Update the meta property
      wp.data.dispatch('core/editor').editPost({
        meta: {
          _blocksolid_gateway_members_only: value
        }
      });
      setChecked(value);
    }
  }))]);
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (compose([])(Blocksolid_Gateway_Plugin));
})();

/******/ })()
;
//# sourceMappingURL=block-blocksolid-gateway.js.map