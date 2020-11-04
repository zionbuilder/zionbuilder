import './connector'
import { createVNode, render } from 'vue'

import previewApp from './App.vue'

const vNode = createVNode(previewApp)
const editorApp = window.parent.zb.editor.appInstance
const appContext = editorApp._context
import { ScriptsLoader } from './ScriptsLoader'
import { InlineEditor } from './components/InlineEditor'
import Element from './components/Element.vue'
import SortableContent from './components/SortableContent.vue'
import RenderTag from './components/RenderTag.vue'
import RenderTagGroup from './components/RenderTagGroup.vue'
import RenderValue from './components/RenderValue.vue'
import ElementIcon from './components/ElementIcon.vue'

editorApp.component('Element', Element)
editorApp.component('InlineEditor', InlineEditor)
editorApp.component('SortableContent', SortableContent)
editorApp.component('RenderTag', RenderTag)
editorApp.component('RenderValue', RenderValue)
editorApp.component('RenderTagGroup', RenderTagGroup)
editorApp.component('ElementIcon', ElementIcon)

editorApp.config.globalProperties.$zb.preview = {
	scripts: ScriptsLoader(window.document)
}

vNode.appContext = appContext

const renderElement = document.getElementById('znpb-preview-content-area')

if (renderElement) {
	render(vNode, renderElement)
}
