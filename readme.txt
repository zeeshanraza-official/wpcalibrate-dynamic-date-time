=== WPCalibrate Dynamic Date & Time ===
Contributors: wpcalibrate
Donate link: https://wpcalibrate.com
Tags: date, time, dynamic date, dynamic time, landing page, elementor, gutenberg, clock
Requires at least: 6.0
Tested up to: 7.1.3
Requires PHP: 8.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display dynamic dates and times across your WordPress site, landing pages, Elementor, Gutenberg, and shortcode page builders.

== Description ==

WPCalibrate Dynamic Date & Time allows site administrators, content creators, and marketers to insert dynamic, auto-updating dates and times into WordPress pages, posts, funnels, and landing pages without writing PHP or JavaScript.

The plugin provides a unified shortcode system `[wpcal_datetime]`, convenient aliases `[wpcal_date]` and `[wpcal_time]`, and a native Gutenberg block. It supports WordPress site timezones, visitor local browser timezones, relative calendar-day offsets, custom format strings, live clocks, and reusable administrator presets.

= Key Features =

* **Dynamic Outputs**: Current date, current time, combined date/time, day name, day number, month name, month number, and year.
* **Safe Day Offsets**: Display future or past dates with relative offsets like `+1 day`, `+3 days`, `+7 days`, `-1 day`, or `-30 days`.
* **Visitor Browser Timezone**: Automatically localizes date and time to the visitor's local browser timezone without IP lookups, geolocation, or external APIs.
* **Live Clock Mode**: Centralized single-timer live updating for clocks that tick smoothly without page refreshes or performance overhead.
* **Reusable Presets**: Bundle your date types, formats, offsets, and timezone configurations into reusable presets.
* **Page Builder Support**: Fully compatible with Gutenberg, Elementor, Divi, Bricks, Beaver Builder, Classic Editor, and any theme or builder supporting WordPress shortcodes.
* **Privacy Focused**: No visitor personal data is collected or stored. All visitor timezone adjustments happen locally inside the visitor's browser.
* **No Dependencies**: Lightweight and independent. Does not require WooCommerce, third-party libraries, or external services.

= Shortcode Examples =

* `[wpcal_datetime]` - Outputs the current date according to your default format.
* `[wpcal_datetime type="date"]` - Outputs the current date.
* `[wpcal_datetime type="time"]` - Outputs the current time.
* `[wpcal_datetime type="datetime"]` - Outputs combined date and time.
* `[wpcal_datetime type="day"]` - Outputs the current day name (e.g. Monday).
* `[wpcal_datetime type="month"]` - Outputs the current month name (e.g. October).
* `[wpcal_datetime type="year"]` - Outputs the current year (e.g. 2026).
* `[wpcal_datetime offset="+1 day"]` - Outputs tomorrow's date.
* `[wpcal_datetime offset="+7 days"]` - Outputs the date 7 days in the future.
* `[wpcal_datetime format="F j, Y"]` - Outputs date formatted with custom PHP tokens.
* `[wpcal_datetime timezone="visitor"]` - Localizes date/time to the visitor's browser timezone.
* `[wpcal_datetime type="time" timezone="visitor" live="yes"]` - Live ticking clock in visitor's local timezone.
* `[wpcal_datetime preset="offer-expiry"]` - Loads configuration from a saved preset.

= Caching Behavior =

* When using `timezone="site"` and `live="no"`, output is server-rendered and can be safely cached by full-page caching plugins (such as WP Rocket, LiteSpeed Cache, Super Cache, or CDN page caches).
* When using `timezone="visitor"` or `live="yes"`, the plugin renders lightweight HTML data attributes with a server fallback and updates the text via native browser JavaScript. This ensures that even when HTML is served from a static page cache, visitors still see their accurate local time.

= External Services Disclosure =

WPCalibrate Dynamic Date & Time does not connect to any external date/time APIs, geolocation services, or third-party servers. All site dates are calculated using the WordPress site timezone, and visitor timezones are determined locally in the browser via the standard `Intl.DateTimeFormat` browser API.

== Installation ==

1. Upload the `wpcalibrate-dynamic-date-time` folder to the `/wp-content/plugins/` directory, or install the ZIP file via **Plugins > Add New > Upload Plugin**.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **WPCalibrate > Dynamic Date & Time** in your WordPress dashboard to configure default formats, manage presets, and view shortcode examples.
4. Insert shortcodes or the Gutenberg "Dynamic Date & Time" block into your content.

== Frequently Asked Questions ==

= Does this plugin require WooCommerce? =
No. WPCalibrate Dynamic Date & Time is completely independent and does not require WooCommerce.

= Does this plugin work with page builders like Elementor? =
Yes. You can paste `[wpcal_datetime]` or any of its variations directly into Elementor Shortcode widgets, Text widgets, headings, or any page builder that evaluates WordPress shortcodes.

= How does visitor timezone detection work? =
Visitor timezone detection uses the browser's native `Intl.DateTimeFormat().resolvedOptions().timeZone` API. No external IP geolocation or tracking is performed. If a visitor's browser disables JavaScript or does not support timezone resolution, the server-rendered site timezone date is displayed as a fallback.

= What happens if I use an invalid offset? =
The plugin strictly parses day offsets and rejects arbitrary or invalid expressions. If an unrecognized expression is provided, it falls back safely to 0 days without throwing PHP warnings or errors.

= Can multiple live clocks be placed on the same page? =
Yes. The plugin uses a single centralized update loop that efficiently refreshes all live elements on the page, avoiding duplicate timers or excessive DOM writes.

== Changelog ==

= 1.0.0 =
* Initial public release.
* Support for dynamic dates, times, day names, month names, and years.
* Safe relative day offsets (`+X days`, `-X days`).
* Support for WordPress site timezone and browser visitor timezone.
* Centralized performance-optimized live clock updating.
* Reusable administrator presets management.
* Gutenberg block integration using server-side rendering.
* Full compatibility with Elementor and shortcode builders.
* Translation-ready and accessible admin interface.

== Privacy ==

WPCalibrate Dynamic Date & Time respects visitor privacy:
* Does not set tracking cookies.
* Does not collect, process, or store personal data.
* Does not communicate with external APIs, IP lookup services, or third-party endpoints.
* Visitor timezone calculations are executed entirely within the visitor's local browser environment.
