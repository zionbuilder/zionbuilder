import { generateUID, getOptionValue, updateOptionValue } from '@zb/utils'
import { each } from 'lodash-es'
import { useElements } from '../index'
// import { setFocusedElement } from '../interactions/focusedElement.ts'

export class Element {
	// Element data for DB
	public element_type: string = ''
	public options: object = {}
	public content: string[] = []
	public uid:string = ''
	// Helpers
	public parentUid: string = ''
	public isHighlighted: boolean = false

	constructor(data, parentUid = '') {
		const {
			uid,
			content,
			options,
			element_type
		} = data

		this.uid = uid || generateUID()
		this.options = options || []
		this.element_type = element_type

		// Keep only the uid for content
		if (Array.isArray(content)) {
			this.addChildren(content)
		}

		this.parentUid = parentUid
	}

	get isWrapper () {
		return this.elementTypeModel.wrapper
	}

	get elementTypeModel () {
		return window.zb.editor.elements.getElement(this.element_type)
	}

	get parent () {
		const { getElement } = useElements()
		return getElement(this.parentUid)
	}

	get name () {
		return getOptionValue(this.options, '_advanced_options._element_name') || this.elementTypeModel.name
	}

	get isVisible () {
		return getOptionValue(this.options, '_isVisible', true)
	}

	rename (name: string) {
		console.log(name);
		updateOptionValue(this.options, '_advanced_options._element_name', name)
	}

	focus () {
		// setFocusedElement(this)
	}

	highlight () {
		this.isHighlighted = true
	}

	unHighlight () {
		this.isHighlighted = false
	}

	addChild (element: Element | string | Object, index = -1) {
		let uid = null

		if (typeof element === 'string') {
			uid = element
		} else if (element instanceof Element) {
			uid = element.uid
		} else {
			const { registerElement } = useElements()
			const elementInstance = registerElement(element, this.uid)
			uid = elementInstance.uid
		}

		this.content.splice(index, 0, uid)
	}

	addChildren (elements, index = -1) {
		each(elements, (element) => {
			console.log({element});

			this.addChild(element, -1)
		})
	}

	removeChild(elementUID: string) {
		const index = this.content.indexOf(elementUID)
		this.content.splice(index, 1)
	}

	move (newParent: Element, index = -1) {
		this.parent.removeChild(this.uid)
		newParent.addChild(this.uid, index)
	}

	/**
	 * Will delete the element and all it's childrens
	 */
	delete () {
		const { unregisterElement } = useElements()

		if (this.parent) {
			this.parent.removeChild(this.uid)
		}
		unregisterElement(this.uid)
	}

	toJSON(): {[key:string]: any} {
		const { getElement } = useElements()
		const content = this.content.map(elementUID => {
			const element = getElement(elementUID)

			return element.toJSON()
		})

		return {
			uid: this.uid,
			content: content,
			element_type: this.element_type,
			options: this.options
		}
	}
}
