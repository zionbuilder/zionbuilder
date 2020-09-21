import ElementFocusMarshall from '@/editor/utils/ElementFocusMarshall'
import OptionsManager from '@/editor/manager/options'
// Models
import PageState from '@/editor/models/PageState'
import Elements from '@/editor/models/Elements'

window.zb = window.zb || {}

window.zb.editor = {
	elements: new Elements(window.ZnPbInitalData.elements_data, window.ZnPbInitalData.elements_categories),
	ElementFocusMarshall: ElementFocusMarshall(),
	options: new OptionsManager(),
	pageState: new PageState()
}
