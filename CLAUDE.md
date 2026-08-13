# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
nvm use              # Node 20.18.0 (.nvmrc)
npm install && composer install

npm run dev          # webpack watch
npm run build        # production build into build/ (required — see "Build output" below)
npm run lint:js      # eslint + --fix on src/**/*.js
npm run lint:css     # stylelint + --fix on src/**/*.scss
composer lint        # phpcs (WordPress-Extra + WordPress-Docs, .phpcs.xml.dist)
composer lint-fix    # phpcbf

npm run release      # commit-and-tag-version: bumps package.json, package-lock.json,
                     # plugin.php header (via wp-plugin-version-updater.js), CHANGELOG.md
git push --follow-tags origin main   # tag push triggers .github/workflows/release.yml
```

There is **no test suite**. Verification is: `composer lint`, `npm run lint:js`/`lint:css`, `npm run build`, then manual check in a WordPress install.

Release CI delegates to the shared reusable workflow `ucsc/actions/.github/workflows/release.yml@v1` — build/packaging changes usually belong in that repo, not here. Tags: `v1.2.3` or `v1.2.3-rc.1`.

## Runtime requirements

- **ACF PRO is a hard dependency.** `plugin.php` only calls `Core::instance()->init()` on `plugins_loaded` (priority 100) if `acf_add_local_field_group()` exists. Without ACF, nothing in `src/` runs.
- **`UCSC_NEWS_SITE`** — when defined `true`, unlocks the news-only blocks, the Photo of the Week post type, custom templates, query/integration subscribers, and object meta. Every gate routes through `Core::is_news_site()`. Only the News block and the `lib/` procedural features are active on non-news sites.

## Architecture

Two layers coexist:

- **`lib/functions/`** — legacy procedural code (admin menus, settings page, shortcodes, GTM/SiteImprove/xmlrpc scripts), included directly by [plugin.php](plugin.php). Global functions here must be `ucsc_`-prefixed and wrapped in `function_exists()` guards (phpcs enforces the prefix).
- **`src/`** — PSR-4 `UCSC\Blocks\` (note: namespace root is `Blocks`, so `src/Blocks/Foo.php` is `UCSC\Blocks\Blocks\Foo`). [src/Core.php](src/Core.php) is the singleton bootstrap and the registry of every block.

### The ACF block triad

Each custom block is three coordinated pieces:

| Piece | Location | Role |
|---|---|---|
| Field group | `src/Blocks/<Name>_Block.php` | extends `ACF_Group` (or `Query_Loop`); defines ACF fields and the `block` location rule |
| Controller | `src/Components/<Name>_Block_Controller.php` | reads `get_field()` into typed props, queries/prepares data for the view |
| View | `src/views/<dir>/` | `block.json` + `index.php` render template + `index.js` + `style.scss`/`editor.scss` |

`block.json` declares `"acf": { "mode": "preview", "renderTemplate": "index.php" }`. Registration happens in `Core::init_blocks()` via `register_block_type_from_metadata()` against **`build/views/<dir>/block.json`**, with `render_callback` pointed at `Core::render_template()`.

`Core::render_template()` rewrites `build/views/` back to `src/views/` before including the template ([src/Core.php:69-78](src/Core.php#L69-L78)). Consequence: **PHP view edits take effect immediately; JS/SCSS edits require a build.** The `index.php` in `build/` is never executed.

ACF field *names* are class constants on the block class (`News_Block::TITLE`, etc.) and are referenced by both the field group and the controller — never hardcode the string in one place and the constant in the other. Field *keys* are composed by `With_Get_Field_Key::get_field_key( $name, $group_name )` → `"{group}_{name}"`; ACF filters like `acf/fields/select/query/key=…` in `src/Hooks/` depend on that exact composition.

### Adding a block

1. `src/views/<dir>/` with `block.json` (name `ucsc-custom-functionality/<slug>`), `index.php`, `index.js`, `style.scss`, `editor.scss`.
2. Field-group class in `src/Blocks/`, controller in `src/Components/`.
3. Register in `Core::BLOCKS_PUBLIC` (all sites) or `Core::BLOCKS_NEWS_ONLY` (news only).

The path in those constants **must match the `src/views/` directory name** — webpack mirrors the source directory into `build/views/<same-name>/`, not the block.json slug. A mismatch silently skips registration, with no error anywhere. This has bitten once already: a bulk `snake_case` → `kebab-case` rename produced `src/views/magazine-lock` against a `magazine-block` constant, and the Magazine block was unregistered for four releases before anyone noticed.

### Query-loop blocks

`Blocks\Query_Loop` (field side) + `Components\Query_Loop_Controller` (data side) implement three editor-selectable modes: `latest`, `automatic` (taxonomy term), `manual` (post_object repeater). Subclasses override `$number_of_posts_display`, `$post_types`, `$max_manual_cards`, and implement `prepare_posts_for_display()`.

### The News block is remote

Unlike every other block, `News_Block_Controller` pulls posts over REST from the news site via `Request\News_Request`, which picks its base URL from `wp_get_environment_type()` (production → `news.ucsc.edu`, staging/development → the Pantheon envs). Taxonomy/term choices for the editor are loaded through `Hooks\News_Blocks_Hooks` and cached in transients for 20 minutes, keyed by field key. When editor dropdowns look stale, delete those transients.

### Assets

`Assets\Assets_Enqueuer` does not enqueue per registered class — it scans `WP_Block_Type_Registry` for names containing `ucsc-custom-functionality/`, then loads `build/views/<last-segment-of-block-name>/index.asset.php` for version/deps. So the block.json slug's last segment must also match the build directory name.

Two extra webpack entries in [webpack.config.js](webpack.config.js) compile `assets/scss/blocks/*.scss` into `build/css/`, attached to *core* blocks via `wp_enqueue_block_style()` in `Assets_Subscriber` (`core/post-terms`, `outermost/social-sharing`).

### Templates and core-block filtering (news sites only)

`Template\Template` subclasses (`Post_Single`, `Photo_Of_The_Week_Archive`) inject block templates through the `get_block_templates` filter, creating a `wp_template` post from `src/views/templates/*.html` on first load and tagging it with the `wp_theme` term `ucsc-2022` (`Template::NAMESPACE`). That namespace ties the templates to the UCSC theme; changing themes orphans them.

`Template\Blocks_Render` (via `Template_Subscriber`) filters `render_block` to rewrite core block output — Co-Authors Plus in `core/post-author-name`, caption/description appended to `core/post-featured-image`, wrappers on `core/post-terms` and `outermost/social-sharing`.

## Conventions

- `declare(strict_types=1)` on all `src/` files; `lib/` files need a file-level docblock.
- Text domain is `ucsc`; phpcs `minimum_supported_wp_version` is 4.9.
- `build/` and `vendor/` are gitignored but both ship in the release zip (`package.json` `files`, `.deployignore`).
- Branches: `feature/…`, `fix/…`, `chore/…` off `main`; commit messages follow Conventional Commits (CHANGELOG is generated from them).
