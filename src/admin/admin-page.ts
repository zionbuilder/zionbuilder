import './scss/index.scss';

import { createApp } from 'vue';
import { createRouter, createWebHashHistory } from 'vue-router';

// External modules from common package/script
import { applyFilters } from '@zb/hooks';
import { install as COMMON_INSTALL } from '@zb/common';

import { initRoutes, routes } from './router';

// Main
import App from './components/BaseAdmin.vue';

// Components
import SideMenu from './components/SideMenu.vue';
import PageTemplate from './components/PageTemplate.vue';
import ListAnimate from './components/ListAnimate.vue';
import ModalTwoColTemplate from './components/ModalTwoColTemplate.vue';

const appInstance = createApp(App);

// Global components
appInstance.component('SideMenu', SideMenu);
appInstance.component('PageTemplate', PageTemplate);
appInstance.component('ListAnimation', ListAnimate);
appInstance.component('ModalTwoColTemplate', ModalTwoColTemplate);

// Plugins
appInstance.use(COMMON_INSTALL);

window.addEventListener('load', function () {
	// Trigger event so others can hook into ZionBuilder API
	const evt = new CustomEvent('zionbuilder/admin/init', {
		detail: window.zb.admin,
	});

	// Add default routes
	initRoutes();

	window.dispatchEvent(evt);

	const router = applyFilters(
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
	routes,
};
