import { createVNode, render } from 'vue'

import previewApp from './App.vue'

const vNode = createVNode(previewApp)
const editorApp = window.parent.zb.editor.appInstance
const appContext = editorApp._context

editorApp.config.globalProperties.$zb.preview = {
	scripts: {
		loadScript () {}
	}
}

vNode.appContext = appContext

const renderElement = document.getElementById('znpb-preview-content-area')

if (renderElement) {
	render(vNode, renderElement)
}
