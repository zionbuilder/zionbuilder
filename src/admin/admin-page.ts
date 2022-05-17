import './scss/index.scss';

import BaseAdmin from './components/BaseAdmin.vue';

import routes from './router';

// Utils
import { addFilter } from '@zb/hooks';
import { useInjections } from '@zb/components';
import { ServerRequest } from '@zb/utils';

// Set Service Interceptor
import { errorInterceptor } from '@zb/rest';
import { useNotifications } from '@zionbuilder/composables';

const serverRequest = new ServerRequest();
const notifications = useNotifications();

// Add error interceptor for API
errorInterceptor(notifications);

const api = {
	routes,
	interceptors: {
		errorInterceptor,
	},
	notifications,
	addFilter,
	useInjections,
	serverRequest,
};

// Add to global object
window.zb = window.zb || {};
window.zb.admin = api;
