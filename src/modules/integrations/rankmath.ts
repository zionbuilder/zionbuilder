function RankMath() {
	const { post_id = 0, is_editor_enabled = false } = window.ZnPbEditPostData ? window.ZnPbEditPostData.data : {};
	let pageContent = '';

	function init() {
		if (is_editor_enabled) {
			window.wp.hooks.addFilter('rank_math_content', 'rank-math', getContent); // This will hook into content analysis data.
		}
	}

	function getContent(content: string) {
		if (pageContent.length) {
			content = pageContent;
		} else {
			const url = `${window.ZbRankMathData.rest_root}zionbuilder/v1/pages/${post_id}/get_rendered_content`;
			fetch(url, {
				headers: {
					'X-WP-Nonce': window.ZbRankMathData.nonce,
					Accept: 'application/json',
					'Content-Type': 'application/json',
				},
			})
				.then(response => response.json())
				.then(data => {
					pageContent = data;

					if (pageContent.length) {
						window.rankMathEditor.refresh('content');
					}
				});
		}

		// perform the query
		return content;
	}

	return {
		init,
	};
}

RankMath().init();

export {};
