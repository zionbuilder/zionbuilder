import { getRenderedContent } from '/@/common/api';

function RankMath() {
	const { post_id = 0, is_editor_enabled = false } = window.ZnPbEditPostData ? window.ZnPbEditPostData.data : {};
	let pageContent = '';
	let hasContent = true;

	function init() {
		if (is_editor_enabled) {
			window.wp.hooks.addFilter('rank_math_content', 'rank-math', getContent); // This will hook into content analysis data.
		}
	}

	function getContent(content) {
		if (pageContent.length) {
			content = pageContent;
		} else {
			getRenderedContent(post_id).then(response => {
				pageContent = response.data;
				if (!pageContent.length) {
					hasContent = false;
				} else {
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
