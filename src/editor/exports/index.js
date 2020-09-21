import ElementFocusMarshall from '@/editor/utils/ElementFocusMarshall'
import Elements from '@/editor/models/Elements'
import OptionsManager from '@/editor/manager/options'

window.zb = window.zb || {}

window.zb.editor = {
	elements: new Elements(window.ZnPbInitalData.elements_data, window.ZnPbInitalData.elements_categories),
	ElementFocusMarshall: ElementFocusMarshall(),
	options: new OptionsManager()
}
