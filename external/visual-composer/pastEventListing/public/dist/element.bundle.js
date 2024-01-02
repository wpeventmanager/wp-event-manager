/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(self["vcvWebpackJsonp4x"] = self["vcvWebpackJsonp4x"] || []).push([["element"],{

/***/ "./pastEventListing/component.js":
/*!***************************************!*\
  !*** ./pastEventListing/component.js ***!
  \***************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": function() { return /* binding */ pastEventListingElement; }\n/* harmony export */ });\n/* harmony import */ var _babel_runtime_helpers_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/extends */ \"./node_modules/@babel/runtime/helpers/esm/extends.js\");\n/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ \"./node_modules/@babel/runtime/helpers/esm/classCallCheck.js\");\n/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ \"./node_modules/@babel/runtime/helpers/esm/createClass.js\");\n/* harmony import */ var _babel_runtime_helpers_get__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/get */ \"./node_modules/@babel/runtime/helpers/esm/get.js\");\n/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ \"./node_modules/@babel/runtime/helpers/esm/inherits.js\");\n/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ \"./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js\");\n/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ \"./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js\");\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! react */ \"./node_modules/react/index.js\");\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var vc_cake__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! vc-cake */ \"./node_modules/vc-cake/index.js\");\n/* harmony import */ var vc_cake__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(vc_cake__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var react_dom_server__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! react-dom/server */ \"./node_modules/react-dom/server.browser.js\");\n\n\n\n\n\n\n\nfunction _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = (0,_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_6__[\"default\"])(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = (0,_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_6__[\"default\"])(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return (0,_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(this, result); }; }\nfunction _isNativeReflectConstruct() { if (typeof Reflect === \"undefined\" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === \"function\") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }\n\n\nvar vcvAPI = vc_cake__WEBPACK_IMPORTED_MODULE_8___default().getService('api');\nvar renderProcessor = vc_cake__WEBPACK_IMPORTED_MODULE_8___default().getService('renderProcessor');\n\n\n/**\n * \n * Visual composer element for all past event listing.\n *\n * @since 3.1.8\n * \n */\nvar pastEventListingElement = /*#__PURE__*/function (_vcvAPI$elementCompon) {\n  (0,_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(pastEventListingElement, _vcvAPI$elementCompon);\n  var _super = _createSuper(pastEventListingElement);\n  function pastEventListingElement(props) {\n    (0,_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(this, pastEventListingElement);\n    return _super.call(this, props);\n  }\n  (0,_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(pastEventListingElement, [{\n    key: \"componentDidMount\",\n    value: function componentDidMount() {\n      (0,_babel_runtime_helpers_get__WEBPACK_IMPORTED_MODULE_3__[\"default\"])((0,_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_6__[\"default\"])(pastEventListingElement.prototype), \"updateShortcodeToHtml\", this).call(this, \"[past_events]\", this.ref);\n    }\n  }, {\n    key: \"componentDidUpdate\",\n    value: function componentDidUpdate(prevProps) {\n      (0,_babel_runtime_helpers_get__WEBPACK_IMPORTED_MODULE_3__[\"default\"])((0,_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_6__[\"default\"])(pastEventListingElement.prototype), \"updateShortcodeToHtml\", this).call(this, \"[past_events]\", this.ref);\n    }\n  }, {\n    key: \"render\",\n    value: function render() {\n      var _this = this;\n      var _this$props = this.props,\n        id = _this$props.id,\n        atts = _this$props.atts,\n        editor = _this$props.editor;\n      var customClass = atts.customClass,\n        metaCustomId = atts.metaCustomId; // destructuring assignment for attributes from settings.json with access public\n      var textBlockClasses = 'vce-text-test-block';\n      var wrapperClasses = 'vce-text-block-test-wrapper vce';\n      var customProps = {};\n      if (typeof customClass === 'string' && customClass) {\n        textBlockClasses = textBlockClasses.concat(' ' + customClass);\n      }\n      if (metaCustomId) {\n        customProps.id = metaCustomId;\n      }\n      var doAll = this.applyDO('all');\n      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_7___default().createElement(\"div\", (0,_babel_runtime_helpers_extends__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({\n        className: textBlockClasses\n      }, editor, customProps), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_7___default().createElement(\"div\", (0,_babel_runtime_helpers_extends__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({\n        className: wrapperClasses,\n        id: 'el-' + id\n      }, doAll), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_7___default().createElement(\"div\", {\n        className: \"vcvhelper\",\n        ref: function ref(_ref) {\n          _this.ref = _ref;\n        },\n        \"data-vcvs-html\": \"[past_events]\"\n      })));\n    }\n  }]);\n  return pastEventListingElement;\n}(vcvAPI.elementComponent);\n\n\n//# sourceURL=webpack://vcwb/./pastEventListing/component.js?");

/***/ }),

/***/ "./pastEventListing/index.js":
/*!***********************************!*\
  !*** ./pastEventListing/index.js ***!
  \***********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var vc_cake__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vc-cake */ \"./node_modules/vc-cake/index.js\");\n/* harmony import */ var vc_cake__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vc_cake__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _component__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./component */ \"./pastEventListing/component.js\");\n/* eslint-disable import/no-webpack-loader-syntax */\n\n\nvar vcvAddElement = (0,vc_cake__WEBPACK_IMPORTED_MODULE_0__.getService)('cook').add;\nvcvAddElement(__webpack_require__(/*! ./settings.json */ \"./pastEventListing/settings.json\"),\n// Component callback\nfunction (component) {\n  component.add(_component__WEBPACK_IMPORTED_MODULE_1__[\"default\"]);\n},\n// css settings // css for element\n{\n  css: false,\n  editorCss: __webpack_require__(/*! raw-loader!./editor.css */ \"./node_modules/raw-loader/index.js!./pastEventListing/editor.css\"),\n  mixins: {\n    boilerplateColorMixin: {\n      mixin: __webpack_require__(/*! raw-loader!./cssMixins/boilerplateColorMixin.pcss */ \"./node_modules/raw-loader/index.js!./pastEventListing/cssMixins/boilerplateColorMixin.pcss\")\n    }\n  }\n});\n\n//# sourceURL=webpack://vcwb/./pastEventListing/index.js?");

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./pastEventListing/cssMixins/boilerplateColorMixin.pcss":
/*!**************************************************************************************************!*\
  !*** ./node_modules/raw-loader/index.js!./pastEventListing/cssMixins/boilerplateColorMixin.pcss ***!
  \**************************************************************************************************/
/***/ (function(module) {

eval("module.exports = \".vce-element-boilerplate--color-$selector {\\n  color: $color;\\n  &:hover {\\n    color: color($color shade(10%));\\n  }\\n}\"\n\n//# sourceURL=webpack://vcwb/./pastEventListing/cssMixins/boilerplateColorMixin.pcss?./node_modules/raw-loader/index.js");

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./pastEventListing/editor.css":
/*!************************************************************************!*\
  !*** ./node_modules/raw-loader/index.js!./pastEventListing/editor.css ***!
  \************************************************************************/
/***/ (function(module) {

eval("module.exports = \".vce-element-boilerplate {\\n  min-height: 1em;\\n}\\n\"\n\n//# sourceURL=webpack://vcwb/./pastEventListing/editor.css?./node_modules/raw-loader/index.js");

/***/ }),

/***/ "./pastEventListing/settings.json":
/*!****************************************!*\
  !*** ./pastEventListing/settings.json ***!
  \****************************************/
/***/ (function(module) {

"use strict";
eval("module.exports = JSON.parse('{\"darkTextSkin\":{\"type\":\"toggleSmall\",\"access\":\"public\",\"value\":false},\"designOptions\":{\"type\":\"designOptions\",\"access\":\"public\",\"value\":{},\"options\":{\"label\":\"Design Options\"}},\"editFormTab1\":{\"type\":\"group\",\"access\":\"protected\",\"value\":[\"metaCustomId\",\"customClass\"],\"options\":{\"label\":\"General\"}},\"metaEditFormTabs\":{\"type\":\"group\",\"access\":\"protected\",\"value\":[\"editFormTab1\",\"designOptions\"]},\"relatedTo\":{\"type\":\"group\",\"access\":\"protected\",\"value\":[\"General\"]},\"metaOrder\":{\"type\":\"number\",\"access\":\"protected\",\"value\":3},\"customClass\":{\"type\":\"string\",\"access\":\"public\",\"value\":\"\",\"options\":{\"label\":\"Extra class name\",\"description\":\"Add an extra class name to the element and refer to it from Custom CSS option.\"}},\"metaCustomId\":{\"type\":\"customId\",\"access\":\"public\",\"value\":\"\",\"options\":{\"label\":\"Element ID\",\"description\":\"Apply unique ID to element to link directly to it by using #your_id (for element ID use lowercase input only).\"}},\"tag\":{\"access\":\"protected\",\"type\":\"string\",\"value\":\"pastEventListing\"}}');\n\n//# sourceURL=webpack://vcwb/./pastEventListing/settings.json?");

/***/ })

},[['./pastEventListing/index.js']]]);