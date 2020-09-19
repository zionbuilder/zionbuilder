import ElementFocusMarshall from '@/editor/utils/ElementFocusMarshall'
import ElementsManager from '@/editor/manager/elements'

window.zb = window.zb || {}

window.zb.editor = {
	elements: ElementsManager,
	ElementFocusMarshall: ElementFocusMarshall()
}
