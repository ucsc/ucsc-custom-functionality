# Changelog

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

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
