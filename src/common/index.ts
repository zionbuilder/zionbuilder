import { type App } from 'vue';
import { createPinia } from 'pinia';

import './scss/index.scss';
import './modules/wordpress';
import * as utils from './utils';
import * as api from './api';
import * as store from './store';
import * as components from './components';
import * as hooks from './modules/hooks';
import * as composables from './composables';
import { PopperDirective } from './components/tooltip';

export const installCommonAPP = (app: App) => {
	app.use(createPinia());

	for (const componentName in components) {
		// @ts-expect-error: I want to index import using string
		app.component(componentName, components[componentName]);
	}

	// Add the tooltip directive
	app.directive('znpb-tooltip', PopperDirective);

	// Init error interceptors
	api.errorInterceptor(store.useNotificationsStore());

	// Register the library sources
	const { addSources } = composables.useLibrary();
	addSources(window.ZBCommonData.library.sources);
};

window.zb = window.zb || {};
Object.assign(window.zb, {
	hooks,
	i18n,
	api,
	utils,
	composables,
	components,
	installCommonAPP,
	store,
});
