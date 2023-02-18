interface Window {
	YoastSEO: Record<string, unknown>;
	zb_yoast_data: {
		page_content: string;
	};
	jQuery;
	ZbRankMathData: {
		rest_root: string;
		nonce: string;
	};

	rankMathEditor: {
		refresh: (content: string) => void;
	};
}
