/*!
 * Foldable v1.0.0
 * Foldable. The full stack CSS3 powered jQuery Accordion.
 * 
 * http://seresinertes.com
 * Copyright 2017 seresinertes
 */

(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory(require("jquery"));
	else if(typeof define === 'function' && define.amd)
		define("Foldable", ["jquery"], factory);
	else if(typeof exports === 'object')
		exports["Foldable"] = factory(require("jquery"));
	else
		root["Foldable"] = factory(root["$"]);
})(this, function(__WEBPACK_EXTERNAL_MODULE_6__) {
return /******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;
/******/
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// identity function for calling harmony imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 7);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});
/**
 * Constant common vars used in plugin modules
 *
 * Except DEFAULTS constant, you must not update any of the remaining vars.
 * 
 */
var TRANSITION_END_EVENT = exports.TRANSITION_END_EVENT = 'transitionend webkitTransitionEnd oTransitionEnd otransitionend';
var ANIMATION_END_EVENT = exports.ANIMATION_END_EVENT = 'animationend webkitAnimationEnd oAnimationEnd oanimationend';

var JQUERY_PLUGIN_NAME = exports.JQUERY_PLUGIN_NAME = 'foldable';
var JQUERY_DATA_NAME = exports.JQUERY_DATA_NAME = 'foldable-instance';

var DATA_GROUPS = exports.DATA_GROUPS = 'foldable-group-instance';;
var TRANSITION_MIN_TIME = exports.TRANSITION_MIN_TIME = 0.001;;

/**
 * Default plugin options
 * To know more about options, please check the documentation
 * @type {Object}
 */
var DEFAULTS = exports.DEFAULTS = {
	/**
  * global id for the accordion
  * @type {String}
  */
	id: null, // string
	/**
  * a group to be opened on init
  * @type {Null || int || CSS selector || jQuery element}
  */
	initActive: null,
	/**
  * set a built-in or custom theme to the accordion
  * @type {String}
  */
	theme: 'default',
	/**
  * Sets the groups of the accordion
  * @type {CSS selector || jQuery element}
  */
	groups: '[data-foldable-role="group"]',
	/**
  * Sets the triggers of the accordion
  * @type {CSS selector || jQuery element}
  */
	triggers: '[data-foldable-role="trigger"]',
	/**
  * Sets the targets of the accordion
  * @type {CSS selector || jQuery element}
  */
	targets: '[data-foldable-role="target"]',
	/**
  * Action event which the groups will be toggled.
  * Could be 'click' or 'hover'
  * @type {String}
  */
	triggerEvent: 'click',
	/**
  * Sets the functionality to make that the groups open individually or one-by-one
  * @type {Boolean}
  */
	accordion: true,
	/**
  * Allow or prevent the use of URL hashes
  * @type {Boolean}
  */
	hash: false,
	/**
  * When closing a group, allow or prevent to auto-close its children groups
  * @type {Boolean}
  */
	autoCloseChildren: true,
	/**
  * By default use CSS3 transitions & animations. Set this to true to force to use jQuery animations
  * @type {Boolean}
  */
	forceJqueryAnimations: false,
	/**
  * Toggling groups transition options
  * @type {Object}
  */
	transition: {
		/**
   * Transition duration in milliseconds.
   * You can set null or 0 to disable the transition
   * @type {Number || Null}
   */
		duration: 300,
		/**
   * Transition easing.
   * When using CSS transitions could be 'ease', 'ease-in', 'ease-out' or 'cubic-bezier()'
   * When using jQuery animation you can use any of built-in easings or also use jQuery easing plugin
   * @type {String}
   */
		easing: 'ease'
	},
	/**
  * Toggling groups animation options
  * @type {Object}
  */
	animation: {
		/**
   * Opening animation class.
   * You can use any of built-in plugin animiations classes or use any animation library like animate.css :)
   * @type {String}
   */
		enter: null,
		/**
   * Closing animation class
   * @type {String}
   */
		leave: null,
		/**
   * Animation duration in milliseconds
   * You can set null or 0 to disable the transition
   * @type {Number || Null}
   */
		duration: 300
	},
	/**
  * Auto-toggling intervally groups' options
  * @type {Object}
  */
	autoplay: {
		/**
   * Interval duration in milliseconds
   * @type {Number}
   */
		interval: 0,
		/**
   * Stops the autoplay interval when an action happens (opening or closing a group manually)
   * @type {Boolean}
   */
		stopOnEvent: true,
		/**
   * Restarts the autoplay with the first group when the last is opened
   * @type {Boolean}
   */
		loop: true
	},
	/**
  * Set of callback methods used in conjunction with triggers methods
  */
	onBeforeOpen: function onBeforeOpen() {},
	onOpen: function onOpen() {},
	onBeforeClose: function onBeforeClose() {},
	onClose: function onClose() {},
	onBeforeAnimation: function onBeforeAnimation() {},
	onAnimation: function onAnimation() {},
	onRefresh: function onRefresh() {},
	onPlay: function onPlay() {},
	onPause: function onPause() {},
	onStop: function onStop() {},
	onEnable: function onEnable() {},
	onDisable: function onDisable() {},
	onDestroy: function onDestroy() {}
};

/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});
exports.camelize = camelize;
exports.decamelize = decamelize;
exports.supportsTransitions = supportsTransitions;
exports.debounce = debounce;
exports.rAF = rAF;
exports.isJSON = isJSON;
exports.stripHash = stripHash;
exports.removeHash = removeHash;
exports.guid = guid;
exports.is = is;
exports.isString = isString;
exports.isObject = isObject;
exports.isArray = isArray;
exports.isUndefined = isUndefined;
exports.isEmpty = isEmpty;
exports.isNull = isNull;
exports.isMobile = isMobile;
exports.isNumeric = isNumeric;
/**
 * Internal util functions used in modules
 */

function camelize(str) {
	return (str + '').replace(/-\D/g, function (match) {
		return match.charAt(1).toUpperCase();
	});
};

function decamelize(str) {
	var sep = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '-';

	if (typeof str !== 'string') {
		throw new TypeError('Expected a string');
	}

	return str.replace(/([a-z\d])([A-Z])/g, '$1' + sep + '$2').replace(/([A-Z]+)([A-Z][a-z\d]+)/g, '$1' + sep + '$2').toLowerCase();
};

function supportsTransitions() {
	var b = document.body || document.documentElement,
	    s = b.style,
	    p = 'transition';

	if (typeof s[p] == 'string') {
		return true;
	}

	var v = ['Moz', 'webkit', 'Webkit', 'Khtml', 'O', 'ms'];

	p = 'Transition';

	for (var i = 0; i < v.length; i++) {
		if (typeof s[v[i] + p] == 'string') {
			return true;
		}
	}

	return false;
};

function debounce(func, threshold, execAsap) {
	var timeout;

	return function debounced() {
		var obj = this,
		    args = arguments;

		function delayed() {
			if (!execAsap) func.apply(obj, args);
			timeout = null;
		}

		if (timeout) clearTimeout(timeout);else if (execAsap) func.apply(obj, args);

		timeout = setTimeout(delayed, threshold || 100);
	};
};

function rAF(cb) {
	if (window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame) {
		return requestAnimationFrame(cb) || webkitRequestAnimationFrame(cb) || mozRequestAnimationFrame(cb);
	} else {
		return setTimeout(cb, 1000 / 60);
	}
};

function isJSON(str) {
	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}

	return true;
};

function stripHash(hash) {
	return !!hash && (hash.indexOf('#') !== -1 ? hash.substr(1, hash.length) : false);
};

function removeHash() {
	try {

		var scrollV = void 0,
		    scrollH = void 0,
		    loc = window.location;

		if ('pushState' in history) {
			history.pushState('', document.title, loc.pathname + loc.search);
		} else {
			scrollV = document.body.scrollTop;
			scrollH = document.body.scrollLeft;

			loc.hash = '';

			document.body.scrollTop = scrollV;
			document.body.scrollLeft = scrollH;
		}
	} catch (e) {
		throw new Error("There were an error removing a hash from location");
	}
};

function guid() {
	var s4 = function s4() {
		return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
	};
	return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
};

function is(type, obj) {

	if (type.toLowerCase() === 'string') {
		return !!obj && Object.prototype.toString.call(obj) === '[object String]';
	} else if (type.toLowerCase() === 'object') {
		return !!obj && Object.prototype.toString.call(obj) === '[object Object]';
	} else if (type.toLowerCase() === 'array') {
		return !!obj && Object.prototype.toString.call(obj) === '[object Array]';
	} else if (type.toLowerCase() === 'undefined') {
		return typeof obj === 'undefined';
	} else if (type.toLowerCase() === 'empty') {
		return size(obj) === 0;
	} else if (type.toLowerCase() === 'null') {
		return obj === null;
	} else if (type.toLowerCase() === 'mobile') {
		return typeof window.orientation !== "undefined" || navigator.userAgent.indexOf('IEMobile') !== -1;
	}
};

function isString(obj) {
	return is('string', obj);
};
function isObject(obj) {
	return is('object', obj);
};
function isArray(obj) {
	return is('array', obj);
};
function isUndefined(obj) {
	return is('undefined', obj);
};
function isEmpty(obj) {
	return is('empty', obj);
};
function isNull(obj) {
	return is('null', obj);
};
function isMobile(obj) {
	return is('mobile', obj);
};
function isNumeric(obj) {
	return !isArray(obj) && obj - parseFloat(obj) + 1 >= 0;
};

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _jquery = __webpack_require__(6);

var _jquery2 = _interopRequireDefault(_jquery);

var _foldable = __webpack_require__(5);

var _foldable2 = _interopRequireDefault(_foldable);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Main file used in compilation builds to generate the production plugin
 */
exports.default = _foldable2.default;
module.exports = exports['default'];

/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * Emitter class.
 *
 * A tiny implementation of the pub/sub functionality created for Foldable plugin.
 * 
 */
var Emitter = function () {

	/**
  * Set up the instance and creates initial vars
  * @param  {Object} args
  * @return {void}
  */
	function Emitter() {
		var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

		_classCallCheck(this, Emitter);

		this._events = Object.create(null);
	}

	/**
  * Returns an array of names splitted by a space
  * @param  {String} name
  * @return {Array}
  */


	_createClass(Emitter, [{
		key: '_getNames',
		value: function _getNames(name) {
			return (/\s/g.test(name) ? name.split(' ') : [name]
			);
		}

		/**
   * Throws an error specifying the method an error ocurred
   * @param  {String} method
   * @param  {String} error
   * @return {void}
   */

	}, {
		key: 'throwError',
		value: function throwError(method, error) {
			throw new Error('[Emitter Error]: ' + method + '() >>> ' + error);
		}

		/**
   * Listen to a specific event based on a event name
   * 
   * If '*' is setted, it will listen to all handler
   * 
   * It's possible to pass multiple names at time separated with a space to listen to each event
   * Ex: on('foo bar hey ho') // will listen to this 4 events
   * 
   * @param  {String} name
   * @param  {Function} handler
   * @return {Emitter prototype}
   */

	}, {
		key: 'on',
		value: function on(name, handler) {
			var _this = this;

			if (!name) return this.throwError('on', 'At least one event name is obligatory');

			var names = this._getNames(name);

			if (names[0] === '*' && names.length > 1) return this.throwError('on', 'Wildcard listener only can be used alone');

			names.forEach(function (name) {
				_this._events[name] = _this._events[name] || [];
				handler && _this._events[name].push(handler);
			});

			return this;
		}

		/**
   * Listen to a specific event based on a event name only once at time
   *
   * If '*' is setted, it will listen to every handler once
   * 
   * @param  {String} name
   * @param  {Function} handler
   * @return {Emitter prototype}
   */

	}, {
		key: 'once',
		value: function once(name, handler) {
			handler._once = true;
			this.on(name, handler);
			return this;
		}

		/**
   * Removes a listener based on a event name
   *
   * If name param is not given, it will delete all handlers
   *
   * Pass multiple events names separated with a space to remove each one at time
   *
   * @param  {String} name
   * @param  {Function} handler
   * @return {Emitter prototype}
   */

	}, {
		key: 'off',
		value: function off(name) {
			var _this2 = this;

			var handler = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

			if (name) {
				var names = this._getNames(name);
				names.forEach(function (name) {
					handler ? _this2._events[name].splice(_this2._events[name].indexOf(handler), 1) : delete _this2._events[name];
				});
			} else {
				this._events = Object.create(null);
			}

			return this;
		}

		/**
   * Run each handler based on each name.
   *
   * @param  {String}    name
   * @param  {arguments} args
   * @return {Emitter prototype}
   */

	}, {
		key: 'emit',
		value: function emit(name) {
			var _this3 = this;

			for (var _len = arguments.length, args = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
				args[_key - 1] = arguments[_key];
			}

			if (!name) name = '*';

			var names = this._getNames(name);
			var handlers = null;

			var emitHandler = function emitHandler(collection, key) {
				collection.forEach(function (handler) {
					handler._once && _this3.off(key, handler);
					handler.call.apply(handler, [_this3].concat(args));
				});
			};

			names.forEach(function (_name) {

				args.push(_name);

				_this3._events[_name] && emitHandler(_this3._events[_name].slice(), _name);
				_this3._events['*'] && emitHandler(_this3._events['*'].slice(), '*');
			});

			return this;
		}
	}]);

	return Emitter;
}();

exports.default = Emitter;
;
module.exports = exports['default'];

/***/ }),
/* 4 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }(); // import required constants and modules


var _utils = __webpack_require__(1);

var utils = _interopRequireWildcard(_utils);

var _constants = __webpack_require__(0);

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key]; } } newObj.default = obj; return newObj; } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * This class assigns the required functionality to each group inside of an accordion
 * Please, don't use it directly as an instance.
 * 
 */
var FoldableGroup = function () {

	/**
  * Setting up the class passing the args as options (stored in this.options)
  * @return {void}
  */
	function FoldableGroup() {
		var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

		_classCallCheck(this, FoldableGroup);

		this.options = args;

		this.$el = this.options.el;
		this.$group = this.options.group;
		this.$trigger = this.options.trigger;
		this.$target = this.options.target;
		this.$animation = null;

		this.siblings = null;
		this.height = 0;
		this.hashFragment = this.$trigger.data('foldable-hash');
		this.root = this.options.root;
		this.index = this.options.index;
		this.level = this.options.level;
		this.hasTarget = this.options.hasTarget;
		this.parent = this.options.parent || null;

		this.isActive = false;

		if (this.options.hash && this.$trigger.attr('href') && /^\#(.+)/g.test(this.$trigger.attr('href'))) {
			this.hashFragment = this.$trigger.attr('href').replace(/^\#/g, '');
		}

		this._activate();
	}

	/**
  * Set up and build the accordion group structure. Also, it adds classes and sets important vars.
  * @return {void}
  */


	_createClass(FoldableGroup, [{
		key: '_activate',
		value: function _activate() {

			if (this.isLink()) return;

			this.$group.attr('data-foldable-role', 'group').addClass('foldable--level-' + this.level + ' ' + (this.isActive ? '' : 'foldable--is-closed') + ' ' + (this.hasTarget ? 'foldable--has-target' : '') + ' ' + (!this.options.isGrandChild ? 'foldable--has-children' : 'foldable--is-grandchild') + ' ' + (this.options.isFirst ? 'foldable--is-first' : '') + ' ' + (this.options.isLast ? 'foldable--is-last' : ''));
			this.$trigger.attr('data-foldable-role', 'trigger');
			this.$target.attr('data-foldable-role', 'target');
			var $targetChildren = this.$target.children();

			if (!this.$target.find('> [data-foldable-role="animation"]').length) {
				var $animation = $('<div data-foldable-role="animation" />');
				$animation.appendTo(this.$target.empty()).append($targetChildren);
			}

			this.$animation = this.$target.find('> [data-foldable-role="animation"]');

			if (utils.isMobile()) {
				this.$trigger.css({ transition: 'all 0s' });
			}

			this.bind();
		}

		/**
   * Triggers a passed event for the following type of listeners:
   * - jQuery event
   * - Built-in emitter
   * - Options callback
   * 
   * @param  {string} event
   * @param  {*} data [Passed data to the listener]
   * @return {void}      
   */

	}, {
		key: '_trigger',
		value: function _trigger(event) {
			this.$el.trigger(event + '.foldable', this);
			this.root.emit(event, this);

			//camelize event and look for the callback event
			var camelized = utils.camelize('on-' + event);

			if (!!camelized && camelized in this.options) {
				this.options[camelized].call(this, this);
			}
		}

		/**
   * Manage URL hash when opening this group
   * @return {void}
   */

	}, {
		key: '_updateHashOpening',
		value: function _updateHashOpening() {
			this.hashFragment ? this.root.updateHash(this.hashFragment) : this._updateHashClosing();
		}

		/**
   * Manage URL hash when closing this group
   * @return {void}
   */

	}, {
		key: '_updateHashClosing',
		value: function _updateHashClosing() {
			this.parent && this.parent.isActive ? this.parent.hashFragment && this.parent.root.updateHash(this.parent.hashFragment) : utils.removeHash();
		}

		/*_trigger(event) {
  	this.$el.trigger(`${event}.foldable`, this);
  	this.root.emit(event, this);
  		//camelize event and look for the callback event
  	const camelized = utils.camelize(`on-${event}`);
  		if (!!camelized && camelized in this.options) {
  		this.options[camelized].call(this, this);
  	}
  }*/

		/**
   * Rebuild the group. Used in conjunction with Foldable refresh method
   * @return {this}
   */

	}, {
		key: 'refresh',
		value: function refresh() {
			var _this = this;

			if (this.isLink()) return;

			this.unbind();
			this._activate();

			var t = setTimeout(function () {
				clearTimeout(t);

				if (_this.isActive) {
					_this.isActive = false;
					_this.toggle(false);
				}
			}, 10);

			return this;
		}

		/**
   * Manage the toggling animation of the group's target.
   * @param  {String} method
   * @return {void}
   */

	}, {
		key: 'toggleAnimation',
		value: function toggleAnimation() {
			var _this2 = this;

			var method = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'open';


			if (!this.$animation) return;

			var animation = method === 'open' ? this.options.animation.enter : this.options.animation.leave;
			var animationDuration = this.options.animation.duration;

			if (animation) {
				this._trigger('before-animation');

				if (!utils.isNull(animationDuration)) {

					var duration = Math.max(animationDuration, this.options.transition.duration);

					this.$animation.css({
						animationDuration: duration + 'ms'
					});
				} else {
					this.$animation.css({
						animationDuration: this.options.transition.duration + 'ms'
					});
				}

				this.$animation.removeClass(animation + ' foldable--is-animating').off(_constants.ANIMATION_END_EVENT).one(_constants.ANIMATION_END_EVENT, function (e) {
					_this2.$animation.off(_constants.ANIMATION_END_EVENT).css({ animationDuration: '' }).delay(10).removeClass(animation + ' foldable--is-animating');

					_this2._trigger('animation');
				}).removeClass(method === 'open' ? this.options.animation.leave : this.options.animation.enter).addClass(animation + ' foldable--is-animating');
			}
		}

		/**
   * Refresh the heights of this group.
   *
   * @return {void}
   */

	}, {
		key: 'updateHeights',
		value: function updateHeights() {
			var height = 0;

			this.$animation.children().each(function (i, child) {
				height += $(child).outerHeight(true);
			});

			this.height = Math.ceil(height);
		}

		/**
   * Manage the opening and closing group state
   * @param  {Boolean} animation [Open or close the group with transition or not]
   * @return {void}
   */

	}, {
		key: 'toggle',
		value: function toggle() {
			var animation = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;


			if (this.isActive) {
				this.close(animation);

				if (this.options.hash) {
					this._updateHashClosing();
				}
			} else {
				this.open(animation);

				if (this.options.hash) {
					this._updateHashOpening();
				}
			}

			this._trigger('toggle');
		}

		/**
   * Bind the event listener to the trigger to handle the toggling actions
   * @return {void}
   */

	}, {
		key: 'bind',
		value: function bind() {
			var _this3 = this;

			this.$trigger.off('click.foldable mouseenter.foldable').on(this.options.triggerEvent + '.foldable', function (e) {

				if (_this3.hasTarget) {
					e.preventDefault();
				}

				_this3.toggle();

				_this3.$el.trigger('action', _this3);
			});
		}
	}, {
		key: 'isLink',
		value: function isLink() {
			return this.$trigger.filter('[data-foldable-link]').length || this.$trigger.hasClass('foldable--is-link');
		}

		/**
   * Returns prepared time and easing for transitions
   * @param  {Boolean} animation
   * @return {void}
   */

	}, {
		key: 'getTimeAndEasing',
		value: function getTimeAndEasing() {
			var animation = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;

			var time = animation ? Math.max(this.options.animation.duration, this.options.transition.duration) : _constants.TRANSITION_MIN_TIME;
			var easing = animation ? this.options.transition.easing : 'ease';

			if (!parseInt(this.options.animation.duration, 10) || !parseInt(this.options.transition.duration, 10)) {
				time = _constants.TRANSITION_MIN_TIME;
			}

			return { time: time, easing: easing };
		}

		/**
   * Open this group and manage siblings and nested groups to be opened or closed
   * @param  {Boolean} animation
   * @return {void}
   */

	}, {
		key: 'open',
		value: function open() {
			var _this4 = this;

			var animation = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;


			if (this.isActive) return;
			if (this.isLink()) return;

			this._trigger('before-open');

			this.isActive = true;

			this.$group.removeClass('foldable--is-closed foldable--is-closing foldable--is-opened foldable--is-opening');

			if (this.options.accordion) this.closeSiblings();

			if (this.options.accordion) this.root.$groups.not('foldable--is-active').removeClass('foldable--is-current');

			this.$group.add(this.$trigger).add(this.$target).addClass('foldable--is-active');

			this.$group.addClass('foldable--is-opening foldable--is-current');

			this.updateHeights();

			var _getTimeAndEasing = this.getTimeAndEasing(animation),
			    time = _getTimeAndEasing.time,
			    easing = _getTimeAndEasing.easing;

			var onSuccess = function onSuccess() {

				_this4.$group.addClass('foldable--is-opened').removeClass('foldable--is-opening');

				_this4._trigger('open');
			};

			if (!this.root.forceJqueryAnimations) {
				this.updateAncestorsHeights('+');

				this.$target.off(_constants.TRANSITION_END_EVENT).one(_constants.TRANSITION_END_EVENT, onSuccess).css({ transition: 'all ' + time + 'ms ' + easing });

				utils.rAF(function () {
					return _this4.$target.css({ maxHeight: _this4.height });
				});

				time > _constants.TRANSITION_MIN_TIME && !!animation && !utils.isMobile() && this.toggleAnimation('open');
			} else {
				if (animation === false || time === 0) {
					easing = '';
				} else {
					easing = !$.easing ? 'swing' : $.easing.hasOwnProperty(easing) ? easing : 'swing';
				}
				this.$target.slideDown(time, easing, onSuccess);
			}
		}

		/**
   * Close this group and manage siblings and nested groups to be opened or closed
   * @param  {Boolean} animation
   * @param  {Boolean} isFromClosingChildren [internal flag to know when the method is called from children groups]
   * @return {void}
   */

	}, {
		key: 'close',
		value: function close() {
			var _this5 = this;

			var animation = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
			var isFromClosingChildren = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;


			if (!this.isActive) return;
			if (this.isLink()) return;

			this._trigger('before-close');

			this.isActive = false;

			this.$group.removeClass('foldable--is-opened foldable--is-opening foldable--is-closed foldable--is-closing foldable--is-current').addClass('foldable--is-closing');

			this.$group.add(this.$trigger).add(this.$target).removeClass('foldable--is-active');

			var $parents = this.$group.parents(this.options.groups);
			var $children = this.$group.find(this.options.groups);

			if (!isFromClosingChildren && $parents && $parents.length) {
				var $parent = $parents.eq(0);

				if (!$parent.hasClass('foldable--is-current')) {
					$parent.addClass('foldable--is-current');
				}
			}

			if ($children && $children.length) {
				$children.removeClass('foldable--is-current');
			}

			this.updateHeights();

			if (!!this.options.autoCloseChildren) this.closeChildren();

			var _getTimeAndEasing2 = this.getTimeAndEasing(animation),
			    time = _getTimeAndEasing2.time,
			    easing = _getTimeAndEasing2.easing;

			var onSuccess = function onSuccess() {

				_this5.$group.addClass('foldable--is-closed').removeClass('foldable--is-closing');

				_this5._trigger('close');
			};

			if (!this.root.forceJqueryAnimations) {
				this.$target.off(_constants.TRANSITION_END_EVENT).one(_constants.TRANSITION_END_EVENT, onSuccess);

				utils.rAF(function () {
					return _this5.$target.css({
						transition: 'all ' + time + 'ms ' + easing,
						maxHeight: 0,
						overflow: 'hidden'
					});
				});

				this.updateAncestorsHeights('-');

				time > _constants.TRANSITION_MIN_TIME && !!animation && !isFromClosingChildren && !utils.isMobile() && this.toggleAnimation('close');
			} else {
				if (animation === false || time === 0) {
					easing = '';
				} else {
					easing = !$.easing ? 'swing' : $.easing.hasOwnProperty(easing) ? easing : 'swing';
				}
				this.$target.slideUp(time, easing, onSuccess);
			}
		}

		/**
   * Refresh ancestors groups' heights.
   *
   * Used when toggling a nested group
   * 
   * @param  {String} operation ['+' or '-']
   * @return {void}
   */

	}, {
		key: 'updateAncestorsHeights',
		value: function updateAncestorsHeights() {
			var _this6 = this;

			var operation = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '+';


			var ancestors = this.getAncestors();

			if (ancestors && ancestors.length) {
				ancestors.forEach(function (ancestor) {
					if (operation === '+') {
						ancestor.height += _this6.height;
					} else if (operation === '-') {
						ancestor.height -= _this6.height;
					} else {
						throw new Error('To update ancestors is required an operation');
					}

					utils.rAF(function () {
						return ancestor.$target.css({ maxHeight: ancestor.height });
					});
				});
			}
		}

		/**
   * Close nested children groups
   * @param  {Boolean} animation
   * @return {void}
   */

	}, {
		key: 'closeChildren',
		value: function closeChildren() {
			var animation = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;

			var $children = this.$group.find(this.options.groups);

			if ($children.length) {
				$children.each(function (i, el) {
					var item = $(el).data('foldable-group-instance');
					if (item && item.isActive) {
						item.close(animation, true);
					}
				});
			}
		}

		/**
   * Returns siblings groups of this instance group (excluding itself)
   * @return {Array}
   */

	}, {
		key: 'getSiblings',
		value: function getSiblings() {
			var _this7 = this;

			if (!this.siblings) return;
			return this.siblings.filter(function (sibling) {
				return sibling !== _this7;
			});
		}

		/**
   * Close siblings groups
   * @param  {Boolean} animation
   * @return {void}
   */

	}, {
		key: 'closeSiblings',
		value: function closeSiblings() {
			var animation = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;

			if (!this.siblings) return;

			var siblings = this.getSiblings();

			if (siblings && siblings.length) {
				siblings.forEach(function (sibling) {
					return sibling.isActive && sibling.close(animation);
				});
			}
		}

		/**
   * Returns ancestors groups of this group
   * @return {Array}
   */

	}, {
		key: 'getAncestors',
		value: function getAncestors() {
			var _this8 = this;

			var ancestors = [];
			var $groups = this.$group.parents(this.options.groups);

			if ($groups && $groups.length) {
				$groups.each(function (i, el) {
					var $group = $(el);

					if ($group && $group.length) {
						var instance = $group.data('foldable-group-instance');
						instance !== _this8 && ancestors.unshift($group.data('foldable-group-instance'));
					}
				});
			}

			return ancestors;
		}

		/**
   * Unset listeners
   * @return {void}
   */

	}, {
		key: 'unbind',
		value: function unbind() {
			this.$trigger.off('click.foldable mouseenter.foldable');
			this.$group.off();
		}

		/**
   * Disable this group and close it
   * @return {void}
   */

	}, {
		key: 'disable',
		value: function disable() {
			this.close(false);
			this.unbind();
		}
	}]);

	return FoldableGroup;
}();

exports.default = FoldableGroup;
;
module.exports = exports['default'];

/***/ }),
/* 5 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _constants = __webpack_require__(0);

var _utils = __webpack_require__(1);

var utils = _interopRequireWildcard(_utils);

var _emitter = __webpack_require__(3);

var _emitter2 = _interopRequireDefault(_emitter);

var _foldableGroup = __webpack_require__(4);

var _foldableGroup2 = _interopRequireDefault(_foldableGroup);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key]; } } newObj.default = obj; return newObj; } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

/**
 * IMPORTANT NOTE!!!
 * 
 * Every method prefixed with an underscore are private and you shouldn't use it directlly from an instance of the plugin
 * 
 */

// check if it's defined jQuery in window. It's a mandatory
if (typeof jQuery === 'undefined') throw new Error('[Foldable] Foldable requires jQuery');

// import required constants and modules

var Foldable = function (_Emitter) {
	_inherits(Foldable, _Emitter);

	/**
  * Setting up the class passing the args (see _parseArgs method)
  * @return {void}
  */
	function Foldable() {
		var _ref;

		_classCallCheck(this, Foldable);

		for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
			args[_key] = arguments[_key];
		}

		var _this = _possibleConstructorReturn(this, (_ref = Foldable.__proto__ || Object.getPrototypeOf(Foldable)).call.apply(_ref, [this].concat(args)));

		_this.options = _constants.DEFAULTS;

		_this._parseArgs.apply(_this, args);
		_this.id = _this.guid = utils.guid();

		_this.$groups = utils.isString(_this.options.groups) ? _this.$el.find(_this.options.groups) : _this.options.groups;
		_this.$triggers = utils.isString(_this.options.triggers) ? _this.$el.find(_this.options.triggers) : _this.options.triggers;
		_this.$targets = utils.isString(_this.options.targets) ? _this.$el.find(_this.options.targets) : _this.options.targets;

		if (!_this.$groups.length) {
			throw new Error('[Foldable] Groups must exists in the DOM');
		}

		if (!_this.$triggers.length) {
			throw new Error('[Foldable] Triggers must exists in the DOM');
		}

		if (!_this.$targets.length) {
			throw new Error('[Foldable] Targets must exists in the DOM');
		}

		_this.parent = null;
		_this.autoplayInterval = null;
		_this.theme = _this.options.theme;
		_this.isInitialized = false;

		_this._activate();
		return _this;
	}

	/**
  * Parse and prepare constructor options to initialize the plugin
  * 
  * You can pass the options in 2 ways:
  * 1) (el, optionsObject)
  * 2) (optionsObject) where it has to include a param called "el" ==> { el:$(my-root-element) }
  *
  * After that, will be set the vars this.options and this.$el
  *
  * this.options = Object that contains all plugin options
  * this.$el = jQuery element renferencing the root element
  *
  * Also, it will check for a possible given options in the "data-foldable" attribute (setted in JSON format)
  *
  * @param  {...args}
  * @return {void}
  */


	_createClass(Foldable, [{
		key: '_parseArgs',
		value: function _parseArgs() {
			var $el = null;
			var options = {};

			for (var _len2 = arguments.length, args = Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
				args[_key2] = arguments[_key2];
			}

			if ($.isPlainObject(args[0])) {
				options = args[0];

				if ('el' in args[0]) {
					$el = $(args[0].el);
				}
			} else {
				if (args.length === 2) {
					options = args[1];
				}

				if (utils.isString(args[0])) {
					$el = $(args[0]);
				} else if (args[0] instanceof jQuery) {
					$el = $(args[0]);
				} else {
					throw new Error('[Foldable] Collapse selector not found');
				}
			}

			if (this._hasHTMLDataOptions($el)) {
				options = JSON.parse($el.attr('data-foldable'));
			}

			this.options = $.extend(true, {}, _constants.DEFAULTS, options || {});

			if (!!$el && $el.length > 0) {
				this.$el = $el;
			} else {
				throw new Error('[Foldable] Collapse element not found');
			}
		}

		/**
   * Check the root element for a data JSON options setted in "data-foldable" attribute
   * @param  {jQuery element}  $el
   * @return {Boolean}
   */

	}, {
		key: '_hasHTMLDataOptions',
		value: function _hasHTMLDataOptions($el) {
			var options = $el.attr('data-foldable');

			if (!!options) {
				if (utils.isJSON(options)) {
					return true;
				} else {
					throw new Error('[Foldable]Foldable HTML data option are not a valid JSON');
				}
			} else {
				return false;
			}
		}

		/**
   * Returns the found element given in param inside the scoop of this plugin instance
   * @param  {HTML element || jQuery element}
   * @return {jQuery element} Found jQuery wrapped element
   */

	}, {
		key: '_getElement',
		value: function _getElement(item) {

			var $item = null;

			if (utils.isNumeric(item)) {
				$item = this.$groups.eq(item);
			} else {
				$item = utils.isString(item) ? this.$el.find(item) : $(item);
			}

			if (!$item.length) throw new Error('[Foldable] Item(s) not found, [' + item + ']');

			return $item;
		}

		/**
   * Set up and build the accordion structure. Also, it adds classes to the root element and sets important vars.
   * @return {void}
   */

	}, {
		key: '_activate',
		value: function _activate() {

			this.all = [];
			this.grandchildren = [];
			this.forceJqueryAnimations = this.options.forceJqueryAnimations ? true : !utils.supportsTransitions();

			this.autoplayCurrentIndex = 0;

			this.$el.attr('data-foldable-id', this.options.id || this.guid).removeClass('foldable--theme-' + this.theme).addClass('foldable--theme-' + this.options.theme + ' ' + (!this.forceJqueryAnimations ? 'foldable--is-css-modern' : 'foldable--is-css-legacy'));

			this.theme = this.options.theme;
			this.triggerEvent = this.options.triggerEvent === 'hover' ? 'mouseenter' : 'click';

			this._build(this.$el);

			if (!this.isInitialized) {
				this._handleInitActive();

				if (this.options.hash) {
					if (utils.stripHash(window.location.hash)) {
						this._openByHash(utils.stripHash(window.location.hash));
					}
				}

				if (!!this.options.autoplay.interval) {
					this.play();
				}

				this.isInitialized = true;
			}

			this._bindings();

			this.$el.addClass('foldable--is-initialized');
		}

		/**
   * Try to open on init a group using "initActive" option or "data-foldable-active" attribute setted in a group
   * @return {void}
   */

	}, {
		key: '_handleInitActive',
		value: function _handleInitActive() {
			var _this2 = this;

			if (!utils.isNull(this.options.initActive) && !utils.isUndefined(this.options.initActive)) {
				var $actives = this._getElement(this.options.initActive);
				!!$actives && $actives.length && this.open($actives, false);
			} else {
				if (this.$el.find('[data-foldable-active]').length) {
					this.$el.find('[data-foldable-active]').each(function (i, el) {
						return _this2.open($(el), false);
					});
				}
			}
		}

		/**
   * Check the current URL hash to match with any hash setted in the triggers
   * @param  {String} hash
   * @return {void}
   */

	}, {
		key: '_openByHash',
		value: function _openByHash() {
			var hash = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : window.location.hash;


			if (hash === '') return foldable;

			var $hashed = this.$el.find('[data-foldable-hash="' + hash + '"]');

			if (!$hashed.length) $hashed = this.$el.find('[href="#' + hash + '"]');

			if (!$hashed.length) $hashed = null;

			if (!$hashed) {
				return 'console' in window && console.warn('There weren\'t found any element with ' + hash + ' hash');
			}

			this.open($hashed.last(), false);
		}

		/**
   * Sets global listeners
   * @return {void}
   */

	}, {
		key: '_bindings',
		value: function _bindings() {
			var _this3 = this;

			if (!!this.options.autoplay.interval) {
				this.$el.on('action', function (e) {
					if (_this3.options.autoplay.stopOnEvent) {
						_this3.stop(false, true);
					}
				});
			}

			$(window).on('resize.foldable', utils.debounce(function () {
				return _this3.updateHeights();
			}, 150));
		}

		/**
   * Unsets global listeners
   * @return {void}
   */

	}, {
		key: '_unbindings',
		value: function _unbindings() {
			this.$el.off('action');
			$(window).off('resize.foldable');
		}

		/**
   * Function where it creates and instance of FoldableGroup by each group found recursively.
   *
   * @param  {jQuery element} $parent
   * @param  {Number} _level
   * @return {void}
   */

	}, {
		key: '_build',
		value: function _build() {
			var _this4 = this;

			var $parent = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : this.$el;

			var _level = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;

			var $groups = $parent.children(this.options.groups);

			if (!$groups.length) return;

			var siblings = [];

			$groups.each(function (i, el) {

				var level = _level;

				var $group = $(el);
				var $trigger = $group.children(_this4.options.triggers).eq(0);
				var $target = $group.children(_this4.options.targets).eq(0);

				var options = $.extend(true, {}, _this4.options, {
					root: _this4,
					el: _this4.$el,
					group: $group,
					trigger: $trigger,
					target: $target,
					index: i,
					level: level,
					isFirst: i === 0,
					isLast: i === $groups.length - 1,
					children: [],
					triggerEvent: _this4.triggerEvent,
					hasTarget: !!$target.length,
					isGrandChild: !$target.find(_this4.options.groups).length,
					autoplayInterval: _this4.autoplayInterval
				});

				if (_this4.parent && level > 0) {
					options.parent = _this4.parent;
				}

				if (_this4.parent && level === _this4.parent.level) {
					options.parent = !!_this4.parent.parent ? _this4.parent.parent : null;
				}

				if (level === 0) {
					options.parent = null;
				}

				var foldableGroupInstance = null;

				if ($group.data(_constants.DATA_GROUPS)) {
					foldableGroupInstance = $group.data(_constants.DATA_GROUPS);
					foldableGroupInstance.options = options;
					foldableGroupInstance.refresh();
				} else {
					foldableGroupInstance = new _foldableGroup2.default(options);
					$group.data(_constants.DATA_GROUPS, foldableGroupInstance);
					$trigger.data(_constants.DATA_GROUPS, foldableGroupInstance);
					$target.data(_constants.DATA_GROUPS, foldableGroupInstance);
				}

				siblings.push(foldableGroupInstance);
				_this4.all.push(foldableGroupInstance);

				if (options.isGrandChild) _this4.grandchildren.push(foldableGroupInstance);

				if ($target.children('[data-foldable-role="animation"]').children(_this4.options.groups).length) {
					_this4.parent = foldableGroupInstance;

					level++;

					_this4._build($target.children('[data-foldable-role="animation"]'), level);
				}

				foldableGroupInstance.siblings = siblings;
			});
		}

		/**
   * Triggers a passed event for the following type of listeners:
   * - jQuery event
   * - Built-in emitter
   * - Options callback
   * 
   * @param  {string} event
   * @param  {*} data [Passed data to the listener]
   * @return {void}      
   */

	}, {
		key: '_trigger',
		value: function _trigger(event) {
			var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : this;

			this.$el.trigger(event + '.foldable', data);
			this.emit(event, this);

			//camelize event and look for te callback event
			var camelized = utils.camelize('on-' + event);

			if (!!camelized && camelized in this.options) {
				this.options[camelized].call(this, data);
			}
		}

		/**
   * Updates the this.options variable to modify the current set up
   * @param  {Object} options
   * @return {this} To chain the method
   */

	}, {
		key: 'updateOptions',
		value: function updateOptions() {
			var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};


			if (!$.isPlainObject(options)) throw new Error('[Foldable] [updateOptions]: Passed options have to be an object');

			try {
				this._unbindings();
				this.options = $.extend(true, {}, this.options, options);
				this._activate();
			} catch (e) {
				throw new Error('[Foldable] There were a problem in updateOptions method');
			}

			return this;
		}

		/**
   * Rebuild the accordion with the new given options
   * @param  {Object} options
   * @return {this}
   */

	}, {
		key: 'refresh',
		value: function refresh() {
			var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};


			if (!$.isPlainObject(options)) throw new Error('[Foldable] [refresh]: Passed options have to be an object');

			try {
				this.updateOptions(options);

				this.parent = null;
				this.all = [];
				this.$groups = this.$el.find(this.options.groups);
				this.$triggers = this.$groups.children(this.options.triggers);
				this.$targets = this.$groups.children(this.options.targets);

				this._trigger('refresh', this);
			} catch (e) {
				throw new Error('[Foldable] There were a problem in refresh method');
			}

			return this;
		}

		/**
   * Open an accordion group(s).
   *
   * You can pass the group to be opened in these ways:
   * - With the group index (when the accordion only has one level)
   * - By CSS selector
   * - jQuery element
   *
   * The second parameter sets the animation to be opened with or without transition. 
   * 
   * @param  {int || String || jQuery element}
   * @param  {Boolean} animation
   * @return {this}
   */

	}, {
		key: 'open',
		value: function open(item) {
			var animation = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

			var $item = this._getElement(item);

			try {
				(function () {
					var ancestors = null;

					$item.each(function (i, el) {
						var instance = $(el).data(_constants.DATA_GROUPS);

						if (!instance) return;

						ancestors = instance.getAncestors();

						if (ancestors && ancestors.length) {
							ancestors.forEach(function (ancestor) {
								return ancestor.open(animation);
							});
						}

						instance.open(animation);
					});
				})();
			} catch (e) {
				throw new Error('[Foldable] Item(s) to open not found, [' + item + ']');
			}

			return this;
		}

		/**
   * Close an accordion group(s).
   *
   * You can pass the group to be closed in these ways:
   * - With the group index (when the accordion only has one level)
   * - By CSS selector
   * - jQuery element
   *
   * The second parameter sets the animation to be closed with or without transition. 
   * 
   * @param  {int || String || jQuery element}
   * @param  {Boolean} animation
   * @return {this}
   */

	}, {
		key: 'close',
		value: function close(item) {
			var animation = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

			var $item = this._getElement(item);

			try {
				$item.each(function (i, el) {
					var instance = $(el).data(_constants.DATA_GROUPS);
					instance && instance.isActive && instance.close(animation);
				});
			} catch (e) {
				throw new Error('[Foldable] Item(s) to close not found, [' + item + ']');
			}

			return this;
		}

		/**
   * Toggle an accordion group(s).
   * It means that if the group passed is currently opened, it will close it and vice versa
   *
   * You can pass the group to be toggled in these ways:
   * - With the group index (when the accordion only has one level)
   * - By CSS selector
   * - jQuery element
   *
   * The second parameter sets the animation to be toggled with or without transition. 
   * 
   * @param  {int || String || jQuery element}
   * @param  {Boolean} animation
   * @return {this}
   */

	}, {
		key: 'toggle',
		value: function toggle(item) {
			var _this5 = this;

			var animation = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

			var $item = this._getElement(item);

			try {
				$item.each(function (i, el) {
					var instance = $(el).data(_constants.DATA_GROUPS);
					instance && _this5[instance.isActive ? 'close' : 'open']($(el), animation);
				});
			} catch (e) {
				throw new Error('[Foldable] Item(s) to toggle not found, [' + item + ']');
			}

			return this;
		}

		/**
   * Open all groups at time
   * @param  {Boolean} animation
   * @return {this}
   */

	}, {
		key: 'expand',
		value: function expand() {
			var animation = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;

			var children = [];

			this.grandchildren.length && this.grandchildren.map(function (grandchild) {
				return children.push(grandchild.$group[0]);
			});

			if (children.length) {

				var oldAccordionOption = this.options.accordion;

				this.updateOptions({ accordion: false });
				children.length && this.open($(children), animation);

				if (oldAccordionOption) this.updateOptions({ accordion: oldAccordionOption });

				this._trigger('expand');
			}

			return this;
		}

		/**
   * Close all groups at time
   * @param  {Boolean} animation
   * @return {this}
   */

	}, {
		key: 'collapse',
		value: function collapse() {
			var animation = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;


			this.close(this.$el.children(this.options.groups), animation);

			this._trigger('collapse');

			return this;
		}

		/**
   * Open the groups intervally with the given time in milliseconds
   *
   * El segundo parametro hace que pare los intervalos
   *
   * The second parameter stops the played interval when you make an action in the accordion
   * 
   * @param  {int} options.interval [int in milliseconds]
   * @param  {Boolean} options.stopOnEvent
   * @return {this}
   */

	}, {
		key: 'play',
		value: function play() {
			var _this6 = this;

			var _ref2 = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {},
			    _ref2$interval = _ref2.interval,
			    interval = _ref2$interval === undefined ? this.options.autoplay.interval : _ref2$interval,
			    _ref2$stopOnEvent = _ref2.stopOnEvent,
			    stopOnEvent = _ref2$stopOnEvent === undefined ? this.options.autoplay.stopOnEvent : _ref2$stopOnEvent;

			var $groups = this.$groups;
			var length = $groups.length;
			var count = this.autoplayCurrentIndex;

			this.stop();

			this.updateOptions({ autoplay: { interval: interval, stopOnEvent: stopOnEvent } });

			var open = function open() {

				if (count >= length) {
					if (_this6.options.autoplay.loop) {
						count = 0;
					} else {
						_this6.stop();
						return _this6.collapse();
					}
				}

				_this6.autoplayCurrentIndex = count;

				_this6.open($groups.eq(count));

				count++;
			};

			this.autoplayInterval = setInterval(open, this.options.autoplay.interval);

			open();

			this._trigger('play', this);

			return this;
		}

		/**
   * Pause the accordion when it have been played by the "play" method
   *
   * When the accordion is played again, it will start with the currently opened group
   * 
   * @return {this}
   */

	}, {
		key: 'pause',
		value: function pause() {
			this.stop(true);
			return this;
		}

		/**
   * Stop the accordion when it have been played by the "play" method
   *
   * When the accordion is played again, it will start with the first group
   *
   * @param  {Boolean} isPaused     [param internally used. Don't use from an instance]
   * @param  {Boolean} isFromAction [param internally used. Don't use from an instance]
   * @return {this}
   */

	}, {
		key: 'stop',
		value: function stop() {
			var isPaused = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
			var isFromAction = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

			if (!!this.autoplayInterval) {
				clearInterval(this.autoplayInterval);

				if (!isPaused) {
					this.autoplayCurrentIndex = 0;
					!isFromAction && this.collapse();
				}

				this._trigger(isPaused ? 'pause' : 'stop', this);
			}
		}

		/**
   * Refresh the heights of each group.
   *
   * Internally used in resize events
   * 
   * @return {this}
   */

	}, {
		key: 'updateHeights',
		value: function updateHeights() {
			this.$groups.each(function (i, el) {
				var $group = $(el);
				var instance = $group.data(_constants.DATA_GROUPS);

				if (!instance) return;

				if (instance.options.isGrandChild && instance.isActive) {
					instance.updateHeights();
					instance.$target.css({
						maxHeight: instance.height
					});
					instance.updateAncestorsHeights();
				}
			});

			return this;
		}

		/**
   * Restarts the accordion when it has been disabled
   * @return {this}
   */

	}, {
		key: 'enable',
		value: function enable() {
			try {
				var $groups = this.$el.find(this.options.groups);
				$groups.each(function (i, el) {
					var itemData = $(el).data(_constants.DATA_GROUPS);
					itemData.bind();
				});
				this.$el.removeClass('foldable--is-disabled');

				this._trigger('enable', this);
			} catch (e) {
				throw new Error('[Foldable] There was an error enabling a group');
			}

			return this;
		}

		/**
   * Disables the accordion, preventing any event interaction with it
   * @return {this}
   */

	}, {
		key: 'disable',
		value: function disable() {
			try {
				var $groups = this.$el.find(this.options.groups);
				$groups.each(function (i, el) {
					var itemData = $(el).data(_constants.DATA_GROUPS);
					itemData.disable();
				});

				this.$el.addClass('foldable--is-disabled');

				this._trigger('disable', this);
			} catch (e) {
				throw new Error('[Foldable] There was an error disabling a group');
			}

			return this;
		}

		/**
   * Set a new hash in the URL when a group have been toggled
   * @param  {String}  hash
   * @param  {Boolean} trigger
   * @return {this}
   */

	}, {
		key: 'updateHash',
		value: function updateHash() {
			var hash = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : undefined;

			if (!this.options.hash) return;
			if (typeof hash === 'undefined') hash = '';

			window.location.hash = hash;

			return this;
		}

		/**
   * Completely removes all classes, datas and events setted by the plugin and restores the plugin as it was at the beginning
   * @return {this}
   */

	}, {
		key: 'destroy',
		value: function destroy() {
			try {
				this.collapse(false);

				this.$el.removeClass('foldable--is-initialized foldable--is-disabled foldable--theme-' + this.options.theme).removeAttr('data-foldable-id').removeData(_constants.JQUERY_DATA_NAME);

				this.all.forEach(function (itemData) {
					var $group = itemData.$group;
					var $trigger = itemData.$trigger;
					var $target = itemData.$target;

					itemData.unbind();

					$group.off().removeClass('foldable--has-target foldable--has-children foldable--is-current foldable--is-opened foldable--is-closed foldable--is-opening foldable--is-closing foldable--is-first foldable--is-last foldable--level-' + itemData.level + ' foldable--is-active').removeAttr('data-foldable-role').removeData(_constants.DATA_GROUPS);

					$trigger.off().removeClass('foldable--is-active').removeAttr('data-foldable-role').removeData(_constants.DATA_GROUPS);

					$target.off().css({ maxHeight: '', overflow: '' }).removeClass('foldable--is-active').removeAttr('data-foldable-role').removeData(_constants.DATA_GROUPS).css('display', '').unwrap('[data-foldable-role="animation"]');
				});

				this._trigger('destroy', this);
			} catch (e) {
				throw new Error('[Foldable] There were an error destroying the accordion');
			}

			return this;
		}
	}]);

	return Foldable;
}(_emitter2.default);

exports.default = Foldable;
;

/**
 * jQuery plugin definition
 *
 * Add to the $.fn methods object a new property called Foldable to use it like a jQuery plugin
 * 
 */
(function (jQuery, exports) {
	$ = jQuery;
	function jQueryPlugin() {
		for (var _len3 = arguments.length, args = Array(_len3), _key3 = 0; _key3 < _len3; _key3++) {
			args[_key3] = arguments[_key3];
		}

		return this.each(function () {
			var $this = $(this),
			    data = $this.data(_constants.JQUERY_DATA_NAME);

			// create a new Foldable instance
			if (!data) {
				var options = $this.data('foldable-config');
				options = !!options ? options : args[0];

				$this.data(_constants.JQUERY_DATA_NAME, data = new Foldable($this, options));
			}

			// if a string is passed to the param, try to call it as a plugin method
			if (typeof args[0] === 'string') {
				var _data$method;

				var method = args[0];
				args.shift();
				(_data$method = data[method]).call.apply(_data$method, [data].concat(args));
			}
		});
	}

	var old = $.fn[_constants.JQUERY_PLUGIN_NAME];

	$.fn[_constants.JQUERY_PLUGIN_NAME] = jQueryPlugin;
	$.fn[_constants.JQUERY_PLUGIN_NAME].Constructor = Foldable;

	$.fn[_constants.JQUERY_PLUGIN_NAME].noConflict = function () {
		$.fn[_constants.JQUERY_PLUGIN_NAME] = old;
		return this;
	};

	// when de DOM is ready, try to auto create plugin instances of each element with [data-foldable]
	$(function () {
		var $foldables = $('[data-foldable]');

		if ($foldables.length) {
			$foldables.each(function (i, el) {
				var $foldable = $(el);

				if (!$foldable.data(_constants.JQUERY_DATA_NAME) && !$foldable.parents('code').length) {
					$foldable[_constants.JQUERY_PLUGIN_NAME]();
				}
			});
		}
	});
})(window.jQuery, window);
module.exports = exports['default'];

/***/ }),
/* 6 */
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE_6__;

/***/ }),
/* 7 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(2);


/***/ })
/******/ ]);
});
//# sourceMappingURL=foldable.js.map
