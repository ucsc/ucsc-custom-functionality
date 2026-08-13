# Roadmap

Audit of `ucsc-custom-functionality` @ 2.0.6. Ordered by severity. Items marked **verified** were reproduced locally; others are read-confirmed risks.

Every item is tracked by a GitHub issue in the **Issue** column. Several issues cover more than one item where the fixes belong in a single PR.

**Status: 7 of 27 items resolved.** The **Status** column reflects the code on `main`, not whether the tracking issue is closed — an issue covering several items stays open until all of them land. Items found *after* the audit are listed under [Found since the audit](#found-since-the-audit).

## P0 — Broken now

| # | Item | Location | Issue | Status |
|---|---|---|---|---|
| 1 | **Magazine block never registers.** Constant points at `/build/views/magazine-block`; webpack emits `build/views/magazine-lock` (it mirrors the *source dir*, not the block slug). `register_block_type_from_metadata()` gets a missing `block.json`. Fix: rename `src/views/magazine-lock` → `magazine-block`. **verified** | [Core.php:37](src/Core.php#L37) | [#100](https://github.com/ucsc/ucsc-custom-functionality/issues/100) | ✅ Done |
| 2 | **`composer lint` cannot run.** Ruleset declares no `<file>`; phpcs exits `You must supply at least one file or directory`. Add `<file>plugin.php</file>`, `<file>lib</file>`, `<file>src</file>`. **verified** | [.phpcs.xml.dist](.phpcs.xml.dist) | [#101](https://github.com/ucsc/ucsc-custom-functionality/issues/101) | ✅ Done |
| 3 | **phpcs fatals when given paths.** WPCS 2.3.0 × PHPCS 3.13.5 on PHP 8.5 → `TypeError` in `ControlStructureSpacingSniff`. Upgrade to `wp-coding-standards/wpcs:^3.1` + `phpcsstandards/phpcsextra`. No PHP linting has run in a long time. **verified** | [composer.json](composer.json) | [#101](https://github.com/ucsc/ucsc-custom-functionality/issues/101) | ✅ Done |
| 4 | **`npm run lint` only lints CSS.** `npm run lint:css lint:js` passes `lint:js` as an *argument* to lint:css, not a second script. Use `npm run lint:css && npm run lint:js`. **verified** | [package.json](package.json) | [#101](https://github.com/ucsc/ucsc-custom-functionality/issues/101) | ✅ Done |

## P1 — Correctness & security

| # | Item | Location | Issue | Status |
|---|---|---|---|---|
| 5 | **Unsanitized `$_POST` into outbound URL path.** `$_POST['taxonomy_selected']` is read with no `isset`/`wp_unslash`/sanitize, then concatenated into the news REST path. Editor-controlled path traversal on the outbound request + transient-key poisoning. Auth is inherited from ACF's AJAX handler, so scope is authenticated editors. Whitelist against `News_Block::ALLOWED_TAX`. | [News_Blocks_Hooks.php:83](src/Hooks/News_Blocks_Hooks.php#L83), [:122](src/Hooks/News_Blocks_Hooks.php#L122) | [#103](https://github.com/ucsc/ucsc-custom-functionality/issues/103) | Open |
| 6 | Same unguarded `$_POST` read into `get_terms()`. Lower impact (WP validates taxonomy) but same missing guards. | [Taxonomies_Hooks.php:89](src/Hooks/Taxonomies_Hooks.php#L89) | [#103](https://github.com/ucsc/ucsc-custom-functionality/issues/103) | Open |
| 7 | **Fatal risk:** `in_array( 'embed-post', $query['slug__in'] )` — index unguarded; `get_block_templates` callers frequently omit `slug__in` → `TypeError` on PHP 8. | [Post_Single.php:16](src/Template/Post_Single.php#L16) | [#104](https://github.com/ucsc/ucsc-custom-functionality/issues/104) | Open |
| 8 | **Fatal risk:** `get_current_screen()` may return `null`; `->base` dereferenced unguarded. | [plugin.php:60](plugin.php#L60) | [#104](https://github.com/ucsc/ucsc-custom-functionality/issues/104) | Open |
| 9 | Unguarded index `$this->query_loop[ MANUAL_CARDS ]` in manual query mode. | [Query_Loop_Controller.php](src/Components/Query_Loop_Controller.php) | [#104](https://github.com/ucsc/ucsc-custom-functionality/issues/104) | Open |
| 10 | **Yoast integration is a no-op loop.** Iterates `PRIMARY_TAX_SUPPORT` (5 taxonomies) but `$tax` is never used — only `academics` is ever registered. **verified** | [Integrations_Subscriber.php:17-26](src/Integrations/Integrations_Subscriber.php#L17-L26) | [#105](https://github.com/ucsc/ucsc-custom-functionality/issues/105) | Open |
| 11 | `esc_html__( '', 'ucsc' )` — translating the empty string returns the PO file's metadata header, not `''`. **verified** | [Query_Loop.php:102](src/Blocks/Query_Loop.php#L102) | [#106](https://github.com/ucsc/ucsc-custom-functionality/issues/106) | ✅ Done |
| 12 | **Duplicate script handle.** `Assets_Enqueuer` loops every registered UCSC block but enqueues each script under the same constant handle (`index`); only the first block's JS loads. Styles are correctly suffixed per block. **verified** | [Assets_Enqueuer.php](src/Assets/Assets_Enqueuer.php) | [#102](https://github.com/ucsc/ucsc-custom-functionality/issues/102) | ✅ Done |
| 13 | `(int) get_field('posts_per_page') ?? self::PER_PAGE` — `??` is dead (a cast never yields null). Missing field → `0` → `array_slice(…, 0, 0)` → empty block. Affects blocks inserted before the field existed. | [News_Block_Controller.php:42](src/Components/News_Block_Controller.php#L42) | [#106](https://github.com/ucsc/ucsc-custom-functionality/issues/106) | Open |

## P2 — Performance & lifecycle

| # | Item | Location | Issue | Status |
|---|---|---|---|---|
| 14 | **N+1 blocking HTTP on cold render.** One `wp_remote_get` per featured image *plus* one per coauthor, on top of the posts request — up to ~19 sequential calls for a 9-post block. Batch via `_embed` on the posts request. **verified** | [News_Block_Controller.php:132-190](src/Components/News_Block_Controller.php#L132-L190) | [#107](https://github.com/ucsc/ucsc-custom-functionality/issues/107) | Open |
| 15 | **No `timeout` on any remote request** — defaults to 5s each, so #14 can stall a page render for tens of seconds. Set an explicit short timeout. | [News_Request.php:17](src/Request/News_Request.php#L17) | [#107](https://github.com/ucsc/ucsc-custom-functionality/issues/107) | Open |
| 16 | **Cache keys embed `taxonomy_ids`,** so the same media/coauthor is cached separately per block configuration. Key those by object ID only. **verified** | [News_Block_Controller.php:125-130](src/Components/News_Block_Controller.php#L125-L130) | [#107](https://github.com/ucsc/ucsc-custom-functionality/issues/107) | Open |
| 17 | **No activation/deactivation/uninstall hooks at all.** CPT registers a `photo-of-the-week` rewrite with no flush (404s until permalinks are re-saved); `wp_template` posts are created but never removed; transients never cleaned. **verified** | plugin-wide | [#108](https://github.com/ucsc/ucsc-custom-functionality/issues/108) | Open |
| 18 | Custom templates are bound to the `wp_theme` term `ucsc-2022`. Theme rename/switch orphans them silently. | [Template.php:14](src/Template/Template.php#L14) | [#109](https://github.com/ucsc/ucsc-custom-functionality/issues/109) | Open |
| 19 | `News_Request::request()` recurses over paginated results with no page cap. A large `X-WP-TotalPages` means unbounded sequential fetches. | [News_Request.php:30-40](src/Request/News_Request.php#L30-L40) | [#107](https://github.com/ucsc/ucsc-custom-functionality/issues/107) | Open |

## P3 — Tooling, packaging, docs

| # | Item | Location | Issue | Status |
|---|---|---|---|---|
| 20 | **Declared PHP floor is wrong.** Code uses union types (`array\|string`), so the real minimum is PHP 8.0; CONTRIBUTING says 7.4+, phpcs is configured for WP 4.9. Add `Requires PHP: 8.0` / `Requires at least:` headers and align the docs. | [News_Block_Controller.php:27](src/Components/News_Block_Controller.php#L27), [plugin.php](plugin.php) | [#110](https://github.com/ucsc/ucsc-custom-functionality/issues/110) | Open |
| 21 | **Text domain `ucsc` is never loaded** and there is no `Text Domain:`/`Domain Path:` header. All `__()` calls are untranslatable. | [plugin.php](plugin.php) | [#110](https://github.com/ucsc/ucsc-custom-functionality/issues/110) | Open |
| 22 | Settings page hardcodes `WP_PLUGIN_DIR . '/ucsc-custom-functionality/plugin.php'`; breaks if the directory is renamed. Use `plugin_dir_path()` / a constant. | [settings.php:42](lib/functions/settings.php#L42) | [#106](https://github.com/ucsc/ucsc-custom-functionality/issues/106) | Open |
| 23 | **No CI lint gate.** `release.yml` only builds on tag push. Add a PR workflow running phpcs + both JS/CSS linters (after P0-2/3/4 land). | [.github/workflows/](.github/workflows/) | [#111](https://github.com/ucsc/ucsc-custom-functionality/issues/111) | Open |
| 24 | No test suite. Nothing enforces the block-registration path contract that broke #1 — a smoke test asserting every `Core::BLOCKS_*` path resolves to a real `block.json` would have caught it. | — | [#111](https://github.com/ucsc/ucsc-custom-functionality/issues/111) | Open |
| 25 | `echo _e( … )` — redundant (`_e` already echoes). Harmless, phpcs-flagged. | [news-block/index.php:37](src/views/news-block/index.php#L37) | [#106](https://github.com/ucsc/ucsc-custom-functionality/issues/106) | ✅ Done |
| 26 | Legacy `lib/functions/` and namespaced `src/` maintain two parallel conventions. Consider folding the procedural files behind a loader registered from `Core`, keeping the `ucsc_` prefix contract. | [lib/functions/](lib/functions/) | [#112](https://github.com/ucsc/ucsc-custom-functionality/issues/112) | Open |
| 27 | GTM container ID and SiteImprove script ID are hardcoded; no per-site override. | [ga.php](lib/functions/scripts/ga.php), [site-improve.php](lib/functions/scripts/site-improve.php) | [#113](https://github.com/ucsc/ucsc-custom-functionality/issues/113) | Open |

## Suggested sequence

1. ✅ **Unblock quality gates** — #2, #3, #4 ([#101](https://github.com/ucsc/ucsc-custom-functionality/issues/101)). Nothing else is safely verifiable until linting runs.
2. **Ship the visible fix** — ✅ #1 ([#100](https://github.com/ucsc/ucsc-custom-functionality/issues/100)); still open: #12 ([#102](https://github.com/ucsc/ucsc-custom-functionality/issues/102)) — both are "the block doesn't work" bugs.
3. **Harden** — #5, #6 ([#103](https://github.com/ucsc/ucsc-custom-functionality/issues/103)); #7, #8, #9 ([#104](https://github.com/ucsc/ucsc-custom-functionality/issues/104)).
4. **Lifecycle** — #17 ([#108](https://github.com/ucsc/ucsc-custom-functionality/issues/108)), then #14/#15/#16/#19 as one caching pass ([#107](https://github.com/ucsc/ucsc-custom-functionality/issues/107)).
5. **Gate it** — #23, #24 ([#111](https://github.com/ucsc/ucsc-custom-functionality/issues/111)), so P0 regressions can't recur.

Remaining items not on the critical path: [#105](https://github.com/ucsc/ucsc-custom-functionality/issues/105), [#106](https://github.com/ucsc/ucsc-custom-functionality/issues/106), [#109](https://github.com/ucsc/ucsc-custom-functionality/issues/109), [#110](https://github.com/ucsc/ucsc-custom-functionality/issues/110), [#112](https://github.com/ucsc/ucsc-custom-functionality/issues/112), [#113](https://github.com/ucsc/ucsc-custom-functionality/issues/113).

Step 1 is done, and step 2 is half done. **Step 3 is now the front of the queue** — items #5 and #6 are the only remaining findings that phpcs still reports, as six `WordPress.Security.NonceVerification` errors on the same lines as the missing input guards.

Step 5 was written on the assumption that a CI gate would have to run report-only until the lint backlog cleared. That backlog is gone (see below), so a gate that fails the build is now viable; [#111](https://github.com/ucsc/ucsc-custom-functionality/issues/111) should be re-read before it is picked up.

## Lint backlog

Not a numbered audit item, but the direct consequence of #2/#3/#4: once the linters ran, they reported a large accumulated backlog. Burned down in [#116](https://github.com/ucsc/ucsc-custom-functionality/issues/116) across five steps.

| | phpcs | eslint | stylelint |
|---|---|---|---|
| Before | 1345 errors / 31 warnings | 135 errors | clean |
| Now | 6 errors / 0 warnings | 0 | clean |

`npm run lint` exits 0. The six remaining phpcs errors are the nonce findings belonging to audit items #5 and #6, deliberately left visible rather than suppressed.

Two things worth knowing about that work:

- **`composer lint-fix` is not safe to run unattended.** During the mechanical pass phpcbf's `WordPress.WP.CapitalPDangit` sniff rewrote `.wordpress` → `.WordPress` inside a regex matching a *hostname*, silently breaking dev-environment detection for site search. The regex is now fenced with a scoped `phpcs:disable`, but the sniff will do the same thing to any similar string it meets.
- Two sniffs are scoped off `src/views/` because the views are included from inside `Core::render_template()`, making their variables method-local rather than global. phpcs cannot see that when analysing a file standalone.

## Found since the audit

Issues discovered while working the audit items. None were part of the original 27.

| Issue | Item | Found during |
|---|---|---|
| [#122](https://github.com/ucsc/ucsc-custom-functionality/issues/122) | News block's ACF field group is titled "Modal Block" — a copy-paste leftover visible to editors since the block shipped | docblock pass |
| [#123](https://github.com/ucsc/ucsc-custom-functionality/issues/123) | `Query_Loop::get_query_type_filed()` is misspelled | docblock pass |
| [#127](https://github.com/ucsc/ucsc-custom-functionality/issues/127) | Magazine tab and panel ids are not unique per block instance, so two blocks sharing an item title emit duplicate ids and cross-trigger each other's panels | lint backlog, step 5 |
