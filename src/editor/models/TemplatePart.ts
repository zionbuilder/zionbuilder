import { Element } from './Element';
import { useElementsStore } from '../store';

export interface TemplatePartConfig {
	id: string;
	name: string;
}

export class TemplatePart {
	public id = '';
	public name = '';
	public element: Element;

	constructor(config: TemplatePartConfig) {
		const { name = '', id = '' } = config;
		this.name = name;
		this.id = id;

		const { registerElement } = useElementsStore();

		this.element = registerElement(
			{
				uid: id,
				element_type: 'contentRoot',
			},
			null,
		);
	}

	toJSON() {
		return this.element.toJSON().content;
	}
}
