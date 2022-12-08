import { type App } from 'vue';
import { createPinia } from 'pinia';

import './scss/index.scss';
import './modules/wordpress';
import * as utils from './utils';
import * as api from './api';
import * as store from './store';
import * as components from './components';
import * as hooks from './modules/hooks';
import * as i18n from './modules/i18n';
import { PopperDirective } from './components/tooltip';
import { useLibrary } from './composables';

window.zb = window.zb || {};
window.zb.hooks = hooks;
window.zb.i18n = i18n;
window.zb.api = api;
window.zb.utils = utils;

export const install = (app: App) => {
	app.use(createPinia());

	// init the translation functions
	app.use(i18n.install);

	for (const componentName in components) {
		// @ts-expect-error: I want to index import using string
		app.component(componentName, components[componentName]);
	}

	// Add the tooltip directive
	app.directive('znpb-tooltip', PopperDirective);

	// Init error interceptors
	api.errorInterceptor(store.useNotificationsStore());

	// Register the library sources
	const { addSources } = useLibrary();
	addSources(window.ZBCommonData.library.sources);
};

window.zb.common = {
	components,
	install,
	store,
};
