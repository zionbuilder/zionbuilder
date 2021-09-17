/* global YoastSEO */

const YoastSEO = window.YoastSEO

class ZionBuilderIntegration {
	constructor() {
		// Ensure YoastSEO.js is present and can access the necessary features.
		if (typeof YoastSEO === "undefined" || typeof YoastSEO.analysis === "undefined" || typeof YoastSEO.analysis.worker === "undefined") {
			return;
		}

		YoastSEO.app.registerPlugin("ZionBuilderIntegration", { status: "ready" });

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
		YoastSEO.app.registerModification("content", callback, "ZionBuilderIntegration", 10);
	}

	/**
	 * Adds to the content to be analyzed by the analyzer.
	 *
	 * @param {string} data The current data string.
	 *
	 * @returns {string} The data string parameter with the added content.
	 */
	addContent(data) {
		const { is_editor_enabled = false } = (window.ZnPbEditPostData ? window.ZnPbEditPostData.data : {})
		if (is_editor_enabled && window.zb_yoast_data && window.zb_yoast_data.page_content) {

			data += window.zb_yoast_data.page_content;
		}

		return data;
	}
}
/**
 * Adds eventlistener to load the plugin.
 */
if (typeof YoastSEO !== "undefined" && typeof YoastSEO.app !== "undefined") {
	new ZionBuilderIntegration();
} else {
	window.jQuery(window).on(
		"YoastSEO:ready",
		function () {
			new ZionBuilderIntegration();
		}
	);
}