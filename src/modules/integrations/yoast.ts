class ZionBuilderIntegration {
	constructor() {
		// Ensure YoastSEO.js is present and can access the necessary features.
		if (
			typeof window.YoastSEO === 'undefined' ||
			typeof window.YoastSEO.analysis === 'undefined' ||
			typeof window.YoastSEO.analysis.worker === 'undefined'
		) {
			return;
		}

		window.YoastSEO.app.registerPlugin('ZionBuilderIntegration', { status: 'ready' });

		this.registerModifications();
	}

	/**
	 * Registers the addContent modification.
	 *
	 * @returns {void}
	 */
	registerModifications() {
		const callback = this.addContent.bind(this);

		// Ensure that the additional data is being seen as a modification to the content.
		window.YoastSEO.app.registerModification('content', callback, 'ZionBuilderIntegration', 10);
	}

	/**
	 * Adds to the content to be analyzed by the analyzer.
	 *
	 * @param {string} data The current data string.
	 *
	 * @returns {string} The data string parameter with the added content.
	 */
	addContent(data: string) {
		const { is_editor_enabled = false } = window.ZnPbEditPostData ? window.ZnPbEditPostData.data : {};
		if (is_editor_enabled && window.zb_yoast_data && window.zb_yoast_data.page_content) {
			data += window.zb_yoast_data.page_content;
		}

		return data;
	}
}
/**
 * Adds eventlistener to load the plugin.
 */
if (typeof window.YoastSEO !== 'undefined' && typeof window.YoastSEO.app !== 'undefined') {
	new ZionBuilderIntegration();
} else {
	window.jQuery(window).on('YoastSEO:ready', function () {
		new ZionBuilderIntegration();
	});
}

export {};
