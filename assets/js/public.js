/**
 * WPCalibrate Dynamic Date & Time Public JavaScript
 *
 * Lightweight vanilla JavaScript controller for client-side visitor timezone
 * localized formatting and centralized live-clock updates.
 */
(function () {
	'use strict';

	/**
	 * Main controller object.
	 */
	const WPCalibrateDateTime = {
		timerId: null,
		liveElements: [],
		locale: 'default',
		siteTimezone: 'UTC',

		/**
		 * Initialize controller.
		 */
		init: function () {
			if (typeof document === 'undefined') {
				return;
			}

			// Determine page or browser locale.
			try {
				if (document.documentElement && document.documentElement.lang) {
					this.locale = document.documentElement.lang;
				} else if (navigator.languages && navigator.languages.length) {
					this.locale = navigator.languages[0];
				} else if (navigator.language) {
					this.locale = navigator.language;
				}
			} catch (e) {
				this.locale = 'default';
			}

			// Extract site timezone from localized settings if available.
			if (typeof wpcalDdtSettings !== 'undefined' && wpcalDdtSettings.siteTimezone) {
				this.siteTimezone = wpcalDdtSettings.siteTimezone;
			}

			// Process all dynamic date/time elements on page.
			this.processElements();

			// Listen for DOM updates (e.g. AJAX pagination, Elementor popups)
			if (typeof MutationObserver !== 'undefined' && document.body) {
				const observer = new MutationObserver((mutations) => {
					let hasNew = false;
					for (let i = 0; i < mutations.length; i++) {
						if (mutations[i].addedNodes && mutations[i].addedNodes.length) {
							hasNew = true;
							break;
						}
					}
					if (hasNew) {
						WPCalibrateDateTime.processElements();
					}
				});
				observer.observe(document.body, { childList: true, subtree: true });
			}
		},

		/**
		 * Find and process all elements matching [data-wpcal-datetime].
		 */
		processElements: function () {
			const elements = document.querySelectorAll('[data-wpcal-datetime="true"]');
			const newLiveElements = [];

			for (let i = 0; i < elements.length; i++) {
				const el = elements[i];
				const timezoneMode = el.getAttribute('data-timezone') || 'site';
				const isLive = el.getAttribute('data-live') === '1';

				// Perform initial formatting update for visitor timezone or live mode.
				if (timezoneMode === 'visitor' || isLive) {
					this.updateElement(el);
				}

				if (isLive) {
					newLiveElements.push(el);
				}
			}

			this.liveElements = newLiveElements;

			// Manage centralized timer lifecycle.
			if (this.liveElements.length > 0) {
				if (!this.timerId) {
					this.startTimer();
				}
			} else if (this.timerId) {
				this.stopTimer();
			}
		},

		/**
		 * Start centralized update timer.
		 */
		startTimer: function () {
			if (this.timerId) {
				return;
			}
			this.timerId = setInterval(() => {
				WPCalibrateDateTime.tick();
			}, 1000);
		},

		/**
		 * Stop centralized update timer.
		 */
		stopTimer: function () {
			if (this.timerId) {
				clearInterval(this.timerId);
				this.timerId = null;
			}
		},

		/**
		 * Tick handler for centralized timer.
		 */
		tick: function () {
			if (!this.liveElements || this.liveElements.length === 0) {
				this.stopTimer();
				return;
			}

			// Filter out disconnected elements that were removed from the DOM.
			const activeElements = [];
			for (let i = 0; i < this.liveElements.length; i++) {
				const el = this.liveElements[i];
				if (document.body.contains(el)) {
					this.updateElement(el);
					activeElements.push(el);
				}
			}

			this.liveElements = activeElements;

			if (this.liveElements.length === 0) {
				this.stopTimer();
			}
		},

		/**
		 * Format and update a single element.
		 *
		 * @param {HTMLElement} el
		 */
		updateElement: function (el) {
			try {
				const format = el.getAttribute('data-format') || 'F j, Y';
				const timezoneMode = el.getAttribute('data-timezone') || 'site';
				const offsetDays = parseInt(el.getAttribute('data-offset') || '0', 10) || 0;

				const formattedText = this.formatDate(format, timezoneMode, offsetDays);

				// Only write to DOM if text has actually changed.
				if (el.textContent !== formattedText) {
					el.textContent = formattedText;
				}
			} catch (err) {
				// Retain server fallback on error.
			}
		},

		/**
		 * Calculate and format date/time string.
		 *
		 * @param {string} format PHP date format tokens.
		 * @param {string} timezoneMode 'visitor' or 'site'.
		 * @param {number} offsetDays Day offset.
		 * @returns {string} Formatted date/time.
		 */
		formatDate: function (format, timezoneMode, offsetDays) {
			const now = new Date();

			// For visitor timezone, perform calendar-day offset in visitor local time.
			// Constructing Date with (year, month, date + offsetDays) preserves local calendar days across DST.
			let targetDate;
			let targetTimezone = undefined;

			if (timezoneMode === 'visitor') {
				targetDate = new Date(
					now.getFullYear(),
					now.getMonth(),
					now.getDate() + offsetDays,
					now.getHours(),
					now.getMinutes(),
					now.getSeconds(),
					now.getMilliseconds()
				);
			} else {
				// Site timezone
				targetTimezone = this.siteTimezone;
				// In target timezone, apply offset in that timezone context.
				targetDate = new Date(now.getTime());
				// Shift days safely
				targetDate.setDate(targetDate.getDate() + offsetDays);
			}

			return this.formatTokens(targetDate, format, targetTimezone);
		},

		/**
		 * Format a Date object using PHP date tokens.
		 *
		 * @param {Date} date Target Date object.
		 * @param {string} format PHP format string.
		 * @param {string|undefined} timeZone Optional explicit timezone.
		 * @returns {string} Formatted string.
		 */
		formatTokens: function (date, format, timeZone) {
			const pad = (n) => (n < 10 ? '0' + n : '' + n);
			const locale = this.locale;

			// Extract components using Intl.DateTimeFormat parts if timezone is specified,
			// or using native Date methods for visitor local timezone.
			let Y, m, d, H, i, s, dayOfWeek;

			if (timeZone) {
				try {
					const dtf = new Intl.DateTimeFormat('en-US', {
						timeZone: timeZone,
						year: 'numeric',
						month: 'numeric',
						day: 'numeric',
						hour: 'numeric',
						minute: 'numeric',
						second: 'numeric',
						hour12: false,
						weekday: 'short',
					});
					const parts = dtf.formatToParts(date);
					const p = {};
					for (let k = 0; k < parts.length; k++) {
						p[parts[k].type] = parts[k].value;
					}
					Y = parseInt(p.year, 10);
					m = parseInt(p.month, 10); // 1-12
					d = parseInt(p.day, 10);
					H = parseInt(p.hour, 10);
					if (H === 24) H = 0; // Fix edge case for some browser formatters
					i = parseInt(p.minute, 10);
					s = parseInt(p.second, 10);
					// Recompute day of week for that timezone
					dayOfWeek = new Date(Date.UTC(Y, m - 1, d)).getUTCDay();
				} catch (e) {
					// Fall back to local
					Y = date.getFullYear();
					m = date.getMonth() + 1;
					d = date.getDate();
					H = date.getHours();
					i = date.getMinutes();
					s = date.getSeconds();
					dayOfWeek = date.getDay();
				}
			} else {
				Y = date.getFullYear();
				m = date.getMonth() + 1;
				d = date.getDate();
				H = date.getHours();
				i = date.getMinutes();
				s = date.getSeconds();
				dayOfWeek = date.getDay();
			}

			// Precalculate localized names using Intl.DateTimeFormat
			let monthFullName, monthShortName, dayFullName, dayShortName;
			try {
				const opts = timeZone ? { timeZone: timeZone } : {};
				monthFullName = new Intl.DateTimeFormat(locale, Object.assign({}, opts, { month: 'long' })).format(date);
				monthShortName = new Intl.DateTimeFormat(locale, Object.assign({}, opts, { month: 'short' })).format(date);
				dayFullName = new Intl.DateTimeFormat(locale, Object.assign({}, opts, { weekday: 'long' })).format(date);
				dayShortName = new Intl.DateTimeFormat(locale, Object.assign({}, opts, { weekday: 'short' })).format(date);
			} catch (e) {
				// Fallback to English names
				const monthsLong = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
				const monthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
				const daysLong = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
				const daysShort = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

				monthFullName = monthsLong[m - 1];
				monthShortName = monthsShort[m - 1];
				dayFullName = daysLong[dayOfWeek];
				dayShortName = daysShort[dayOfWeek];
			}

			// 12-hour format calculations
			const hour12 = H % 12 === 0 ? 12 : H % 12;
			const ampmLower = H < 12 ? 'am' : 'pm';
			const ampmUpper = H < 12 ? 'AM' : 'PM';

			// English ordinal suffix
			const getSuffix = function (val) {
				const j = val % 10;
				const k = val % 100;
				if (j === 1 && k !== 11) return 'st';
				if (j === 2 && k !== 12) return 'nd';
				if (j === 3 && k !== 13) return 'rd';
				return 'th';
			};

			// Days in month
			const isLeap = (Y % 4 === 0 && Y % 100 !== 0) || Y % 400 === 0;
			const daysInMonth = [31, isLeap ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][m - 1];

			let result = '';
			let isEscaped = false;

			for (let idx = 0; idx < format.length; idx++) {
				const char = format[idx];

				if (char === '\\') {
					if (isEscaped) {
						result += '\\';
						isEscaped = false;
					} else {
						isEscaped = true;
					}
					continue;
				}

				if (isEscaped) {
					result += char;
					isEscaped = false;
					continue;
				}

				switch (char) {
					// Day
					case 'd':
						result += pad(d);
						break;
					case 'D':
						result += dayShortName;
						break;
					case 'j':
						result += d;
						break;
					case 'l':
						result += dayFullName;
						break;
					case 'N':
						result += dayOfWeek === 0 ? 7 : dayOfWeek;
						break;
					case 'S':
						result += getSuffix(d);
						break;
					case 'w':
						result += dayOfWeek;
						break;

					// Month
					case 'F':
						result += monthFullName;
						break;
					case 'm':
						result += pad(m);
						break;
					case 'M':
						result += monthShortName;
						break;
					case 'n':
						result += m;
						break;
					case 't':
						result += daysInMonth;
						break;

					// Year
					case 'L':
						result += isLeap ? '1' : '0';
						break;
					case 'Y':
						result += Y;
						break;
					case 'y':
						result += ('' + Y).slice(-2);
						break;

					// Time
					case 'a':
						result += ampmLower;
						break;
					case 'A':
						result += ampmUpper;
						break;
					case 'g':
						result += hour12;
						break;
					case 'G':
						result += H;
						break;
					case 'h':
						result += pad(hour12);
						break;
					case 'H':
						result += pad(H);
						break;
					case 'i':
						result += pad(i);
						break;
					case 's':
						result += pad(s);
						break;

					default:
						result += char;
						break;
				}
			}

			return result;
		},
	};

	// Expose globally for testing and diagnostics.
	window.WPCalibrateDateTime = WPCalibrateDateTime;

	// Automatically initialize once DOM is ready.
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			WPCalibrateDateTime.init();
		});
	} else {
		WPCalibrateDateTime.init();
	}
})();
