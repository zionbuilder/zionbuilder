interface Window {
	zb: {
		run: T<string, Record<string, unknown>>;
	} = {};
	ZnRestConfig: {
		nonce: string;
		rest_root: string;
	};
	ZnPbInitialData: {
		page_id: string;
		autosaveInterval: number;
	};

	ZnPbEditPostData: {
		data: {
			post_id: number;
			is_editor_enabled: boolean;
			l10n: Record<string, string>;
		};
	};
}
