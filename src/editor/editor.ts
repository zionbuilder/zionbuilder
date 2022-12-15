import './scss/index.scss';
import * as Vue from 'vue';

// Main
import { registerEditorOptions } from './components/options';
import * as STORE from './store';

// Plugins
import { useElementDefinitionsStore } from './store';

// Global components
import UIElementIcon from './common/UIElementIcon.vue';
import AddElementIcon from './common/AddElementIcon.vue';
import EmptySortablePlaceholder from './common/EmptySortablePlaceholder.vue';
import SortableHelper from './common/SortableHelper.vue';
import SortablePlaceholder from './common/SortablePlaceholder.vue';
import RenderValue from '/@/preview/components/RenderValue.vue';
import ElementIcon from '/@/preview/components/ElementIcon.vue';
import InlineEditor from '/@/preview/components/InlineEditor/InlineEditor.vue';
import ElementWrapper from '/@/preview/components/ElementWrapper.vue';
import Element from '/@/preview/components/Element.vue';

// Preview related
import SortableContent from '/@/preview/components/SortableContent.vue';
import HeartBeat from './modules/HeartBeat.js';

// Exports
import * as API from './api.js';
import * as COMPOSABLES from './composables';
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

// Register options
registerEditorOptions();

// init data
const appInstance = Vue.createApp(App);

// Init global components
appInstance.use(window.zb.installCommonAPP);

// Register nested components
appInstance.component('EmptySortablePlaceholder', EmptySortablePlaceholder);
appInstance.component('AddElementIcon', AddElementIcon);
appInstance.component('UIElementIcon', UIElementIcon);
appInstance.component('SortableHelper', SortableHelper);
appInstance.component('SortablePlaceholder', SortablePlaceholder);
appInstance.component('SortableContent', SortableContent);
appInstance.component('RenderValue', RenderValue);
appInstance.component('ElementIcon', ElementIcon);
appInstance.component('InlineEditor', InlineEditor);
appInstance.component('ElementWrapper', ElementWrapper);
appInstance.component('Element', Element);

// Add editor methods and utilities to all components
appInstance.config.globalProperties.$zb = {
	appInstance,
	urls: window.ZnPbInitialData.urls,
};
appInstance.provide('$zb', appInstance.config.globalProperties.$zb);

// WordPress heartBeat
new HeartBeat();

window.addEventListener('load', function () {
	// Trigger event so others can hook into ZionBuilder API
	const evt = new CustomEvent('zionbuilder/editor/ready');
	window.dispatchEvent(evt);

	appInstance.mount('#znpb-app');
});

// Export so we can access them from window.zb.editor
const elementDefinitionsStore = useElementDefinitionsStore();
window.zb = window.zb || {};
window.zb.editor = Object.assign(
	{},
	{ appInstance, registerElementComponent: elementDefinitionsStore.registerElementComponent },
	API,
	COMPOSABLES,
	UTILS,
	STORE,
);

window.zb.commandsManager = commandsManager;
window.zb.run = function (commandName: string, commandArgs: Record<string, unknown>) {
	return commandsManager.runCommand(commandName, commandArgs);
};
window.zb.history = history;
