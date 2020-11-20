import './connector'
import { createVNode, render } from 'vue'

import previewApp from './App.vue'

const vNode = createVNode(previewApp)
const editorApp = window.parent.zb.editor.appInstance
const appContext = editorApp._context

// Components
import { InlineEditor } from './components/InlineEditor'
import Element from './components/Element.vue'
import SortableContent from './components/SortableContent.vue'
import RenderValue from './components/RenderValue.vue'
import ElementIcon from './components/ElementIcon.vue'

appContext.components['Element'] = Element
appContext.components['InlineEditor'] = InlineEditor
appContext.components['SortableContent'] = SortableContent
appContext.components['RenderValue'] = RenderValue
appContext.components['ElementIcon'] = ElementIcon

vNode.appContext = appContext

const renderElement = document.getElementById('znpb-preview-content-area')

if (renderElement) {
	render(vNode, renderElement)
}
