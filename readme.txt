=== reCAPTCHA v3 For Gravity Forms ===
Contributors: aniltarah
Tags: gravity forms, recaptcha, google recaptcha, antispam, spam protection
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.2
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple, lightweight, and efficient plugin to add Google reCAPTCHA v3 validation to Gravity Forms.

== Description ==

This plugin adds **Google reCAPTCHA v3** validation to Gravity Forms in WordPress. It allows you to specify exactly which forms should be protected and easily manage your API keys.

**Features:**

* **Per-Form Control:** reCAPTCHA is **not** automatically enabled for all forms. You can enable or disable it via each form's individual settings.
* **AJAX Support:** Works correctly with forms submitted via AJAX.
* **Lightweight & Optimized:** Scripts are only loaded when a form is present on the page and reCAPTCHA is enabled for it.
* **i18n Ready:** Fully internationalized (Includes Persian `fa_IR` language file).

== Installation ==

1.  Install And Activate the plugin through the 'Plugins' screen in WordPress.
2.  **Global Setup:** Go to **Forms > Settings > reCAPTCHA v3** and enter your Google reCAPTCHA v3 Site Key and Secret Key.
3.  **Form Setup:** Go to the settings of the specific Gravity Form you want to protect, click on the "reCAPTCHA v3 Settings" tab, and check "Enable reCAPTCHA v3 for this form".

== Frequently Asked Questions ==

= Is reCAPTCHA enabled for all forms by default? =
No. By design, it is disabled by default to avoid conflicts or unnecessary script loading. You must manually enable it for each form in the form settings.

= Does this work with AJAX forms? =
Yes, the plugin is designed to handle tokens correctly even with AJAX submissions.

= How do I change the score threshold? =
You can change the score threshold (default 0.5) in the global settings page (Forms > Settings > reCAPTCHA v3).

== Screenshots ==

1. Global Settings Page - Enter your API keys here.
2. Form Settings Tab - Enable reCAPTCHA for specific forms.

== Changelog ==

= 1.0 =
* Initial release.