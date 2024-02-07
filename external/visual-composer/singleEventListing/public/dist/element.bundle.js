/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(self["vcvWebpackJsonp4x"] = self["vcvWebpackJsonp4x"] || []).push([["element"],{

/***/ "./singleEventListing/component.js":
/*!*****************************************!*\
  !*** ./singleEventListing/component.js ***!
  \*****************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": function() { return /* binding */ singleEventListingElement; }\n/* harmony export */ });\n/* harmony import */ var _babel_runtime_helpers_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/extends */ \"./node_modules/@babel/runtime/helpers/esm/extends.js\");\n/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ \"./node_modules/@babel/runtime/helpers/esm/classCallCheck.js\");\n/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ \"./node_modules/@babel/runtime/helpers/esm/createClass.js\");\n/* harmony import */ var _babel_runtime_helpers_get__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/get */ \"./node_modules/@babel/runtime/helpers/esm/get.js\");\n/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ \"./node_modules/@babel/runtime/helpers/esm/inherits.js\");\n/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ \"./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js\");\n/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ \"./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js\");\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! react */ \"./node_modules/react/index.js\");\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var vc_cake__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! vc-cake */ \"./node_modules/vc-cake/index.js\");\n/* harmony import */ var vc_cake__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(vc_cake__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var react_dom_server__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! react-dom/server */ \"./node_modules/react-dom/server.browser.js\");\n\n\n\n\n\n\n\nfunction _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = (0,_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_6__[\"default\"])(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = (0,_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_6__[\"default\"])(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return (0,_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(this, result); }; }\nfunction _isNativeReflectConstruct() { if (typeof Reflect === \"undefined\" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === \"function\") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }\n\n\nvar vcvAPI = vc_cake__WEBPACK_IMPORTED_MODULE_8___default().getService('api');\nvar renderProcessor = vc_cake__WEBPACK_IMPORTED_MODULE_8___default().getService('renderProcessor');\n\n\n/**\n * \n * Visual composer element for single event listing.\n *\n * @since 3.1.8\n * \n */\nvar singleEventListingElement = /*#__PURE__*/function (_vcvAPI$elementCompon) {\n  (0,_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(singleEventListingElement, _vcvAPI$elementCompon);\n  var _super = _createSuper(singleEventListingElement);\n  function singleEventListingElement(props) {\n    var _this;\n    (0,_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(this, singleEventListingElement);\n    _this = _super.call(this, props);\n    _this.eventid = '';\n    return _this;\n  }\n  (0,_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(singleEventListingElement, [{\n    key: \"componentDidMount\",\n    value: function componentDidMount() {\n      (0,_babel_runtime_helpers_get__WEBPACK_IMPORTED_MODULE_3__[\"default\"])((0,_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_6__[\"default\"])(singleEventListingElement.prototype), \"updateShortcodeToHtml\", this).call(this, \"[event]\", this.ref);\n    }\n  }, {\n    key: \"componentDidUpdate\",\n    value: function componentDidUpdate(prevProps) {\n      var eventid = this.props.atts.eventid;\n      if (eventid && eventid !== '' && eventid !== prevProps.atts.eventid) this.eventid = 'id=' + eventid;\n      (0,_babel_runtime_helpers_get__WEBPACK_IMPORTED_MODULE_3__[\"default\"])((0,_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_6__[\"default\"])(singleEventListingElement.prototype), \"updateShortcodeToHtml\", this).call(this, \"[event \".concat(this.eventid, \"]\"), this.ref);\n    }\n  }, {\n    key: \"render\",\n    value: function render() {\n      var _this2 = this;\n      var _this$props = this.props,\n        id = _this$props.id,\n        atts = _this$props.atts,\n        editor = _this$props.editor;\n      var eventid = atts.eventid,\n        customClass = atts.customClass,\n        metaCustomId = atts.metaCustomId; // destructuring assignment for attributes from settings.json with access public\n      var textBlockClasses = 'vce-text-test-block';\n      var wrapperClasses = 'vce-text-block-test-wrapper vce';\n      var customProps = {};\n      if (typeof customClass === 'string' && customClass) {\n        textBlockClasses = textBlockClasses.concat(' ' + customClass);\n      }\n      if (metaCustomId) {\n        customProps.id = metaCustomId;\n      }\n      var doAll = this.applyDO('all');\n      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_7___default().createElement(\"div\", (0,_babel_runtime_helpers_extends__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({\n        className: textBlockClasses\n      }, editor, customProps), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_7___default().createElement(\"div\", (0,_babel_runtime_helpers_extends__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({\n        className: wrapperClasses,\n        id: 'el-' + id\n      }, doAll), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_7___default().createElement(\"div\", {\n        className: \"vcvhelper\",\n        ref: function ref(_ref) {\n          _this2.ref = _ref;\n        },\n        \"data-vcvs-html\": \"[event \".concat(this.eventid, \"]\")\n      })));\n    }\n  }]);\n  return singleEventListingElement;\n}(vcvAPI.elementComponent);\n\n\n//# sourceURL=webpack://vcwb/./singleEventListing/component.js?");

/***/ }),

/***/ "./singleEventListing/index.js":
/*!*************************************!*\
  !*** ./singleEventListing/index.js ***!
  \*************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var vc_cake__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vc-cake */ \"./node_modules/vc-cake/index.js\");\n/* harmony import */ var vc_cake__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vc_cake__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _component__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./component */ \"./singleEventListing/component.js\");\n\n\nvar vcvAddElement = vc_cake__WEBPACK_IMPORTED_MODULE_0___default().getService('cook').add;\n/**\n * \n * Add element component and json file\n *\n * @since 3.1.8\n * \n */\nvcvAddElement(__webpack_require__(/*! ./settings.json */ \"./singleEventListing/settings.json\"),\n// Component callback\nfunction (component) {\n  component.add(_component__WEBPACK_IMPORTED_MODULE_1__[\"default\"]);\n},\n// css settings // css for element\n{\n  css: false,\n  editorCss: __webpack_require__(/*! raw-loader!./editor.css */ \"./node_modules/raw-loader/index.js!./singleEventListing/editor.css\"),\n  mixins: {\n    singleEventListing: {\n      mixin: __webpack_require__(/*! raw-loader!./cssMixins/singleEventListing.pcss */ \"./node_modules/raw-loader/index.js!./singleEventListing/cssMixins/singleEventListing.pcss\")\n    }\n  }\n});\n\n//# sourceURL=webpack://vcwb/./singleEventListing/index.js?");

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./singleEventListing/cssMixins/singleEventListing.pcss":
/*!*************************************************************************************************!*\
  !*** ./node_modules/raw-loader/index.js!./singleEventListing/cssMixins/singleEventListing.pcss ***!
  \*************************************************************************************************/
/***/ (function(module) {

eval("module.exports = \"\"\n\n//# sourceURL=webpack://vcwb/./singleEventListing/cssMixins/singleEventListing.pcss?./node_modules/raw-loader/index.js");

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./singleEventListing/editor.css":
/*!**************************************************************************!*\
  !*** ./node_modules/raw-loader/index.js!./singleEventListing/editor.css ***!
  \**************************************************************************/
/***/ (function(module) {

eval("module.exports = \".vce-element-boilerplate {\\n  min-height: 1em;\\n}\\n\"\n\n//# sourceURL=webpack://vcwb/./singleEventListing/editor.css?./node_modules/raw-loader/index.js");

/***/ }),

/***/ "./singleEventListing/settings.json":
/*!******************************************!*\
  !*** ./singleEventListing/settings.json ***!
  \******************************************/
/***/ (function(module) {

"use strict";
eval("module.exports = JSON.parse('{\"eventid\":{\"type\":\"autocomplete\",\"access\":\"public\",\"value\":\"\",\"options\":{\"label\":\"Event Id\",\"action\":\"eventId\"}},\"darkTextSkin\":{\"type\":\"toggleSmall\",\"access\":\"public\",\"value\":false},\"designOptions\":{\"type\":\"designOptions\",\"access\":\"public\",\"value\":{},\"options\":{\"label\":\"Design Options\"}},\"editFormTab1\":{\"type\":\"group\",\"access\":\"protected\",\"value\":[\"eventid\",\"metaCustomId\",\"customClass\"],\"options\":{\"label\":\"General\"}},\"metaEditFormTabs\":{\"type\":\"group\",\"access\":\"protected\",\"value\":[\"editFormTab1\",\"designOptions\"]},\"relatedTo\":{\"type\":\"group\",\"access\":\"protected\",\"value\":[\"General\"]},\"metaOrder\":{\"type\":\"number\",\"access\":\"protected\",\"value\":3},\"customClass\":{\"type\":\"string\",\"access\":\"public\",\"value\":\"\",\"options\":{\"label\":\"Extra class name\",\"description\":\"Add an extra class name to the element and refer to it from Custom CSS option.\"}},\"metaCustomId\":{\"type\":\"customId\",\"access\":\"public\",\"value\":\"\",\"options\":{\"label\":\"Element ID\",\"description\":\"Apply unique ID to element to link directly to it by using #your_id (for element ID use lowercase input only).\"}},\"tag\":{\"access\":\"protected\",\"type\":\"string\",\"value\":\"singleEventListing\"}}');\n\n//# sourceURL=webpack://vcwb/./singleEventListing/settings.json?");

/***/ })

},[['./singleEventListing/index.js']]]);