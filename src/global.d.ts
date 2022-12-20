type App = import('vue').App;

interface Window {
	zb: {
		run: T<string, Record<string, unknown>>;
		hooks: import('./common/modules');
		api: import('./common/api');
		utils: import('./common/utils');
		components: import('./common/components');
		store: import('./common/store');
		composables: import('./common/composables');
		installCommonAPP: (app: App) => void;
	} = {};

	ZBCommonData: {
		environment: {
			is_pro_active: boolean;
			plugin_version: string;
		};
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
