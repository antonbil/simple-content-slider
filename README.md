# Simple Content Slider

[![License: GPL v2 or later](https://img.shields.io/badge/License-GPL%20v2%20or%20later-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

**Author:** [Anton Bil](https://familiebil.nl/anton) ([@antonbil on GitHub](https://github.com/antonbil))
**Tags:** slider, content slider, carousel, slick carousel, shortcode, responsive, json
**Requires at least:** 5.0
**Tested up to:** 6.8.2
**Stable tag:** 1.2
**License:** GPLv2 or later
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

Displays a simple, responsive content slider using Slick Carousel. Slides can be defined directly in the shortcode or loaded from a `simple-content-slider.json` file.

## Description

The "Simple Content Slider" plugin allows you to easily add a responsive and touch-friendly content slider to your WordPress posts and pages. It leverages the powerful Slick Carousel library (loaded via CDN) for its core functionality.

You can configure slides in two ways:
1.  **Directly via Shortcode Attributes:** Define title, subtitle, and URL for each slide within the `[simple_content_slider]` shortcode.
2.  **Via a JSON file:** If no slide attributes are provided in the shortcode, the plugin will attempt to load slide data from a `simple-content-slider.json` file located in the plugin's root directory.

The slider is highly configurable through Slick Carousel's options (some exposed via shortcode attributes or JavaScript). It's designed to be lightweight and integrate smoothly into your WordPress site.

## Features

*   **Responsive Design:** Adapts to different screen sizes.
*   **Touch-Friendly:** Supports swipe gestures on mobile devices.
*   **Flexible Content Source:**
    *   Define slides directly in the shortcode.
    *   Load slides from a `simple-content-slider.json` file.
*   **Customizable Autoplay:** Control autoplay speed.
*   **DOM Placement Control:** Optionally move the slider into a specified target HTML element on the page.
*   **Slick Carousel Powered:** Utilizes the popular and robust Slick Carousel library (via CDN).
*   **Standard WordPress Button Styling:** Slide links are styled as default WordPress outline buttons.
*   **Lightweight:** Minimal CSS and JS, relying on CDN for the core slider library.
*   **Translation Ready:** Includes a `.pot` file for easy localization.

## Installation

1.  **Upload to WordPress:**
    *   Download the plugin ZIP file (if from GitHub, you might get it from the "Releases" page or clone the repository and ZIP it yourself).
    *   Go to your WordPress admin area: `Plugins` > `Add New`.
    *   Click `Upload Plugin` and choose the ZIP file.
    *   Activate the plugin through the 'Plugins' menu in WordPress.
2.  **Manual Installation (via FTP):**
    *   Upload the `simple-content-slider` folder to the `/wp-content/plugins/` directory.
    *   Activate the plugin through the 'Plugins' menu in WordPress.
3.  **Prepare `simple-content-slider.json` (Optional):**
    *   If you plan to use the JSON method for defining slides, create a file named `simple-content-slider.json` in the root directory of the plugin (e.g., `/wp-content/plugins/simple-content-slider/simple-content-slider.json`).
    *   The structure of `simple-content-slider.json` should be an array of slide objects:
```
[
{ "title": "Feature Highlight 1", "subtitle": "Discover our new product", "url": "https://yourwebsite. com/ product1"  },
{ "title": "Latest Blog Post", "subtitle": "Read our insights on XYZ", "url": "https://yourwebsite. com/ blog/ latest- post"  },
{ "title": "Special Announcement", "subtitle": "Something exciting is coming!" // "url": "" // URL is optional; if omitted or empty, title is displayed without a link }
]
```
*   `title`: (Required) The main text for the slide.
        *   `subtitle`: (Optional) Additional descriptive text.
        *   `url`: (Optional) The destination URL. If provided, the title becomes a clickable link styled as a button.

## Usage

Use the `[simple_content_slider]` shortcode in the content of any post or page where you want the slider to appear.

### Shortcode Attributes:

*   **`slideX_title`** (string, required for each slide defined via shortcode):
    *   The main title for slide number `X` (e.g., `slide1_title`, `slide2_title`).
*   **`slideX_subtitle`** (string, optional):
    *   The subtitle for slide number `X`.
*   **`slideX_url`** (string, optional):
    *   The URL for slide number `X`. If provided, the title will be linked.
*   **`autoplay_speed`** (integer, optional):
    *   The time (in milliseconds) each slide is displayed when autoplay is active.
    *   Default: `4000` (4 seconds).
    *   Example: `autoplay_speed="5000"`
*   **`move_target_selector`** (string, optional):
    *   A CSS selector (e.g., `#my-div`, `.my-class`) of an HTML element on the page where you want the entire slider to be moved (appended). This can be useful for placing the slider in specific layout areas not directly editable via the content editor.
    *   Example: `move_target_selector=".hero-banner-slider-area"`

### Examples:

1.  **Defining slides directly in the shortcode:**
```
[simple_content_slider
slide1_title="Welcome to Our Site" slide1_subtitle="Explore our features" slide1_url="/features"
slide2_title="Latest News" slide2_subtitle="Stay updated" slide2_url="/news" autoplay_speed="5000"  ]
```
2.  **Using `simple-content-slider.json` (no slide attributes in shortcode):**
```
[simple_content_slider autoplay_speed="3500" ]
```
3.  **Moving the slider to a specific element:**
```
[simple_content_slider move_target_selector= " # custom- slider- placeholder" ]
```
## Styling

The plugin includes basic CSS (`css/simple-content-slider.css`) for the slider structure and the default WordPress button styling for links. Slick Carousel's theme CSS is also loaded from a CDN.

You can override these styles in your theme's stylesheet or via the WordPress Customizer (Appearance > Customize > Additional CSS) to customize the appearance of the slider.

Key CSS classes:
*   `.scs-slider-wrapper`: The main wrapper for a slider instance (has a unique ID like `scs-slider-instance-xxxxx`).
*   `.scs-slider-container`: The direct container on which Slick Carousel is initialized.
*   `.scs-slides`: The `<ul>` element containing individual `<li>` slides.
*   `.scs-slide`: An individual slide `<li>` item.
*   `.scs-slide-content`: Wrapper for the text content within a slide.
*   `.scs-slide-title`: The main title text.
*   `.scs-slide-subtitle`: The subtitle text.
*   `.scs-slide-button`: The `div.wp-block-button` wrapping the link.
## JavaScript Customization

The core Slick Carousel initialization happens in `js/simple-content-slider.js`. While many common options are set by default (dots, arrows, autoplay, etc.), you can modify this file if you need to:
*   Change default Slick Carousel options.
*   Add more responsive breakpoints.
*   Implement more advanced Slick features.

Remember that changes directly to the plugin's JavaScript file might be overwritten during plugin updates. For more robust customization, consider dequeuing the plugin's script and enqueuing your own modified version in your theme.

## Filters

The plugin includes the following WordPress filters for developers:

*   **`scs_max_shortcode_slides`**
    *   Allows changing the maximum number of slides the shortcode will parse via `slideX_` attributes.
    *   Default: `10`
    *   Example: `add_filter( 'scs_max_shortcode_slides', function() { return 20; } );`
*   **`scs_json_file_path`**
    *   Allows changing the default path to the `simple-content-slider.json` file.
    *   Default: `SCS_PLUGIN_PATH . '/simple-content-slider.json'`
    *   Example: `add_filter( 'scs_json_file_path', function() { return get_stylesheet_directory() . '/custom-slider-data.json'; } );`

## Localization

This plugin is translation-ready.
*   The text domain is `simple-content-slider`.
*   A `.pot` file is included in the `/languages/` folder for generating new translations.
*   Place your `.po` and `.mo` files in the `/wp-content/plugins/simple-content-slider/languages/` directory, or in `/wp-content/languages/plugins/`.

## Frequently Asked Questions

*   **Q: The slider is not appearing or not working correctly.**
    *   A: Check the following:
        *   Is the shortcode correctly placed on a page or post?
        *   If using shortcode attributes for slides: Are `slideX_title` attributes correctly numbered and present?
        *   If using `simple-content-slider.json`:
            *   Does the file exist in the plugin's root directory (`/wp-content/plugins/simple-content-slider/simple-content-slider.json`) or the path specified by the `scs_json_file_path` filter?
            *   Is the JSON file correctly formatted? Validate it using a JSON linter.
            *   Are there any items in the JSON array, and do they at least have a `title`?
        *   Check your browser's developer console (usually F12) for JavaScript errors. Slick Carousel or other scripts might be conflicting or failing to load.
        *   Ensure your theme is correctly enqueuing jQuery if other scripts rely on it heavily. (WordPress core includes jQuery by default).
        *   If using `move_target_selector`, does the target element exist on the page when the slider script runs?
*   **Q: How can I change the look of the slider arrows and dots?**
    *   A: Slick Carousel's theme CSS provides the basic styling. You can override these CSS rules in your theme to customize them. Inspect the elements in your browser's developer tools to find the specific classes used by Slick for arrows (e.g., `.slick-prev`, `.slick-next`) and dots (e.g., `.slick-dots li button`).

## Changelog

### 1.2 (Your Current Version Date)
*   Switched to loading Slick Carousel from CDN.
*   Made all user-facing strings in PHP translatable.
*   Improved JavaScript to handle multiple slider instances and dynamic `autoplay_speed`.
*   Added `move_target_selector` functionality to move the slider in the DOM.
*   Enhanced plugin header and prepared for better localization.
*   Refined comments to English and general code cleanup.

### 1.1 (Previous Version Date)
*   (Describe changes in 1.1)

### 1.0 (Initial Version Date)
*   Initial release.

## Contributing

Contributions, issues, and feature requests are welcome!
Feel free to check [issues page](https://github.com/antonbil/simple-content-slider/issues).

1.  **Fork the repository** on GitHub.
2.  **Create a new branch** for your feature or fix.
3.  **Make your changes.**
4.  **Test your changes thoroughly.**
5.  **Commit your changes** with clear messages.
6.  **Push to your branch.**
7.  **Create a new Pull Request** against the `main` (or `develop`) branch of the [antonbil/simple-content-slider](https://github.com/antonbil/simple-content-slider) repository.

## Support

For support, please open an issue on the [GitHub repository issues page](https://github.com/antonbil/simple-content-slider/issues).
You can also find more about my work at [familiebil.nl/anton](https://familiebil.nl/anton).

---
