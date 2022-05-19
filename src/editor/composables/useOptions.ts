import { cloneDeep } from 'lodash-es';
import { applyFilters } from '@zb/hooks';

export const parseOptions = (options, schema, config) => {
	// Create a clone
	options = applyFilters('zionbuilder/options/model', cloneDeep(options), config);
	const renderAttributes = {};
	const customCSS = '';
	const customCSSMap = {
		default: {},
		laptop: {},
		tablet: {},
		mobile: {},
	};

	const startLoading = () => {
		if (typeof config.onLoadingStart === 'function') {
			config.onLoadingStart();
		}
	};

	const endLoading = () => {
		if (typeof config.onLoadingEnd === 'function') {
			config.onLoadingEnd();
		}
	};

	const parseData = () => {
		// Allow external data modification
		const options =
			// Set defaults and extract render attributes and custom css
			this.parseOptions(this.schema, options);

		return {
			options: options,
			renderAttributes: this.renderAttributes,
			customCSS: this.getCustomCSS(),
		};
	};

	return {
		renderAttributes,
		customCSS,
		options,
	};
};
