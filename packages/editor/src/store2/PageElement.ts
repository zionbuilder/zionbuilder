import { generateUID } from '@zb/utils'
import { each } from 'lodash-es'

export default class Element {
	public element_type: string = ''
	public content: array = []
	public uid:string = ''
	public parentUid: string = ''
	public elementTypeModel = null

	constructor(data, parentUid) {
		const {
			uid,
			content,
			options,
			element_type
		} = data

		this.uid = uid || generateUID()
		this.options = options
		this.element_type = element_type

		// Keep only the uid for content
		if (Array.isArray(content)) {
			each(content, elementConfig => {
				console.log(elementConfig)
				this.addChildren(elementConfig)
			})
		}

		this.parentUid = parentUid
	}

	get collection (){
		return window.zb.editor.pageElements
	}

	get parent () {
		return this.collection.getElement(this.parentUid)
	}

	addChildren (element, index = -1) {
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

	addChildrens (elements, index) {
		each(elements, (element) => {
			console.log({element});

			this.addChildren(element, -1)
		})
	}

	removeChildren(elementUID) {
		const index = this.data.content.indexOf(elementUID)
		this.content.splice(index, 1)
	}

	move (newParent, index = -1) {
		this.parent.removeChildren(this.uid)
		newParent.addChildren(this.uid, index)
	}

	/**
	 * Will delete the element and all it's childrens
	 */
	delete () {
		this.collection.deleteElement(this)
	}
}
