import './scss/index.scss';
import { createApp } from 'vue';

// Main
import { createPinia } from 'pinia';
import { useElementTypes } from './composables';
import { registerEditorOptions } from './components/options';

// Plugins
import { install as ComponentsInstall } from '../common';
import { install as I18nInstall } from '../common/modules/i18n';
import { errorInterceptor } from '../common/api';
import { useLibrary } from '../common/composables';
import { useNotificationsStore } from '../common/store';

// Global components
import { TreeViewList, TreeViewListItem } from './components/treeView';
import UIElementIcon from './common/UIElementIcon.vue';
import AddElementIcon from './common/AddElementIcon.vue';
import EmptySortablePlaceholder from './common/EmptySortablePlaceholder.vue';
import SortableHelper from './common/SortableHelper.vue';
import SortablePlaceholder from './common/SortablePlaceholder.vue';

import { WireframeList, WireframeListItem } from './components/treeView';
import { useOptionsSchemas } from '../common';

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
appInstance.use(I18nInstall, window.ZnI18NStrings);
appInstance.use(ComponentsInstall);
appInstance.use(createPinia());

// Init library sources
const { addSources } = useLibrary();
addSources(window.ZnPbInitalData.template_sources);

// Add error interceptor for API
errorInterceptor(useNotificationsStore());

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
