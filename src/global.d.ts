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
	};
}
