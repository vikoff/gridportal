/*
 * SimpleModal 1.4.1 - jQuery Plugin
 * http://www.ericmmartin.com/projects/simplemodal/
 * Copyright (c) 2010 Eric Martin (http://twitter.com/ericmmartin)
 * Dual licensed under the MIT and GPL licenses
 * Revision: $Id: jquery.simplemodal.js 261 2010-11-05 21:16:20Z emartin24 $
 */
(function ($) {
	var ie6 = $.browser.msie && parseInt($.browser.version) === 6 && typeof window['XMLHttpRequest'] !== 'object',
		ie7 = $.browser.msie && parseInt($.browser.version) === 7,
		ieQuirks = null,
		w = [],
		screenBlocked = false,
		screenScrollHandler = null;

	/*
	 * Create and display a modal dialog.
	 *
	 * @param {string, object} data A string, jQuery object or DOM object
	 * @param {object} [options] An optional object containing options overrides
	 */
	$.modal = function (data, options) {
		$.modal.impl.init(data, options);
		return $.modal;
	};

	/*
	 * Close the modal dialog.
	 */
	$.modal.close = function () {
		$.modal.impl.close();
	};

	/*
	 * Set focus on first or last visible input in the modal dialog. To focus on the last
	 * element, call $.modal.focus('last'). If no input elements are found, focus is placed
	 * on the data wrapper element.
	 */
	$.modal.focus = function (pos) {
		$.modal.impl.focus(pos);
	};

	// update position of modal window
	$.modal.updatePosition = function () {
		$.modal.impl.setContainerDimensions();
		return $.modal;
	};
	
	// set css for container
	$.modal.css = function(param1, param2){
		$('#' + this.impl.o.dataId).css(param1, param2);
		this.impl.setContainerDimensions();
		return $.modal;
	};
	
	// insert new html into modal window
	$.modal.html = function(html){
		$('#' + this.impl.o.dataId).html(html);
		this.impl.setContainerDimensions();
		return $.modal;
	};
	
	// append html into modal window
	$.modal.append = function(html){
		$('#' + this.impl.o.dataId).append(html);
		this.impl.setContainerDimensions();
		return $.modal;
	};
	
	// load content via ajax (GET)
	$.modal.load = function(url, data, loadType){
		$.modal('');
		$.get(url, data, function(response){
			$.modal.html(response);
		});
		return $.modal;
	}
	
	// prevent body scrolling
	$.modal.blockScreen = function(){
		$.modal.impl.blockScreen();
		return this;
	};
	
	// allow body scrolling
	$.modal.unblockScreen = function(){
		$.modal.impl.unblockScreen();
		return $.modal;
	};
	
	// set Esc close behavior
	$.modal.escClose = function(isClosable){
		$.modal.impl.escClose(isClosable);
		return $.modal;
	};
	
	/*
	 * Chained function to create a modal dialog.
	 *
	 * @param {object} [options] An optional object containing options overrides
	 */
	$.fn.modal = function (options) {
		return $.modal.impl.init(this, options);
	};
	$.modal.defaults = {
		appendTo: 'body',
		focus: true,
		opacity: 50,
		overlayId: 'simplemodal-overlay',
		overlayCss: {},
		containerId: 'simplemodal-container',
		containerCss: {},
		dataId: 'simplemodal-data',
		dataCss: {},
		width: null,
		height: null,
		minHeight: null,
		minWidth: null,
		maxHeight: null,
		maxWidth: null,
		autoResize: true,
		autoPosition: true,
		zIndex: 10000,
		close: true,
		closeHTML: '<a class="modalCloseImg" title="Close"></a>',
		closeClass: 'simplemodal-close',
		escClose: true,
		overlayClose: false,
		position: null,
		persist: false,
		modal: true,
		onOpen: null,
		onShow: null,
		onClose: null
	};

	/*
	 * Main modal object
	 * o = options
	 */
	$.modal.impl = {
		/*
		 * Contains the modal dialog elements and is the object passed
		 * back to the callback (onOpen, onShow, onClose) functions
		 */
		d: {},
		/*
		 * Initialize the modal dialog
		 */
		init: function (data, options) {
			var s = this;
			
			// don't allow multiple calls
			if (s.d.data) {
				s.close();
			}

			// $.boxModel is undefined if checked earlier
			ieQuirks = $.browser.msie && !$.boxModel;

			// merge defaults and user options
			s.o = $.extend({}, $.modal.defaults, options);

			// keep track of z-index
			s.zIndex = s.o.zIndex;

			// set the onClose callback flag
			s.occb = false;

			// determine how to handle the data based on its type
			if (typeof data === 'object') {
				// convert DOM object to a jQuery object
				data = data instanceof jQuery ? data : $(data);
				s.d.placeholder = false;

				// if the object came from the DOM, keep track of its parent
				if (data.parent().parent().size() > 0) {
					data.before($('<span></span>')
						.attr('id', 'simplemodal-placeholder')
						.css({display: 'none'}));

					s.d.placeholder = true;
					s.display = data.css('display');

					// persist changes? if not, make a clone of the element
					if (!s.o.persist) {
						s.d.orig = data.clone(true);
					}
				}
			}
			else if (typeof data === 'string' || typeof data === 'number') {
				// just insert the data as innerHTML
				data = $('<div></div>').html(data);
			}
			else {
				// unsupported data type!
				alert('SimpleModal Error: Unsupported data type: ' + typeof data);
				return s;
			}

			// create the modal overlay, container and, if necessary, iframe
			s.create(data);
			data = null;

			// display the modal dialog
			s.open();

			// useful for adding events/manipulating data in the modal dialog
			if ($.isFunction(s.o.onShow)) {
				s.o.onShow.apply(s, [s.d]);
			}

			// don't break the chain =)
			return s;
		},
		/*
		 * Create and add the modal overlay and container to the page
		 */
		create: function (data) {
			var s = this;

			// get the window properties
			w = s.getDimensions();

			// add an iframe to prevent select options from bleeding through
			if (s.o.modal && ie6) {
				s.d.iframe = $('<iframe src="javascript:false;"></iframe>')
					.css($.extend(s.o.iframeCss, {
						display: 'none',
						opacity: 0,
						position: 'fixed',
						height: w[0],
						width: w[1],
						zIndex: s.o.zIndex,
						top: 0,
						left: 0
					}))
					.appendTo(s.o.appendTo);
			}

			// create the overlay
			s.d.overlay = $('<div></div>')
				.attr('id', s.o.overlayId)
				.addClass('simplemodal-overlay')
				.css($.extend(s.o.overlayCss, {
					display: 'none',
					opacity: s.o.opacity / 100,
					height: s.o.modal ? w[0] : 0,
					width: s.o.modal ? w[1] : 0,
					position: 'fixed',
					left: 0,
					top: 0,
					zIndex: s.o.zIndex + 1
				}))
				.appendTo(s.o.appendTo);

			// create the container
			s.d.container = $('<div></div>')
				.attr('id', s.o.containerId)
				.addClass('simplemodal-container')
				.css($.extend(s.o.containerCss, {
					display: 'none',
					position: 'fixed',
					zIndex: s.o.zIndex + 2
				}))
				.append(s.o.close && s.o.closeHTML
					? $(s.o.closeHTML).addClass(s.o.closeClass)
					: '')
				.appendTo(s.o.appendTo);

			s.d.wrap = $('<div></div>')
				// .attr('tabIndex', -1)
				.addClass('simplemodal-wrap')
				.css({height: '100%', outline: 0, width: '100%'})
				.appendTo(s.d.container);

			// add styling and attributes to the data
			// append to body to get correct dimensions, then move to wrap
			s.d.data = data
				.attr('id', data.attr('id') || s.o.dataId)
				.addClass('simplemodal-data')
				.css($.extend(s.o.dataCss, {
						display: 'none'
				}))
				.appendTo('body');
			data = null;
			
			// yn tuninig
			if(s.o.width)
				s.d.data.css('width', s.o.width);
			if(s.o.height)
				s.d.data.css('height', s.o.height);
				
			// s.setContainerDimensions();
			s.d.data.appendTo(s.d.wrap);

			// fix issues with IE
			if (ie6 || ieQuirks) {
				s.fixIE();
			}
		},
		/*
		 * Bind events
		 */
		bindEvents: function () {
			var s = this;

			// bind the close event to any element with the closeClass class
			$('.' + s.o.closeClass).bind('click.simplemodal', function (e) {
				e.preventDefault();
				s.close();
			});

			// bind the overlay click to the close function, if enabled
			if (s.o.modal && s.o.close && s.o.overlayClose) {
				s.d.overlay.bind('click.simplemodal', function (e) {
					e.preventDefault();
					s.close();
				});
			}

			// bind keydown events
			$(document).bind('keydown.simplemodal', function (e) {
				if ((s.o.close && s.o.escClose) && e.keyCode === 27) { // ESC
					e.preventDefault();
					s.close();
				}
			});

			// update window size
			$(window).bind('resize.simplemodal', function () {
				// redetermine the window width/height
				w = s.getDimensions();

				// reposition the dialog
				s.o.autoResize ? s.setContainerDimensions() : s.o.autoPosition && s.setPosition();

				if (ie6 || ieQuirks) {
					s.fixIE();
				}
				else if (s.o.modal) {
					// update the iframe & overlay
					s.d.iframe && s.d.iframe.css({height: w[0], width: w[1]});
					s.d.overlay.css({height: w[0], width: w[1]});
				}
			});
		},
		/*
		 * Unbind events
		 */
		unbindEvents: function () {
			$('.' + this.o.closeClass).unbind('click.simplemodal');
			$(document).unbind('keydown.simplemodal');
			$(window).unbind('resize.simplemodal');
			this.d.overlay.unbind('click.simplemodal');
		},
		/*
		 * Fix issues in IE6 and IE7 in quirks mode
		 */
		fixIE: function () {
			var s = this, p = s.o.position;

			// simulate fixed position - adapted from BlockUI
			$.each([s.d.iframe || null, !s.o.modal ? null : s.d.overlay, s.d.container], function (i, el) {
				if (el) {
					var bch = 'document.body.clientHeight', bcw = 'document.body.clientWidth',
						bsh = 'document.body.scrollHeight', bsl = 'document.body.scrollLeft',
						bst = 'document.body.scrollTop', bsw = 'document.body.scrollWidth',
						ch = 'document.documentElement.clientHeight', cw = 'document.documentElement.clientWidth',
						sl = 'document.documentElement.scrollLeft', st = 'document.documentElement.scrollTop',
						s = el[0].style;

					s.position = 'absolute';
					if (i < 2) {
						s.removeExpression('height');
						s.removeExpression('width');
						s.setExpression('height','' + bsh + ' > ' + bch + ' ? ' + bsh + ' : ' + bch + ' + "px"');
						s.setExpression('width','' + bsw + ' > ' + bcw + ' ? ' + bsw + ' : ' + bcw + ' + "px"');
					}
					else {
						var te, le;
						if (p && p.constructor === Array) {
							var top = p[0]
								? typeof p[0] === 'number' ? p[0].toString() : p[0].replace(/px/, '')
								: el.css('top').replace(/px/, '');
							te = top.indexOf('%') === -1
								? top + ' + (t = ' + st + ' ? ' + st + ' : ' + bst + ') + "px"'
								: parseInt(top.replace(/%/, '')) + ' * ((' + ch + ' || ' + bch + ') / 100) + (t = ' + st + ' ? ' + st + ' : ' + bst + ') + "px"';

							if (p[1]) {
								var left = typeof p[1] === 'number' ? p[1].toString() : p[1].replace(/px/, '');
								le = left.indexOf('%') === -1
									? left + ' + (t = ' + sl + ' ? ' + sl + ' : ' + bsl + ') + "px"'
									: parseInt(left.replace(/%/, '')) + ' * ((' + cw + ' || ' + bcw + ') / 100) + (t = ' + sl + ' ? ' + sl + ' : ' + bsl + ') + "px"';
							}
						}
						else {
							te = '(' + ch + ' || ' + bch + ') / 2 - (this.offsetHeight / 2) + (t = ' + st + ' ? ' + st + ' : ' + bst + ') + "px"';
							le = '(' + cw + ' || ' + bcw + ') / 2 - (this.offsetWidth / 2) + (t = ' + sl + ' ? ' + sl + ' : ' + bsl + ') + "px"';
						}
						s.removeExpression('top');
						s.removeExpression('left');
						s.setExpression('top', te);
						s.setExpression('left', le);
					}
				}
			});
		},
		/*
		 * Place focus on the first or last visible input
		 */
		focus: function (pos) {
			var s = this, p = pos && $.inArray(pos, ['first', 'last']) !== -1 ? pos : 'first';

			// focus on dialog or the first visible/enabled input element
			var input = $(':input:enabled:visible:' + p, s.d.wrap);
			setTimeout(function () {
				input.length > 0 ? input.focus() : s.d.wrap.focus();
			}, 10);
		},
		getDimensions: function () {
			var el = $(window);

			// fix a jQuery/Opera bug with determining the window height
			var h = $.browser.opera && $.browser.version > '9.5' && $.fn.jquery < '1.3'
						|| $.browser.opera && $.browser.version < '9.5' && $.fn.jquery > '1.2.6'
				? el[0].innerHeight : el.height();

			return [h, el.width()];
		},
		getVal: function (v, d) {
			return v ? (typeof v === 'number' ? v
					: v === 'auto' ? 0
					: v.indexOf('%') > 0 ? ((parseInt(v.replace(/%/, '')) / 100) * (d === 'h' ? w[0] : w[1]))
					: parseInt(v.replace(/px/, '')))
				: null;
		},
		setContainerDimensions: function () {
			
			var s = this;
			var dw = s.d.data.outerWidth(true);		// data width
			var dh = s.d.data.outerHeight(true);	// data height
			var mw = w[1] - 50;		// max width
			var mh = w[0] - 50;		// max height
			cw = dw < mw ? dw : mw;		// container width
			ch = dh < mh ? dh : mh;		// container height

			s.d.wrap.css({overflow: (dh > ch || dw > cw) ? 'auto' : 'visible'});
			dw > cw && s.d.wrap.css('width', cw);
			dh > ch && s.d.wrap.css('height', ch);
			s.o.autoPosition && s.setPosition();
		},
		setPosition: function () {
			var s = this, top, left,
				hc = (w[0]/2) - (s.d.container.outerHeight(true)/2),
				vc = (w[1]/2) - (s.d.container.outerWidth(true)/2);

			if (s.o.position && Object.prototype.toString.call(s.o.position) === '[object Array]') {
				top = s.o.position[0] || hc;
				left = s.o.position[1] || vc;
			} else {
				top = hc;
				left = vc;
			}
			s.d.container.css({left: left, top: top});
		},
		/*
		 * Open the modal dialog elements
		 * - Note: If you use the onOpen callback, you must "show" the
		 *	        overlay and container elements manually
		 *         (the iframe will be handled by SimpleModal)
		 */
		open: function () {
			var s = this;
			// display the iframe
			s.d.iframe && s.d.iframe.show();

			if ($.isFunction(s.o.onOpen)) {
				// execute the onOpen callback
				s.o.onOpen.apply(s, [s.d]);
			}
			else {
				// display the remaining elements
				s.d.overlay.show();
				s.d.container.show();
				s.d.data.show();
			}
			
			s.setContainerDimensions();
			
			// bind default events
			s.bindEvents();
		},
		/*
		 * Close the modal dialog
		 * - Note: If you use an onClose callback, you must remove the
		 *         overlay, container and iframe elements manually
		 *
		 * @param {boolean} external Indicates whether the call to this
		 *     function was internal or external. If it was external, the
		 *     onClose callback will be ignored
		 */
		close: function () {
			var s = this;

			// prevent close when dialog does not exist
			if (!s.d.data) {
				return false;
			}
			
			// allow screen scroll, if prevented
			s.unblockScreen();
			
			// remove the default events
			s.unbindEvents();

			// execute the onClose callback
			if ($.isFunction(s.o.onClose))
				s.o.onClose.apply(s, [s.d]);
				
			// if the data came from the DOM, put it back
			if (s.d.placeholder) {
				var ph = $('#simplemodal-placeholder');
				// save changes to the data?
				if (s.o.persist) {
					// insert the (possibly) modified data back into the DOM
					ph.replaceWith(s.d.data.removeClass('simplemodal-data').css('display', s.display));
				}
				else {
					// remove the current and insert the original,
					// unmodified data back into the DOM
					s.d.data.hide().remove();
					ph.replaceWith(s.d.orig);
				}
			}
			else {
				// otherwise, remove it
				s.d.data.hide().remove();
			}

			// remove the remaining elements
			s.d.container.hide().remove();
			s.d.overlay.hide();
			s.d.iframe && s.d.iframe.hide().remove();
			// setTimeout(function(){
				// opera work-around
				s.d.overlay.remove();

				// reset the dialog object
				s.d = {};
			// }, 10);
		},
		/*
		 * prevent body scrolling
		 */
		blockScreen: function(){
			if(!screenBlocked){
				screenBlocked = true;
				screenScrollHandler = document.body.onmousewheel;
				document.body.onmousewheel = function(){ return false; }
			}
		},
		
		/*
		 * allow body scrolling
		 */
		unblockScreen: function(){
			if(screenBlocked){
				screenBlocked = false;
				document.body.onmousewheel = screenScrollHandler;
			}
		},
		/**
		 * closing modal window by esc press
		 */
		escClose: function(isClosable){
			this.o.escClose = typeof(isClosable) == 'undefined' || isClosable
				? true
				: false;
		}
	};
})(jQuery);
