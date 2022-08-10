import './scss/index.scss';
import * as Vue from 'vue';

// Main
import { createPinia } from 'pinia';
import { registerEditorOptions } from './components/options';
import * as STORE from './store';
import * as COMMONUTILS from '/@/common/utils';

// Plugins
import { install as ComponentsInstall } from '../common';
import { install as I18nInstall } from '../common/modules/i18n';
import { errorInterceptor } from '../common/api';
import { useLibrary } from '../common/composables';
import { useNotificationsStore } from '../common/store';
import { useElementDefinitionsStore } from './store';

// Global components
import UIElementIcon from './common/UIElementIcon.vue';
import AddElementIcon from './common/AddElementIcon.vue';
import EmptySortablePlaceholder from './common/EmptySortablePlaceholder.vue';
import SortableHelper from './common/SortableHelper.vue';
import SortablePlaceholder from './common/SortablePlaceholder.vue';

// Preview related
import SortableContent from '/@/preview/components/SortableContent.vue';

import { useOptionsSchemas } from '../common';
import HeartBeat from './modules/HeartBeat.js';

// Exports
import * as API from './api.js';
import * as COMPOSABLES from './composables';
import * as COMMON_COMPOSABLES from '../common/composables';
import * as UTILS from './utils';

// Components
import App from './EditorApp.vue';

// Register commands
import { CommandManager } from './modules/Commands';
import { HistoryManager } from './modules/History';

// Init commands manager
const commandsManager = new CommandManager();
// Provide the commands manager globally
window.zb.commandsManager = commandsManager;

const history = new HistoryManager();

// Register editor options schemas
const { registerSchema } = useOptionsSchemas();
registerSchema('pageSettingsSchema', window.ZnPbInitialData.page_settings.schema);

// Register options
registerEditorOptions();

// init data
const appInstance = Vue.createApp(App);

// Init global components
appInstance.use(I18nInstall, window.ZnI18NStrings);
appInstance.use(ComponentsInstall);
appInstance.use(createPinia());

// Init library sources
const { addSources } = useLibrary();
addSources(window.ZnPbInitialData.template_sources);

// Add error interceptor for API
errorInterceptor(useNotificationsStore());

// Register nested components
appInstance.component('EmptySortablePlaceholder', EmptySortablePlaceholder);
appInstance.component('AddElementIcon', AddElementIcon);
appInstance.component('UIElementIcon', UIElementIcon);
appInstance.component('SortableHelper', SortableHelper);
appInstance.component('SortablePlaceholder', SortablePlaceholder);
appInstance.component('SortableContent', SortableContent);

// Add editor methods and utilities to all components
appInstance.config.globalProperties.$zb = {
	appInstance,
	urls: window.ZnPbInitialData.urls,
};
appInstance.provide('$zb', appInstance.config.globalProperties.$zb);

// Expose common methods
const elementDefinitionsStore = useElementDefinitionsStore();
elementDefinitionsStore.setCategories(window.ZnPbInitialData.elements_categories);
elementDefinitionsStore.addElements(window.ZnPbInitialData.elements_data);

// WordPress heartBeat
new HeartBeat();

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
	{ appInstance, registerElementComponent: elementDefinitionsStore.registerElementComponent },
	API,
	COMPOSABLES,
	COMMON_COMPOSABLES.units,
	UTILS,
	STORE,
	COMMON_COMPOSABLES,
);

window.zb.vue = Vue;
window.zb.utils = COMMONUTILS;
window.zb.commandsManager = commandsManager;
window.zb.run = function (commandName: string, commandArgs: Record<string, unknown>) {
	return commandsManager.runCommand(commandName, commandArgs);
};
window.zb.history = history;
