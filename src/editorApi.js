import ElementFocusMarshall from '@/editor/utils/ElementFocusMarshall'
import ElementsManager from '@/editor/manager/elements'
import OptionsManager from '@/editor/manager/options'

window.zb = window.zb || {}

window.zb.editor = {
	elements: ElementsManager,
	ElementFocusMarshall: ElementFocusMarshall(),
	options: new OptionsManager()
}
