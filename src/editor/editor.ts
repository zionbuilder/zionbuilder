import './scss/index.scss';
import { createApp } from 'vue';

// Main
import * as hooks from '@zb/hooks';
import { useElementTypes } from './composables';
import { registerEditorOptions } from './components/options';
// Plugins
import { install as ComponentsInstall } from '@zb/components';
import { install as L18NInstall } from '@zb/i18n';
import { errorInterceptor } from '@zb/rest';
import { useNotifications, useLibrary } from '../common/composables';

// Global components
import { TreeViewList, TreeViewListItem } from './components/treeView';
import UIElementIcon from './common/UIElementIcon.vue';
import AddElementIcon from './common/AddElementIcon.vue';
import EmptySortablePlaceholder from './common/EmptySortablePlaceholder.vue';
import SortableHelper from './common/SortableHelper.vue';
import SortablePlaceholder from './common/SortablePlaceholder.vue';

import { WireframeList, WireframeListItem } from './components/treeView';
import { useOptionsSchemas } from '@zb/components';

// Exports
import * as API from './api.js';
import * as COMPOSABLES from './composables';
import * as COMMON_COMPOSABLES from '../common/composables';
import * as UTILS from './utils';

// Register editor options schemas
const { registerSchema } = useOptionsSchemas();
registerSchema('pageSettingsSchema', window.ZnPbInitalData.page_settings.schema);

// Register options
registerEditorOptions();

// Components
import App from './EditorApp.vue';

// init data
const appInstance = createApp(App);

// Init global components
appInstance.use(L18NInstall, window.ZnI18NStrings);
appInstance.use(ComponentsInstall);

// Init library sources
const { addSources } = useLibrary();
addSources(window.ZnPbInitalData.template_sources);

// Add error interceptor for API
errorInterceptor(useNotifications());

// Register nested components
appInstance.component('TreeViewList', TreeViewList);
appInstance.component('TreeViewListItem', TreeViewListItem);
appInstance.component('WireframeList', WireframeList);
appInstance.component('WireframeListItem', WireframeListItem);
appInstance.component('EmptySortablePlaceholder', EmptySortablePlaceholder);
appInstance.component('AddElementIcon', AddElementIcon);
appInstance.component('UIElementIcon', UIElementIcon);
appInstance.component('SortableHelper', SortableHelper);
appInstance.component('SortablePlaceholder', SortablePlaceholder);

// Add editor methods and utilities to all components
appInstance.config.globalProperties.$zb = {
	hooks,
	appInstance,
	urls: window.ZnPbInitalData.urls,
};
appInstance.provide('$zb', appInstance.config.globalProperties.$zb);

// Expose common methods
const { registerElementComponent } = useElementTypes();

window.addEventListener('load', function () {
	// Trigger event so others can hook into ZionBuilder API
	const evt = new CustomEvent('zionbuilder/editor/ready');
	window.dispatchEvent(evt);

	appInstance.mount('#znpb-app');
});

// Export so we can access them from window.zb.editor
window.zb = window.zb || {};
window.zb.editor = Object.assign(
	{},
	{ appInstance, registerElementComponent },
	API,
	COMPOSABLES,
	COMMON_COMPOSABLES.units,
	UTILS,
);