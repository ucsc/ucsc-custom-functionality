const path = require('path');
const defaultConfig = require('@wordpress/scripts/config/webpack.config.js');

module.exports = {
	...defaultConfig,
	...{
		entry: {
			...defaultConfig.entry(),
			'css/post-terms': path.resolve(process.cwd(), 'assets/scss/blocks', 'post-terms.scss'),
			'css/social-sharing': path.resolve(process.cwd(), 'assets/scss/blocks', 'social-sharing.scss'),
		},
	},
};
