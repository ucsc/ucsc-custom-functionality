# Contributing to UCSC Custom Functionality

Thank you for contributing to the **UCSC Custom Functionality** WordPress plugin! This document provides guidelines and instructions for setting up a local development environment, working with the codebase, and submitting contributions.

---

## Table of Contents

- [About This Plugin](#about-this-plugin)
- [Repository Structure](#repository-structure)
- [Development Prerequisites](#development-prerequisites)
- [Local Development Setup](#local-development-setup)
- [Available Scripts](#available-scripts)
- [Coding Standards](#coding-standards)
- [Branching Strategy](#branching-strategy)
- [Submitting a Pull Request](#submitting-a-pull-request)
- [Releasing a New Version](#releasing-a-new-version)

---

## About This Plugin

`ucsc-custom-functionality` is a WordPress plugin that adds theme-independent functionality to UC Santa Cruz websites, including:

- **Admin customizations** – removes deprecated/unwanted items from the WordPress dashboard and admin bar.
- **Shortcodes** – `[site-search]`, `[copyright]`, `[last-modified]`.
- **Scripts** – Google Tag Manager, SiteImprove analytics, XML-RPC disabling, and block editor variations.
- **Custom Gutenberg blocks** – News, Featured News, Photo of the Week, Media Coverage, Magazine, Related Stories, Press Inquiries, and Post Header blocks (ACF-dependent).
- **Settings page** – A plugin info/settings page under *Settings → UCSC Custom Functionality*.

---

## Repository Structure

```
ucsc-custom-functionality/
├── .github/
│   ├── CONTRIBUTING.md          # This file
│   ├── PULL_REQUEST_TEMPLATE.md # PR template
│   └── workflows/
│       └── release.yml          # Build & release GitHub Action
├── assets/
│   └── js/                      # Compiled/static JS assets (e.g., block variations)
├── lib/
│   ├── css/                     # Admin CSS
│   └── functions/               # PHP function files loaded by plugin.php
│       ├── admin-menus.php
│       ├── scripts.php
│       ├── settings.php
│       ├── shortcodes.php
│       └── scripts/
│           ├── disable-xmlrpc.php
│           ├── ga.php
│           └── site-improve.php
├── src/                         # PSR-4 autoloaded PHP classes (namespace UCSC\Blocks\)
│   └── Core.php                 # Plugin bootstrap / block registration
├── plugin.php                   # WordPress plugin entry point
├── composer.json                # PHP dependencies & autoloading
├── package.json                 # Node dependencies & npm scripts
├── webpack.config.js            # Webpack configuration (via @wordpress/scripts)
├── .phpcs.xml.dist              # PHP CodeSniffer ruleset
├── .stylelintrc.json            # Stylelint configuration
├── .editorconfig                # Editor formatting rules
└── .nvmrc                       # Node version pin
```

---

## Development Prerequisites

| Tool | Version | Notes |
|------|---------|-------|
| **PHP** | 7.4+ | Required for WordPress and Composer |
| **Composer** | 2.x | PHP dependency management |
| **Node.js** | 20 (see `.nvmrc`) | JavaScript tooling |
| **npm** | bundled with Node | Package management |
| **WordPress** | 4.9+ | Minimum supported version |
| **ACF (Advanced Custom Fields)** | any | Required for custom blocks |

> **Tip:** Use [nvm](https://github.com/nvm-sh/nvm) to manage Node versions. Run `nvm use` in the project root to automatically switch to the correct version specified in `.nvmrc`.

---

## Local Development Setup

1. **Clone the repository** into your WordPress plugins directory (or symlink it):

   ```bash
   git clone https://github.com/ucsc/ucsc-custom-functionality.git wp-content/plugins/ucsc-custom-functionality
   cd wp-content/plugins/ucsc-custom-functionality
   ```

2. **Install PHP dependencies** (for linting; vendor is excluded from production builds):

   ```bash
   composer install
   ```

3. **Install Node dependencies:**

   ```bash
   nvm use        # switch to the pinned Node version
   npm install
   ```

4. **Start the development build watcher:**

   ```bash
   npm run dev
   ```

5. **Activate the plugin** in your local WordPress admin under *Plugins → Installed Plugins*.

---

## Available Scripts

### Node / npm

| Command | Description |
|---------|-------------|
| `npm run dev` | Start Webpack in watch mode (hot-reloads block assets) |
| `npm run build` | Production build of block assets into `build/` |
| `npm run lint:css` | Lint and auto-fix SCSS files in `src/` |
| `npm run lint:js` | Lint and auto-fix JS files in `src/` |
| `npm run lint` | Run both CSS and JS linters |
| `npm run zip` | Package the plugin into a `.zip` for distribution |
| `npm run release` | Bump version with `standard-version` and generate CHANGELOG entry |
| `npm run packages-update` | Update `@wordpress/scripts` and related packages |

### Composer / PHP

| Command | Description |
|---------|-------------|
| `composer lint` | Run PHP CodeSniffer against the codebase |
| `composer lint-fix` | Auto-fix PHP CodeSniffer violations with PHPCBF |

---

## Coding Standards

This project follows **WordPress Coding Standards** enforced via PHP CodeSniffer.

### PHP

- Adhere to [WordPress PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/).
- All functions, classes, and globals **must** be prefixed with `ucsc_` or use the `UCSC\Blocks\` namespace.
- All PHP files in `lib/` must include a proper file-level docblock (see existing files for examples).
- Minimum supported WordPress version: **4.9**.
- Use `strict_types=1` declarations in namespaced class files (see `src/Core.php`).

Run the linter before committing:
```bash
composer lint
```

Auto-fix where possible:
```bash
composer lint-fix
```

### JavaScript

- Follow the [@wordpress/eslint-plugin](https://www.npmjs.com/package/@wordpress/eslint-plugin) rules (enforced via `@wordpress/scripts`).
- Block variations and editor scripts live in `assets/js/` or `src/`.

### CSS / SCSS

- Follow [WordPress CSS Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/css/).
- Configuration is in `.stylelintrc.json`.

### Editor Config

An `.editorconfig` file is included. Please ensure your editor respects it (most editors support this via a free plugin).

---

## Branching Strategy

| Branch | Purpose |
|--------|---------|
| `main` | Stable production code; protected branch |
| `feature/<short-description>` | New features |
| `fix/<short-description>` | Bug fixes |
| `chore/<short-description>` | Maintenance, dependency updates, refactors |

Branch off of `main` for all new work:

```bash
git checkout main
git pull origin main
git checkout -b feature/my-new-feature
```

---

## Submitting a Pull Request

1. Ensure your branch is up to date with `main`.
2. Run linters and fix any issues:
   ```bash
   composer lint
   npm run lint
   ```
3. Build assets to confirm nothing is broken:
   ```bash
   npm run build
   ```
4. Push your branch and open a Pull Request against `main`.
5. Fill out the **PR template** completely:
   - Describe what the PR does or fixes.
   - Include testing instructions and expected outcomes.
6. Request a review from a team member.
7. Address review feedback; do **not** force-push after a review has started.

---

## Releasing a New Version

Releases are automated via the **Build and release** GitHub Actions workflow (`.github/workflows/release.yml`), which triggers on version tags.

### Steps to release

1. **Bump the version** using `standard-version` (updates `package.json`, `package-lock.json`, `plugin.php`, and `CHANGELOG.md`):

   ```bash
   npm run release
   ```

   For a release candidate:
   ```bash
   npm run release -- --prerelease rc
   ```

2. **Push the commit and the generated tag:**

   ```bash
   git push --follow-tags origin main
   ```

3. The GitHub Action will automatically:
   - Install Composer and Node dependencies.
   - Build plugin assets.
   - Package the plugin as `ucsc-custom-functionality.zip`.
   - Create a GitHub Release with auto-generated release notes and the zip attached.

### Version tag format

- Stable release: `v1.2.3`
- Release candidate: `v1.2.3-rc.1`

---

## Questions?

Open an [issue](https://github.com/ucsc/ucsc-custom-functionality/issues) or reach out to the UC Santa Cruz web team.
