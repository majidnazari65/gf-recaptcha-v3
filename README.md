English | [فارسی](#-افزونه-recaptcha-v3-برای-gravity-forms)

# reCAPTCHA v3 for Gravity Forms

[![License: GPL v2 or later](https://img.shields.io/badge/License-GPLv2%20or%20later-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

A simple, lightweight, and efficient plugin to add **Google reCAPTCHA v3** validation to [Gravity Forms](https://www.gravityforms.com/) in WordPress.

This plugin allows you to specify exactly which forms should be protected and easily manage your API keys.

## ✨ Features

* **Per-Form Control:** reCAPTCHA is **not** automatically enabled for all forms. You can enable or disable it via each form's individual settings.
* **AJAX Support:** Works correctly with forms submitted via AJAX.
* **i18n Ready:** Fully internationalized and ready for translation. (Includes Persian `fa_IR` language file).
* **Lightweight & Optimized:** Scripts are only loaded when a form is present on the page and reCAPTCHA is enabled for it.

## ⚙️ Requirements

* WordPress 5.0 or higher
* Gravity Forms (active)
* Google reCAPTCHA v3 Keys (Site Key and Secret Key)

## 🚀 Installation & Setup

Setting up the plugin involves two main steps:

### Step 1: Install the Plugin

1.  Download the latest release from the [GitHub Releases page](https://github.com/majidnazari65/gf-recaptcha-v3/archive/refs/heads/main.zip).
2.  In your WordPress dashboard, go to **Plugins > Add New > Upload Plugin**.
3.  Select the downloaded `.zip` file and install it.
4.  Activate the plugin.

### Step 2: Configuration (Very Important)

Setup has two parts: **Global Settings** (for API keys) and **Form Settings** (for activation).

#### Part 1: Global Settings (Enter API Keys)

1.  First, get your reCAPTCHA v3 keys from the [Google reCAPTCHA Admin Console](https://www.google.com/recaptcha/admin/create).
2.  In your WordPress dashboard, go to **Forms > Setting > reCAPTCHA v3**.
3.  Enter the **Site Key** and **Secret Key** you received from Google.
4.  Set the **Score Threshold** (Google's default is `0.5`).
5.  Click **"Save Settings"**.

#### Part 2: Per-Form Activation

By design, reCAPTCHA is **disabled** by default for all forms. You must manually enable it for each form you wish to protect:

1.  Go to your main "Forms" list.
2.  Hover over the desired form and go to its **"Settings"**.
3.  On the settings page, click the **"reCAPTCHA v3 Settings"** tab.
4.  Check the box for **"Enable reCAPTCHA v3 for this form"**.
5.  Save the form settings.

This form is now protected. Repeat this process for any other forms you need to secure.

## 📄 License

This plugin is licensed under the [GPLv2 (or later)](https://www.gnu.org/licenses/gpl-2.0.html).

---

[English](#recaptcha-v3-for-gravity-forms) | فارسی

# افزونه reCAPTCHA v3 برای Gravity Forms

[![License: GPL v2 or later](https://img.shields.io/badge/License-GPLv2%20or%20later-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

یک پلاگین ساده، سبک و کارآمد برای افزودن اعتبارسنجی **Google reCAPTCHA v3** به فرم‌های [Gravity Forms](https://www.gravityforms.com/) در وردپرس.

این افزونه به شما اجازه می‌دهد تا به صورت کامل مشخص کنید کدام فرم‌ها باید محافظت شوند و تنظیمات کلیدهای API خود را به راحتی مدیریت کنید.

## ✨ ویژگی‌ها

* **کنترل کامل بر روی فرم‌ها:** reCAPTCHA به صورت خودکار برای همه‌ فرم‌ها فعال **نمی‌شود**. شما می‌توانید از طریق تنظیمات هر فرم، آن را فعال یا غیرفعال کنید.
* **پشتیبانی از AJAX:** به درستی با فرم‌هایی که از ارسال ایجکس (AJAX) استفاده می‌کنند، کار می‌کند.
* **پشتیبانی از چندزبانگی:** به طور کامل برای ترجمه (i18n) آماده شده و شامل فایل ترجمه فارسی (`fa_IR`) است.
* **سبک و بهینه:** تنها زمانی اسکریپت‌ها را بارگذاری می‌کند که فرم مورد نظر در صفحه وجود داشته باشد و reCAPTCHA برای آن فعال باشد.

## ⚙️ نیازمندی‌ها

* وردپرس نسخه 5.0 یا بالاتر
* پلاگین Gravity Forms (فعال)
* کلیدهای Google reCAPTCHA v3 (Site Key و Secret Key)

## 🚀 نصب و راه‌اندازی

نصب و تنظیم این افزونه در دو مرحله اصلی انجام می‌شود:

### مرحله 1: نصب افزونه

1.  آخرین نسخه افزونه را از [صفحه Releases گیت‌هاب](https://github.com/majidnazari65/gf-recaptcha-v3/archive/refs/heads/main.zip) دانلود کنید.
2.  به پیشخوان وردپرس خود بروید: **افزونه‌ها > افزودن > بارگذاری افزونه**.
3.  فایل `.zip` دانلود شده را انتخاب و نصب کنید.
4.  افزونه را فعال نمایید.

### مرحله 2: پیکربندی (بسیار مهم)

راه اندازی این افزونه شامل ۲ بخش است: **تنظیمات عمومی** (برای ورود کلیدها) و **تنظیمات فرم** (برای فعال‌سازی).

#### بخش اول: تنظیمات عمومی (ورود کلیدها)

1.  ابتدا باید کلیدهای reCAPTCHA v3 خود را از [کنسول ادمین Google reCAPTCHA](https://www.google.com/recaptcha/admin/create) دریافت کنید.
2.  در پیشخوان وردپرس، به منوی **فرم‌ها > تنظیمات > reCAPTCHA v3** بروید.
3.  **کلید سایت (Site Key)** و **کلید مخفی (Secret Key)** را که از گوگل دریافت کرده‌اید، در فیلدهای مربوطه وارد کنید.
4.  **آستانه امتیاز (Score Threshold)** را تنظیم کنید (پیشنهاد گوگل `0.5` است).
5.  روی دکمه **"ذخیره تنظیمات"** کلیک کنید.

#### بخش دوم: فعال‌سازی برای هر فرم

بر اساس طراحی این افزونه، reCAPTCHA به صورت پیش‌فرض برای فرم‌ها **غیرفعال** است. شما باید آن را برای هر فرمی که نیاز به محافظت دارد، به صورت دستی فعال کنید:

1.  به لیست فرم‌های خود در منوی **"فرم‌ها"** بروید.
2.  روی فرم مورد نظر هاور کنید و به **"تنظیمات"** بروید.
3.  در صفحه تنظیمات فرم، روی تب **"reCAPTCHA v3 Settings"** کلیک کنید.
4.  تیک گزینه **"Enable reCAPTCHA v3 for this form"** را بزنید.
5.  تنظیمات فرم را ذخیره کنید.

اکنون این فرم توسط reCAPTCHA v3 محافظت می‌شود. این فرآیند را برای هر فرم دیگری که نیاز به محافظت دارد، تکرار کنید.

## 📄 مجوز

این افزونه تحت مجوز [GPLv2 (یا جدیدتر)](https://www.gnu.org/licenses/gpl-2.0.html) منتشر شده است.