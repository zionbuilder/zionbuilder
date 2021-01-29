import { markRaw } from 'vue'

export class ElementType {
	element_type: string = ''
	name: string = ''
	component = null
	category: string = ''
	deprecated: boolean = false
	icon: string = ''
	is_child: boolean = false
	keywords: string[] = []
	label: string = ''
	options: object = {}
	scripts: object = {}
	styles: object = {}
	show_in_ui: boolean = true
	style_elements: object = {}
	wrapper: boolean = false
	content_orientation: string = 'horizontal'

	constructor(config) {
		Object.assign(this, config)
	}

	getComponent() {
		return this.component
	}

	registerComponent(component) {
		this.component = markRaw(component)
	}

	resetComponent() {
		this.component = null
	}
}