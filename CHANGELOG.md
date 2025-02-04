# Changelog

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

## [1.6.1]

### Features
* Add new News Block 

## [1.6.0](https://github.com/ucsc/ucsc-custom-functionality/compare/v1.5.3...v1.6.0) (2024-06-10)


### Features

* ensure the plugin properly checks for functions before calling them ([ba4f8b4](https://github.com/ucsc/ucsc-custom-functionality/commit/ba4f8b4470feb4e4b87efded7392e91cd7de7c06)), closes [#21](https://github.com/ucsc/ucsc-custom-functionality/issues/21)

### [1.5.3](https://github.com/ucsc/ucsc-custom-functionality/compare/v1.5.2...v1.5.3) (2024-01-10)

### [1.5.2](https://github.com/ucsc/ucsc-custom-functionality/compare/v1.5.1...v1.5.2) (2024-01-10)


### Bug Fixes

* 🐛 Numeric node version it is :-( ([a250457](https://github.com/ucsc/ucsc-custom-functionality/commit/a2504573b19f0675bc5a1559a1cc3de9f5fbd11f))

### [1.5.1](https://github.com/ucsc/ucsc-custom-functionality/compare/v1.5.0...v1.5.1) (2024-01-09)


### Bug Fixes

* 🐛 Fix for Node version so we always use lts ([898d934](https://github.com/ucsc/ucsc-custom-functionality/commit/898d93428bd80c5e91e93b3b761f4d6d87865c39))
* 🐛 Setup action node version to numeric value ([5b6a9fe](https://github.com/ucsc/ucsc-custom-functionality/commit/5b6a9fe26181cd4fc300afa227db56fced4b6b76))
* 🐛 Wrap release node version in quotes ([899f836](https://github.com/ucsc/ucsc-custom-functionality/commit/899f836d0822297ce00482cd17bb03cc5314be6e))

## [1.5.0](https://github.com/ucsc/ucsc-custom-functionality/compare/v1.4.0...v1.5.0) (2024-01-09)


### Features

* ✨ Add Github deployment workflow ([#20](https://github.com/ucsc/ucsc-custom-functionality/issues/20)) ([444eea2](https://github.com/ucsc/ucsc-custom-functionality/commit/444eea26689a0c4f045749f90ea16860660bb2a7))


### Bug Fixes

* 🐛 Remove xmlrpc link from wp_head since we've disabled the functionality in WordPress ([#18](https://github.com/ucsc/ucsc-custom-functionality/issues/18)) ([56e2a7f](https://github.com/ucsc/ucsc-custom-functionality/commit/56e2a7fe9189863d60815fa96197075043ab4651))

## [1.4.0](https://github.com/ucsc/ucsc-custom-functionality/compare/v1.3.0...v1.4.0) (2023-10-10)


### Features

* ✨ Add shortcode `site-search` to display Google search results ([#14](https://github.com/ucsc/ucsc-custom-functionality/issues/14)) ([6eac524](https://github.com/ucsc/ucsc-custom-functionality/commit/6eac5248b9fa6bf040cc7d90fdb00bfe007f7d06))

## [1.3.0](https://github.com/ucsc/ucsc-custom-functionality/compare/v1.2.2...v1.3.0) (2022-12-09)


### Features

* ⚡️ Update date format in last-modified shortcode ([05c9b9a](https://github.com/ucsc/ucsc-custom-functionality/commit/05c9b9a3bdea34fcaef9bfdde446c71608c09ac8))

### [1.2.2](https://github.com/ucsc/ucsc-custom-functionality/compare/v1.2.1...v1.2.2) (2022-10-19)

### [1.2.1](https://github.com/ucsc/ucsc-custom-functionality/compare/v1.2.0...v1.2.1) (2022-08-17)


### Bug Fixes

* :lock: Function to prevent Site Manager from elevating a user to Administrator. Fixes [#7](https://github.com/ucsc/ucsc-custom-functionality/issues/7) ([52b631c](https://github.com/ucsc/ucsc-custom-functionality/commit/52b631c3e8815cd5a6437d3cfd38d4de785efdc4))

## [1.2.0](https://github.com/ucsc/ucsc-custom-functionality/compare/v1.1.4...v1.2.0) (2022-08-08)


### Features

* :sparkles: Add GA, SiteImprove, Security Headers and other scripts to functionality. ([024accc](https://github.com/ucsc/ucsc-custom-functionality/commit/024accc55a722a2a95dfa243b29d932c86842352))


### Bug Fixes

* 🐛 Remove Content-Security-Policy header ([70aa81a](https://github.com/ucsc/ucsc-custom-functionality/commit/70aa81aa0d3866d56df3abbb8e6fb29005cedc75))
* 📝 Remove additional security headers ([#9](https://github.com/ucsc/ucsc-custom-functionality/issues/9)) ([c8538fa](https://github.com/ucsc/ucsc-custom-functionality/commit/c8538fadac3d90c96ab5785c98826d0f2b075e9e))
* Bump plugin version to match last release ([924212b](https://github.com/ucsc/ucsc-custom-functionality/commit/924212b4e64497530741231ebb9b38838e2e3b0c))

### [1.1.4](https://github.com/ucsc/ucsc-custom-functionality/compare/v1.1.3...v1.1.4) (2022-05-03)

### [1.1.3](https://github.com/ucsc/ucsc-custom-functionality/compare/v1.1.2...v1.1.3) (2022-04-20)

### [1.1.2](https://github.com/ucsc/ucsc-custom-functionality/compare/v1.1.1...v1.1.2) (2022-04-20)

### [1.1.1](https://github.com/ucsc/ucsc-custom-functionality/compare/v1.1.0...v1.1.1) (2022-04-20)

## 1.1.0 (2022-04-20)


### Features

* :sparkles: Add and enque `shortcodes.php`, moved shortcodes from themes `functions.php` per WP Theme Check ([d45ce61](https://github.com/ucsc/ucsc-custom-functionality/commit/d45ce613310ef6ce80cc4b914d0b76b44f233cf5))
* :sparkles: Add PHP/WP coding standards check, add `standard-version` via npm ([e93d80d](https://github.com/ucsc/ucsc-custom-functionality/commit/e93d80da52660203bb2239d4276aa97350886703))
* :tada: Scaffold out new `ucsc-main-core-functionality-plugin` and create a basic `role` function for a Site Admin ([5c39d5b](https://github.com/ucsc/ucsc-custom-functionality/commit/5c39d5b66a4f99ab87c4181e13557105585b5229))
* ✨ Remove ucsc_site_manager role on plugin deactivation. Resolves [#1](https://github.com/ucsc/ucsc-custom-functionality/issues/1) ([4fe0cae](https://github.com/ucsc/ucsc-custom-functionality/commit/4fe0caef6c3e1551ed1c32cc8e042f4db6ae26f0))
* 🚀 Add build/zip script and file manifest ([d82f8da](https://github.com/ucsc/ucsc-custom-functionality/commit/d82f8da4c4ae2a659edff292233338d395a3b8fd))


### Bug Fixes

* :art: replace theme function prefix with plugin function prefix. ([303dabb](https://github.com/ucsc/ucsc-custom-functionality/commit/303dabb8abaa01585bedf8e3007418e647d5d5ab))
* 🐛 Remove breadcrumb library since we don't use it in this plugin ([87c7cc5](https://github.com/ucsc/ucsc-custom-functionality/commit/87c7cc5c06de5a0fdf1b7e06832d40813da6e2eb))
* 🐛 Update slug and label; Set proper capabilities. Resolves [#2](https://github.com/ucsc/ucsc-custom-functionality/issues/2) ([27a0444](https://github.com/ucsc/ucsc-custom-functionality/commit/27a0444dc7baff899d6c31a493dcf79086548e92))
