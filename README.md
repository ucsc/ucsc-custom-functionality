# UCSC functionality for WordPress sites

This is WordPress plugin contains custom functionality for UCSC websites. The concept is to keep features of a site that are theme independent, such as custom post-types, taxonomies, and roles separate from the [UCSC theme code](https://github.com/ucsc/theme-ucsc). This will ensure that future theme changes do not affect a site's functionality.

## Features

-   `admin-menus.php` - Customizes the admin area to remove unwanted menus from the Dashboard and front-facing Admin menu.
-   `shortcodes.php` - Custom short codes.
-   `scripts.php` - Google Analytics, SiteImprove, Security Headers, and Disable XMLRPC
