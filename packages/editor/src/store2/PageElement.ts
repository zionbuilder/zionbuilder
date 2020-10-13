import { generateUID, getOptionValue } from '@zb/utils'
import { each } from 'lodash-es'

export default class Element {
	public element_type: string = ''
	public options: object = {}
	public content: array = []
	public uid:string = ''
	public parentUid: string = ''

	constructor(data, parentUid) {
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

	get collection () {
		return window.zb.editor.pageElements
	}

	get parent () {
		return this.collection.getElement(this.parentUid)
	}

	get name () {
		return getOptionValue(this.options, '_advanced_options._element_name') || this.elementTypeModel.name
	}

	get isVisible () {
		return getOptionValue(this.options, '_isVisible', true)
	}

	addChild (element, index = -1) {
		let uid = null

		if (typeof element === 'string') {
			uid = element
		} else if (typeof element === Element) {
			uid = element.uid
		} else {
			const elementInstance = this.collection.addElement(element, this.uid)
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

	removeChild(elementUID) {
		const index = this.data.content.indexOf(elementUID)
		this.content.splice(index, 1)
	}

	move (newParent, index = -1) {
		this.parent.removeChild(this.uid)
		newParent.addChild(this.uid, index)
	}

	/**
	 * Will delete the element and all it's childrens
	 */
	delete () {
		this.collection.removeElement(this)
	}

	toJSON () {
		const content = this.content.map(elementUID => {
			const element = this.collection.getElement(elementUID)

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
