import { createVNode, render } from 'vue'

import previewApp from './App.vue'

const vNode = createVNode(previewApp)
const editorApp = window.parent.zb.editor.appInstance
const appContext = editorApp._context
import { ScriptsLoader } from './ScriptsLoader'
import SortableContent from './components/SortableContent.vue'
import RenderTag from './components/RenderTag.vue'
import RenderTagGroup from './components/RenderTagGroup.vue'
import RenderValue from './components/RenderValue.vue'

window.zb = window.parent.zb
editorApp.component('SortableContent', SortableContent)
editorApp.component('RenderTag', RenderTag)
editorApp.component('RenderValue', RenderValue)
editorApp.component('RenderTagGroup', RenderTagGroup)

editorApp.config.globalProperties.$zb.preview = {
	scripts: ScriptsLoader(window.document)
}

vNode.appContext = appContext

const renderElement = document.getElementById('znpb-preview-content-area')

if (renderElement) {
	render(vNode, renderElement)
}
