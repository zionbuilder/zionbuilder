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
			urls: {
				zion_dashboard: string;
				logo: string;
				pro_logo: string;
				plugin_root: string;
				purchase_url: string;
				documentation_url: string;
				free_changelog: string;
				pro_changelog: string;
				updates_page: string;
			};
			plugin_free: PluginInfo;
			plugin_pro: PluginInfo;
			plugin_name: string;

			// TODO: remove OLD DATA
			is_pro_active: boolean;
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
		wp_editor: string;
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

	// Frontend data
	zbScripts: {
		counter: new (element: HTMLElement) => void;
		progressBars: new (element: HTMLElement) => void;
		video: new (element: HTMLElement) => {
			init: () => {
				destroy: () => void;
			};
		};
	};

	// Screenshot data
	ZnPbScreenshotData: {
		home_url: string;
		is_debug: boolean;
		nonce_key: string;
		assets: {
			placeholder_iframe: string[];
		};
		constants: {
			PROXY_URL_ARGUMENT: string;
			PROXY_URL_NONCE_KEY: string;
			PROXY_ASSET_PARAM: string;
		};
	};

	wp: {
		i18n: {
			__: (text: string, domain?: string) => string;
		};
	};
}
