import './scss/index.scss';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { createRouter, createWebHashHistory } from 'vue-router';

import { hooks } from '../common/modules/hooks';
import { install as I18nInstall } from '../common/modules/i18n';
import { errorInterceptor } from '../common/api';
import { useNotificationsStore } from '../common/store';
import { install as ComponentsInstall } from '../common';
import * as COMMONUTILS from '/@/common/utils';

import { initRoutes, routes } from './router';
import { useLibrary } from '../common/composables/useLibrary';

// Main
import App from './components/BaseAdmin.vue';

// Components
import SideMenu from './components/SideMenu.vue';
import PageTemplate from './components/PageTemplate.vue';
import ListAnimate from './components/ListAnimate.vue';
import ModalTwoColTemplate from './components/ModalTwoColTemplate.vue';

const { addSources } = useLibrary();
addSources(window.ZnPbAdminPageData.template_sources);

const appInstance = createApp(App);

// Global components
appInstance.component('SideMenu', SideMenu);
appInstance.component('PageTemplate', PageTemplate);
appInstance.component('ListAnimation', ListAnimate);
appInstance.component('ModalTwoColTemplate', ModalTwoColTemplate);

// Plugins
appInstance.use(ComponentsInstall);
appInstance.use(I18nInstall, window.ZnI18NStrings);
appInstance.use(createPinia());

const notificationsStore = useNotificationsStore();

errorInterceptor(notificationsStore);

window.addEventListener('load', function () {
	// Trigger event so others can hook into ZionBuilder API
	const evt = new CustomEvent('zionbuilder/admin/init', {
		detail: window.zb.admin,
	});

	// Add default routes
	initRoutes();

	window.dispatchEvent(evt);

	const router = hooks.applyFilters(
		'zionbuilder/router',
		createRouter({
			// 4. Provide the history implementation to use. We are using the hash history for simplicity here.
			history: createWebHashHistory(),
			routes: routes.getConfigForRouter(), // short for `routes: routes`
		}),
	);

	appInstance.use(router);
	appInstance.mount('#znpb-admin');
});

// Provide API to 3rd party developers
window.zb = window.zb || {};
window.zb.admin = {
	hooks,
	// Old API
	routes,
	interceptors: {
		errorInterceptor,
	},
	notifications: notificationsStore,
	// TODO: deprecate this
	addFilter: hooks.addFilter,
};

window.zb.hooks = hooks;
window.zb.utils = COMMONUTILS;
