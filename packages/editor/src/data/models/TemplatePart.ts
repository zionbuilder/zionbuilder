import { Element } from './Element'
import { useElements } from '@data'

export interface TemplatePartConfig {
	id: string,
	name: string
}

export class TemplatePart {
	public id: string = ''
	public name: string = ''
	public element: Element

	constructor (config: TemplatePartConfig) {
		const { name = '', id = '' } = config
		this.name = name
		this.id = id

		const { registerElement } = useElements()

		this.element = registerElement({
			uid: id,
			element_type: 'contentRoot'
		}, null)
	}

	toJSON () {
		return this.element.toJSON().content
	}
}