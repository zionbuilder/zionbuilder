import ElementFocusMarshall from '@/editor/utils/ElementFocusMarshall'
import OptionsManager from '@/editor/manager/options'
// Models
import PageState from '@/editor/models/PageState'
import { Elements } from '@/editor/models/Elements.ts'

window.zb = window.zb || {}

const editor = {
	elements: new Elements(window.ZnPbInitalData.elements_data, window.ZnPbInitalData.elements_categories),
	ElementFocusMarshall: ElementFocusMarshall(),
	options: new OptionsManager(),
	pageState: new PageState()
}

window.zb.editor = editor

export { editor }
