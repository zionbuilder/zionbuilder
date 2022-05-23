window.zb = window.parent.zb;

import { createVNode, render } from 'vue';

import previewApp from './PreviewApp.vue';

const vNode = createVNode(previewApp);

const editorApp = window.zb.editor.appInstance;
const appContext = editorApp._context;

// Components
import { InlineEditor } from './components/InlineEditor';
import Element from './components/preview/Element.vue';
import ElementWrapper from './components/preview/ElementWrapper.vue';
import SortableContent from './components/preview/SortableContent.vue';
import RenderValue from './components/preview/RenderValue.vue';
import ElementIcon from './components/preview/ElementIcon.vue';

editorApp.component('Element', Element);
editorApp.component('ElementWrapper', ElementWrapper);
editorApp.component('InlineEditor', InlineEditor);
editorApp.component('SortableContent', SortableContent);
editorApp.component('RenderValue', RenderValue);
editorApp.component('ElementIcon', ElementIcon);

vNode.appContext = appContext;

const { editorData } = window.zb.editor.useEditorData();

const renderElement = document.getElementById(`znpb-preview-${editorData.value.page_id}-area`);

if (renderElement) {
	render(vNode, renderElement);
}

// Cleanup after itself
window.addEventListener('unload', event => {
	if (renderElement) {
		render(null, renderElement);
	}
});
