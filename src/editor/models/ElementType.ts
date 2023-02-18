import { markRaw } from 'vue';

export class ElementType {
	element_type = '';
	name = '';
	component = null;
	category = '';
	deprecated = false;
	icon = '';
	thumb = '';
	is_child = false;
	keywords: string[] = [];
	label = '';
	options: object = {};
	scripts: Record<string, { src: string; handle: string }> = {};
	styles: Record<string, { src: string; handle: string }> = {};
	show_in_ui = true;
	style_elements: object = {};
	wrapper = false;
	content_orientation = 'horizontal';

	constructor(config) {
		Object.assign(this, config);
	}

	getComponent() {
		return this.component;
	}

	registerComponent(component) {
		this.component = markRaw(component);
	}

	resetComponent() {
		this.component = null;
	}
}
