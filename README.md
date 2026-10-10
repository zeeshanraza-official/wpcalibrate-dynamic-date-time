# WPCalibrate Dynamic Date & Time

[![WordPress Tested](https://img.shields.io/badge/WordPress-6.0--7.1.3-blue.svg)](https://wordpress.org)
[![PHP Target](https://img.shields.io/badge/PHP-%3E%3D8.2-777bb4.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-GPL%202.0%2B-green.svg)](LICENSE)
[![GitHub release](https://img.shields.io/badge/Release-1.0.1-orange.svg)](https://github.com/zeeshanraza-official/wpcalibrate-dynamic-date-time/releases)

Display dynamic dates and times anywhere across your WordPress site—landing pages, promotional sales, sales funnels, Gutenberg content, Elementor pages, and any page builder supporting shortcodes.

Features visitor and site timezone modes, safe calendar-day offsets, a centralized live clock ticker, and reusable preset templates—with zero external API dependencies and zero page bloat.

---

## 📖 Table of Contents

- [About the Plugin](#-about-the-plugin)
- [Key Features](#-key-features)
- [Installation](#-installation)
- [Updating from WordPress Dashboard](#-updating-from-wordpress-dashboard)
- [How to Use](#-how-to-use)
  - [Native Gutenberg Block](#1-native-gutenberg-block)
  - [Shortcodes](#2-shortcodes)
  - [Reusable Presets](#3-reusable-presets)
  - [Shortcode Attributes Reference](#shortcode-attributes-reference)
- [Operating Modes](#-operating-modes)
  - [Site Timezone vs. Visitor Timezone](#site-timezone-vs-visitor-timezone)
  - [Live Clock Mode](#live-clock-mode)
  - [Safe Calendar Day Offsets](#safe-calendar-day-offsets)
- [Admin Settings & Overview](#-admin-settings--overview)
- [Architecture & Performance](#-architecture--performance)
- [Changelog](#-changelog)
- [Support & Community](#-support--community)
- [License](#-license)

---

## 🌟 About the Plugin

Static dates on promotional landing pages, sales funnels, and checkout banners quickly expire, requiring continuous manual editing or risking lost sales and credibility.

**WPCalibrate Dynamic Date & Time** solves this by providing automated, real-time date and time output everywhere:
- **Scarcity & Urgency:** Display evergreen offer deadlines like *"Special promotion ends this Sunday, October 18"*.
- **Localized Presence:** Display the customer's actual local time (*"Customer care available now in your timezone"*).
- **Automated Copyright & Year:** Keep footer and header copyright notices automatically current without yearly updates.
- **Cache-Proof:** Full-page caches (WP Rocket, LiteSpeed, Cloudflare, Fastly) are preserved while dynamic visitor dates hydrate instantly on page load.

---

## ✨ Key Features

- **🚀 3 Easy Ways to Embed:**
  - **Gutenberg Block:** Visual editor with live sidebar inspector controls.
  - **Clean Shortcodes:** `[wpcal_datetime]`, `[wpcal_date]`, `[wpcal_time]`.
  - **Reusable Presets:** Define logic once and reference by slug (`preset="flash-sale"`).
- **🌍 Dual Timezone Architecture:**
  - **Site Timezone Mode:** Uses your WordPress General Settings timezone (`wp_timezone()`).
  - **Visitor Timezone Mode:** Seamless client-side JavaScript hydration using the visitor's browser timezone (`Intl.DateTimeFormat`).
- **⏱️ Centralized Live Clock Engine:**
  - Real-time live ticking clocks with zero memory leaks.
  - A single coordinated `requestAnimationFrame` loop updates all live clocks simultaneously without layout thrashing.
- **📅 Safe Calendar-Day Offsets:**
  - Supports `+X days`, `-X days`, `tomorrow`, `yesterday`, and `today`.
  - Full calendar arithmetic preserving Daylight Saving Time (DST) transitions—never drifts by &plusmn;1 hour.
- **🔒 Enterprise Security & Sanitization:**
  - Strict input sanitization and PHP format token validation.
  - No `eval()`, no raw `strtotime()` on untrusted input, no external API lookups, and no tracking scripts.
- **🎨 Polished Admin Interface:**
  - Dedicated **Overview Tab** detailing plugin purpose, system health, and methods.
  - Redesigned **Presets Manager** with 1-click clipboard copy, badges, and responsive layout.
  - Multi-plugin coexistence under the top-level `WPCalibrate` dashboard menu.

---

## 📥 Installation

### Method 1: WordPress Dashboard Upload (Recommended)
1. Download the latest `wpcalibrate-dynamic-date-time-1.0.0.zip` from [Releases](https://github.com/zeeshanraza-official/wpcalibrate-dynamic-date-time/releases).
2. In your WordPress admin dashboard, go to **Plugins → Add New Plugin → Upload Plugin**.
3. Choose the ZIP file and click **Install Now**.
4. Click **Activate Plugin**.

### Method 2: Manual Installation via FTP
1. Extract the ZIP archive.
2. Upload the `wpcalibrate-dynamic-date-time` folder to `/wp-content/plugins/` on your server.
3. In WordPress admin, go to **Plugins → Installed Plugins** and activate **WPCalibrate Dynamic Date & Time**.

---

## 🔄 Updating from WordPress Dashboard

This plugin includes built-in **GitHub Release Update Integration**:

- When a new version is released on GitHub, WordPress automatically detects it during scheduled background update checks.
- You will see the familiar update notification badge under **Dashboard → Updates** and **Plugins**.
- Click **"Update Now"** or click **"View version details"** to view changelogs directly inside the WordPress modal.
- Fully compatible with **Git Updater** and **GitHub Updater** via standard `Update URI` and `GitHub Plugin URI` plugin headers.

---

## 🚀 How to Use

### 1. Native Gutenberg Block

1. In the Block Editor, click the `+` block inserter.
2. Search for **Dynamic Date & Time** (under the *WPCalibrate* category).
3. Select an existing **Preset** from the dropdown, or customize **Type**, **Format**, **Timezone**, **Offset**, and **Live Mode** directly in the block sidebar inspector.

### 2. Shortcodes

Insert shortcodes into any post, page, Gutenberg shortcode block, Elementor Shortcode widget, Divi, Beaver Builder, or theme template.

#### Basic Examples
```text
[wpcal_date]
Output: October 10, 2026

[wpcal_time]
Output: 1:45 PM

[wpcal_datetime]
Output: October 10, 2026 1:45 PM
```

#### Relative Day Offsets
```text
[wpcal_date offset="+3 days"]
Output: October 13, 2026

[wpcal_date offset="tomorrow" format="l, F j"]
Output: Sunday, October 11
```

#### Visitor Timezone & Live Clock
```text
[wpcal_time timezone="visitor" live="yes" format="g:i:s A"]
Output: 1:45:22 PM (updates live every second)
```

### 3. Reusable Presets

Create presets under **WPCalibrate → Presets** and reference them anywhere with a simple slug:

```text
[wpcal_datetime preset="offer-expiry"]
```

### Shortcode Attributes Reference

| Attribute | Default | Options | Description |
| :--- | :--- | :--- | :--- |
| `type` | `date` | `date`, `time`, `datetime`, `day`, `day_num`, `month`, `month_num`, `year`, `tomorrow`, `yesterday` | Output category. |
| `format` | Global setting | Any PHP date format string (e.g. `F j, Y`, `g:i A`, `l`) | Formatting template. |
| `timezone` | `site` | `site`, `visitor` | Timezone calculation source. |
| `offset` | `0 days` | `+X days`, `-X days`, `tomorrow`, `yesterday`, `today` | Relative calendar-day offset (&plusmn;3650 days). |
| `live` | `no` | `yes`, `no` | Enables real-time ticking clock updates. |
| `preset` | *None* | Preset slug (e.g. `offer-expiry`) | References saved preset logic. |
| `class` | *None* | CSS class name string | Custom CSS classes applied to output wrapper. |

---

## ⚙️ Operating Modes

### Site Timezone vs. Visitor Timezone

| Aspect | Site Timezone (`timezone="site"`) | Visitor Timezone (`timezone="visitor"`) |
| :--- | :--- | :--- |
| **Origin** | Server-side WordPress setting (`wp_timezone()`) | Client-side browser runtime (`Intl.DateTimeFormat`) |
| **Best For** | Global broadcast events, scheduled store sales | Localized user greetings, visitor office hours |
| **Caching** | Caches with page unless full-page cache bypass | Completely cache-proof (hydrates via JavaScript) |

### Live Clock Mode
When `live="yes"`, the plugin tags output elements with `data-wpcal-datetime` and registers them with a single centralized browser timer. Elements update every second with minimal CPU and memory impact.

### Safe Calendar Day Offsets
All offsets are calculated using full calendar date increments:
* **PHP:** `DateTimeImmutable::modify("+{$days} days")`
* **JavaScript:** `new Date(year, month, date + offsetDays)`

This guarantees that crossing Daylight Saving Time (DST) boundaries preserves exact calendar dates without drifting forward or backward by one hour.

---

## 🖥️ Admin Settings & Overview

Access plugin controls under **WPCalibrate** in your WordPress admin menu:

* **Overview Tab:** System snapshot (server timezone, server time, preset counts), feature guide, and operational instructions.
* **General Settings Tab:** Default formats, default timezone preference, live clock enable/disable, and uninstallation data cleanup rules.
* **Presets Tab:** Full CRUD management for reusable date/time presets with 1-click clipboard copy.
* **Shortcode Guide Tab:** Comprehensive parameter reference table and copyable shortcode cards.
* **Support Tab:** Environment diagnostic details and direct support links.

---

## 🏗️ Architecture & Performance

```
┌─────────────────────────┐     ┌────────────────────────┐     ┌───────────────────────┐
│ [wpcal_datetime] / Block│ ──> │ DateTimeService        │ ──> │ HTML Output / Span    │
└─────────────────────────┘     └────────────────────────┘     └───────────────────────┘
                                           │
                                ┌──────────┴──────────┐
                                ▼                     ▼
                       Site Mode (PHP)       Visitor Mode (JS Data)
```

- **Zero External Dependencies:** No external API requests, no CDN dependencies, and no WooCommerce requirements.
- **Zero Database Bloat:** Presets and settings are stored in standard WordPress options (`wpcalibrate_ddt_settings`, `wpcalibrate_ddt_presets`).
- **Complete Uninstall Handler:** Optional clean uninstall wipes all plugin options and transients when enabled.

---

## 📋 Changelog

### [1.0.1] - 2026-10-10
- **Bug Fixes & Hardening:**
  - Fixed fatal critical error on activation caused by autoloader filename mismatch for `GitHubUpdater`.
  - Added robust multi-candidate autoloader resolution and explicit bootstrap dependency loading.
  - Added compatibility aliases for in-dashboard GitHub update checker.

### [1.0.0] - 2026-10-10
- **Initial Release:**
  - Dynamic date, time, and datetime rendering via shortcodes and native Gutenberg block.
  - Dual timezone engine (Site WordPress timezone vs. Visitor browser timezone).
  - Centralized live clock ticker with single timer loop.
  - Calendar-day offset engine supporting up to &plusmn;3650 days with DST preservation.
  - Reusable preset template management in WordPress admin.
  - Dedicated Overview tab with operational guidelines and quickstart guide.
  - Native in-dashboard GitHub release update checking and installation.
  - Multi-plugin coexistence under shared top-level WPCalibrate dashboard menu.
  - Automated PHP and Edge headless browser test suites.

---

## 💬 Support & Community

- **Website:** [https://wpcalibrate.com](https://wpcalibrate.com)
- **Documentation:** [https://docs.wpcalibrate.com](https://docs.wpcalibrate.com)
- **Issues & Contributions:** [GitHub Issues](https://github.com/zeeshanraza-official/wpcalibrate-dynamic-date-time/issues)
- **Email:** [support@wpcalibrate.com](mailto:support@wpcalibrate.com)

---

## 📄 License

This plugin is free software, licensed under the terms of the [GNU General Public License v2.0 or later](LICENSE).

Copyright © 2026 [WPCalibrate](https://wpcalibrate.com).
