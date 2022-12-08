type App = import('vue').App;

interface Window {
	zb: {
		run: T<string, Record<string, unknown>>;
		hooks: import('./common/modules');
		i18n: import('./common/modules');
		api: import('./common/api');
		utils: import('./common/utils');
		common: {
			components: import('./common/components');
			install: (app: App) => void;
			store: import('./common/store');
		};
	} = {};

	ZBCommonData: {
		i18n: Record<string, string>;
		rest: {
			nonce: string;
			rest_root: string;
		};
		is_pro_active: boolean;
		library: {
			// TODO: set proper types here
			sources: Record<string, Record<string, string>>;
		};
	};

	// TODO: remove OLD DATA
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
