export class ElementType {
	element_type: string = ''
	name: string = ''
	component = null
	category: string = ''
	deprecated: boolean = false
	has_multiple: boolean = false
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

	getComponent () {
		return this.component
	}

	registerComponent (component) {
		this.component = component
	}
}