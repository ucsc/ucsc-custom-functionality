# Changelog

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

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
