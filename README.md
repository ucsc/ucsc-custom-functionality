# UCSC Custom Functionality

[![Build and release](https://github.com/ucsc/ucsc-custom-functionality/actions/workflows/release.yml/badge.svg)](https://github.com/ucsc/ucsc-custom-functionality/actions/workflows/release.yml)

A WordPress plugin that provides theme-independent custom functionality for UC Santa Cruz websites. By keeping features like custom post types, blocks, shortcodes, and admin customizations separate from the [UCSC theme](https://github.com/ucsc/theme-ucsc), future theme changes won't affect site functionality.

## All Sites

These features are active on every UCSC WordPress site using the plugin.

### Admin Customizations

- Removes the deprecated Link Manager from the dashboard sidebar
- Hides the Customizer from the admin bar for users without theme editing capabilities

### Analytics & Security

- **Google Tag Manager** integration for site analytics
- **SiteImprove** analytics and accessibility check script
- **XML-RPC security** — disabled by default with an admin toggle to re-enable when external editing tools are needed

### Shortcodes

- `[site-search]` — Embeds a Google Site Search results page with context-aware domain detection
- `[copyright]` — Displays a copyright symbol with the current year
- `[last-modified]` — Shows a post's last modified date (or creation date as a fallback)

### Settings Page

An admin settings page under **Settings > UCSC Custom Functionality** provides:

- XML-RPC enable/disable toggle
- Plugin version and feature overview

### News Block

The **News** block is available on all sites. It displays news articles with configurable layout, taxonomy filtering, and visibility toggles for image, date, excerpt, author, and tags. Built with Advanced Custom Fields and registered via `block.json`.

## News Sites Only

The following features require the `UCSC_NEWS_SITE` constant to be defined as `true`. They provide content management tools tailored to the UCSC news site.

### Custom Blocks (ACF)

| Block | Description |
|-------|-------------|
| **Featured News** | Query-based block for featured stories with a CTA link |
| **Magazine** | Two-line title/subtitle layout with repeating items (title, byline, image, description, CTA) |
| **Photo of the Week** | Single photo selector with title and "All Photos" CTA |
| **Press Inquiries** | Up to 2 inquiry contacts (name, email, phone) plus media file and description |
| **Media Coverage** | Query loop for the media coverage post type |
| **Related Stories** | Displays up to 3 manually selected or automatically queried related posts |
| **Post Header** | Configurable post header layout (small or large image) |

Query-based blocks support three query modes: **latest posts**, **automatic** (pull from a taxonomy), or **manual** (select posts individually). Results are cached with 20-minute transients.

### Custom Post Types

- **Photo of the Week** (`photo-of-the-week`) — A post type with custom fields for photographer credit and image, with a public archive and REST API support

### Custom Templates

- **Post Single** — Custom single post template
- **Photo of the Week Archive** — Custom archive template for the Photo of the Week post type

### Integrations

- **Yoast SEO** — Registers primary term support for UCSC-specific taxonomies (academics, administration, person, section, kind)
- **Co-Authors Plus** — Adjusts the author block to support multiple authors
- **ACF** — Registers a simplified WYSIWYG toolbar (bold, italic, link) used by block fields

## Requirements

- WordPress 5.5+
- [Advanced Custom Fields PRO](https://www.advancedcustomfields.com/pro/)
- Define `UCSC_NEWS_SITE` as `true` to enable news-specific blocks, post types, templates, and integrations

## Project Structure

```
lib/functions/      Legacy procedural functionality (admin menus, scripts, settings, shortcodes)
src/
  Blocks/           ACF block field group definitions
  Components/       Block rendering controllers (data preparation for views)
  Hooks/            ACF field hooks for dynamic taxonomy/term loading
  Integrations/     Third-party plugin integrations (Yoast, ACF toolbars)
  Object_Meta/      Custom post meta field definitions
  Post_Types/       Custom post type registrations
  Query/            Query modifications
  Template/         Custom block template registrations and render filters
  views/            Block view templates and block.json files
  Core.php          Plugin bootstrap and service registration
```
