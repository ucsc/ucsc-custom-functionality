# Roadmap

Audit of `ucsc-custom-functionality` @ 2.0.6. Ordered by severity. Items marked **verified** were reproduced locally; others are read-confirmed risks.

## P0 — Broken now

| # | Item | Location |
|---|---|---|
| 1 | **Magazine block never registers.** Constant points at `/build/views/magazine-block`; webpack emits `build/views/magazine-lock` (it mirrors the *source dir*, not the block slug). `register_block_type_from_metadata()` gets a missing `block.json`. Fix: rename `src/views/magazine-lock` → `magazine-block`. **verified** | [Core.php:37](src/Core.php#L37) |
| 2 | **`composer lint` cannot run.** Ruleset declares no `<file>`; phpcs exits `You must supply at least one file or directory`. Add `<file>plugin.php</file>`, `<file>lib</file>`, `<file>src</file>`. **verified** | [.phpcs.xml.dist](.phpcs.xml.dist) |
| 3 | **phpcs fatals when given paths.** WPCS 2.3.0 × PHPCS 3.13.5 on PHP 8.5 → `TypeError` in `ControlStructureSpacingSniff`. Upgrade to `wp-coding-standards/wpcs:^3.1` + `phpcsstandards/phpcsextra`. No PHP linting has run in a long time. **verified** | [composer.json](composer.json) |
| 4 | **`npm run lint` only lints CSS.** `npm run lint:css lint:js` passes `lint:js` as an *argument* to lint:css, not a second script. Use `npm run lint:css && npm run lint:js`. **verified** | [package.json](package.json) |

## P1 — Correctness & security

| # | Item | Location |
|---|---|---|
| 5 | **Unsanitized `$_POST` into outbound URL path.** `$_POST['taxonomy_selected']` is read with no `isset`/`wp_unslash`/sanitize, then concatenated into the news REST path. Editor-controlled path traversal on the outbound request + transient-key poisoning. Auth is inherited from ACF's AJAX handler, so scope is authenticated editors. Whitelist against `News_Block::ALLOWED_TAX`. | [News_Blocks_Hooks.php:83](src/Hooks/News_Blocks_Hooks.php#L83), [:122](src/Hooks/News_Blocks_Hooks.php#L122) |
| 6 | Same unguarded `$_POST` read into `get_terms()`. Lower impact (WP validates taxonomy) but same missing guards. | [Taxonomies_Hooks.php:89](src/Hooks/Taxonomies_Hooks.php#L89) |
| 7 | **Fatal risk:** `in_array( 'embed-post', $query['slug__in'] )` — index unguarded; `get_block_templates` callers frequently omit `slug__in` → `TypeError` on PHP 8. | [Post_Single.php:16](src/Template/Post_Single.php#L16) |
| 8 | **Fatal risk:** `get_current_screen()` may return `null`; `->base` dereferenced unguarded. | [plugin.php:60](plugin.php#L60) |
| 9 | Unguarded index `$this->query_loop[ MANUAL_CARDS ]` in manual query mode. | [Query_Loop_Controller.php](src/Components/Query_Loop_Controller.php) |
| 10 | **Yoast integration is a no-op loop.** Iterates `PRIMARY_TAX_SUPPORT` (5 taxonomies) but `$tax` is never used — only `academics` is ever registered. **verified** | [Integrations_Subscriber.php:17-26](src/Integrations/Integrations_Subscriber.php#L17-L26) |
| 11 | `esc_html__( '', 'ucsc' )` — translating the empty string returns the PO file's metadata header, not `''`. **verified** | [Query_Loop.php:102](src/Blocks/Query_Loop.php#L102) |
| 12 | **Duplicate script handle.** `Assets_Enqueuer` loops every registered UCSC block but enqueues each script under the same constant handle (`index`); only the first block's JS loads. Styles are correctly suffixed per block. **verified** | [Assets_Enqueuer.php](src/Assets/Assets_Enqueuer.php) |
| 13 | `(int) get_field('posts_per_page') ?? self::PER_PAGE` — `??` is dead (a cast never yields null). Missing field → `0` → `array_slice(…, 0, 0)` → empty block. Affects blocks inserted before the field existed. | [News_Block_Controller.php:42](src/Components/News_Block_Controller.php#L42) |

## P2 — Performance & lifecycle

| # | Item | Location |
|---|---|---|
| 14 | **N+1 blocking HTTP on cold render.** One `wp_remote_get` per featured image *plus* one per coauthor, on top of the posts request — up to ~19 sequential calls for a 9-post block. Batch via `_embed` on the posts request. **verified** | [News_Block_Controller.php:132-190](src/Components/News_Block_Controller.php#L132-L190) |
| 15 | **No `timeout` on any remote request** — defaults to 5s each, so #14 can stall a page render for tens of seconds. Set an explicit short timeout. | [News_Request.php:17](src/Request/News_Request.php#L17) |
| 16 | **Cache keys embed `taxonomy_ids`,** so the same media/coauthor is cached separately per block configuration. Key those by object ID only. **verified** | [News_Block_Controller.php:125-130](src/Components/News_Block_Controller.php#L125-L130) |
| 17 | **No activation/deactivation/uninstall hooks at all.** CPT registers a `photo-of-the-week` rewrite with no flush (404s until permalinks are re-saved); `wp_template` posts are created but never removed; transients never cleaned. **verified** | plugin-wide |
| 18 | Custom templates are bound to the `wp_theme` term `ucsc-2022`. Theme rename/switch orphans them silently. | [Template.php:14](src/Template/Template.php#L14) |
| 19 | `News_Request::request()` recurses over paginated results with no page cap. A large `X-WP-TotalPages` means unbounded sequential fetches. | [News_Request.php:30-40](src/Request/News_Request.php#L30-L40) |

## P3 — Tooling, packaging, docs

| # | Item | Location |
|---|---|---|
| 20 | **Declared PHP floor is wrong.** Code uses union types (`array|string`), so the real minimum is PHP 8.0; CONTRIBUTING says 7.4+, phpcs is configured for WP 4.9. Add `Requires PHP: 8.0` / `Requires at least:` headers and align the docs. | [News_Block_Controller.php:27](src/Components/News_Block_Controller.php#L27), [plugin.php](plugin.php) |
| 21 | **Text domain `ucsc` is never loaded** and there is no `Text Domain:`/`Domain Path:` header. All `__()` calls are untranslatable. | [plugin.php](plugin.php) |
| 22 | Settings page hardcodes `WP_PLUGIN_DIR . '/ucsc-custom-functionality/plugin.php'`; breaks if the directory is renamed. Use `plugin_dir_path()` / a constant. | [settings.php:42](lib/functions/settings.php#L42) |
| 23 | **No CI lint gate.** `release.yml` only builds on tag push. Add a PR workflow running phpcs + both JS/CSS linters (after P0-2/3/4 land). | [.github/workflows/](.github/workflows/) |
| 24 | No test suite. Nothing enforces the block-registration path contract that broke #1 — a smoke test asserting every `Core::BLOCKS_*` path resolves to a real `block.json` would have caught it. | — |
| 25 | `echo _e( … )` — redundant (`_e` already echoes). Harmless, phpcs-flagged. | [news-block/index.php:37](src/views/news-block/index.php#L37) |
| 26 | Legacy `lib/functions/` and namespaced `src/` maintain two parallel conventions. Consider folding the procedural files behind a loader registered from `Core`, keeping the `ucsc_` prefix contract. | [lib/functions/](lib/functions/) |
| 27 | GTM container ID and SiteImprove script ID are hardcoded; no per-site override. | [ga.php](lib/functions/scripts/ga.php), [site-improve.php](lib/functions/scripts/site-improve.php) |

## Suggested sequence

1. **Unblock quality gates** — #2, #3, #4. Nothing else is safely verifiable until linting runs.
2. **Ship the visible fix** — #1, plus #12 (both are "the block doesn't work" bugs).
3. **Harden** — #5, #6, #7, #8, #9.
4. **Lifecycle** — #17, then #14/#15/#16 as one caching pass.
5. **Gate it** — #23, #24, so P0 regressions can't recur.
