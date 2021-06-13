import { generateUID } from '@zb/utils'
import { regenerateUIDs } from '@utils'
import { each, update, get, set, isPlainObject } from 'lodash-es'
import { useElements } from '../useElements'
import { useElementTypes } from '../useElementTypes'
import { useElementActions } from '../useElementActions'
import { RenderAttributes } from './RenderAttributes'
import { useEditElement } from '../useEditElement'
import { useHistory } from '../useHistory'

const { registerElement, unregisterElement, getElement } = useElements()
const { getElementType } = useElementTypes()

export class Element {
	// Element data for DB
	public element_type: string = ''
	public _content: Element[] = []
	public uid: string = ''
	// Make it ref so we can watch it
	public options: { [key: string]: any } = {}
	// Helpers
	public renderAttributes: RenderAttributes
	public parentUid: string = ''
	public isHighlighted: boolean = false
	public activeElementRename: boolean = false
	public scrollTo: boolean = false
	public isCutted: boolean = false
	public widgetID: string = ''
	public callbacks: {} = {}
	public loading: boolean = false
	public treeViewItemExpanded: boolean = false

	constructor(data, parentUid = '') {
		this.setElementData(data)
		this.renderAttributes = new RenderAttributes()
		this.parentUid = parentUid
	}

	setElementData(data) {
		const {
			uid = generateUID(),
			content,
			options = {},
			element_type,
			widget_id: widgetID,
			...remainingProperties
		} = data
		this.options = isPlainObject(options) ? options : {}
		this.uid = uid
		this.element_type = element_type

		if (widgetID) {
			this.widgetID = widgetID
		}

		if (remainingProperties) {
			Object.keys(remainingProperties).forEach(id => {
				const value = remainingProperties[id]
				this[id] = value
			})
		}

		// Keep only the uid for content
		if (Array.isArray(content)) {
			this.addChildren(content)
		}
	}

	get content() {
		return this._content
	}

	set content(newValue) {
		if (Array.isArray(newValue)) {
			newValue.forEach(element => {
				element.setParent(this.uid)
			})
		}
		this._content = newValue
	}

	get isWrapper() {
		return this.elementTypeModel.wrapper
	}

	get elementTypeModel() {
		return getElementType(this.element_type)
	}

	get parent() {
		return getElement(this.parentUid)
	}

	get name() {
		return get(this.options, '_advanced_options._element_name', this.elementTypeModel.name)
	}

	set name(newName) {
		const oldName = this.name
		this.updateOptionValue('_advanced_options._element_name', newName)
		// set(this.options, '_advanced_options._element_name', newName)

		// Add to history
		const { addToHistory } = useHistory()
		addToHistory(`Renamed ${oldName} to ${newName}`)

	}

	// Element visibility
	get isVisible() {
		return get(this.options, '_isVisible', true)
	}

	set isVisible(visbility) {
		update(this.options, '_isVisible', () => visbility)

		// Add to history
		const { addToHistory } = useHistory()
		const visibilityText = visbility ? 'visible' : 'hidden'
		addToHistory(`Set ${this.name} visibility to ${visibilityText}`)
	}

	get elementCssId() {
		return this.getOptionValue('_advanced_options._element_id', this.uid)
	}

	updateOptions(newValues) {
		this.options = newValues ? newValues : {}
	}

	getOptionValue(path, defaultValue = null) {
		return get(this.options, path, defaultValue)
	}

	updateOptionValue(path, newValue) {
		update(this.options, path, () => newValue)
	}

	setParent(parentUid: string) {
		this.parentUid = parentUid
	}

	rename() {
		this.activeElementRename = true
	}

	toggleVisibility() {
		this.isVisible = !this.isVisible
	}

	focus() {
		const { focusElement } = useElementActions()
		focusElement(this)
	}

	highlight() {
		this.isHighlighted = true
	}

	unHighlight() {
		this.isHighlighted = false
	}


	addChild(element: Element | string | Object, index = -1) {
		let uid = null
		let elementInstance = null

		if (element instanceof Element) {
			elementInstance = element
			uid = element.uid
		} else {
			elementInstance = registerElement(element, this.uid)
			uid = elementInstance.uid
		}

		// Set the parent
		index = index === -1 ? this.content.length : index
		elementInstance.parentUid = this.uid
		this.content.splice(index, 0, elementInstance)
	}

	addChildren(elements, index = -1) {
		each(elements, (element) => {
			this.addChild(element, index)
		})
	}

	getIndexInParent() {
		return this.parent.content.indexOf(this)
	}

	removeChild(element) {
		const index = this.content.indexOf(element)
		this.content.splice(index, 1)
	}

	move(newParent: Element, index = -1) {
		this.parent.removeChild(this)
		const element = getElement(this.uid)
		newParent.addChild(element, index)
	}

	/**
	 * Duplicates the current element instance and replaces the UID's
	 * for it and all it's nested elements
	 */
	duplicate() {
		const indexInParent = this.parent.content.indexOf(this)
		const elementAsJSON = this.getClone()

		this.parent.addChild(elementAsJSON, indexInParent + 1)

		const { addToHistory } = useHistory()
		addToHistory(`Duplicated ${this.name}`)
	}

	/**
	 * Will delete the element and all it's childrens
	 */
	delete() {
		if (this.parent) {
			const { element, unEditElement } = useEditElement()

			if (element.value === this) {
				unEditElement()
			}

			this.parent.removeChild(this)
		}

		unregisterElement(this.uid)

		// Add to history
		const { addToHistory } = useHistory()
		addToHistory(`Deleted ${this.name}`)
	}

	deleteChild(child: string | Element) {
		let element = null
		if (typeof child === 'string') {
			element = getElement(child)
		} else if (typeof child === Element) {
			element = child
		}

		if (element) {
			element.delete()
		} else {
			console.error(`Could not find element for deletion!`, child)
		}
	}

	toJSON(): { [key: string]: any } {
		const content = this.content.map(element => {
			return element.toJSON()
		})

		const element_type = this.element_type

		const elementData = {
			uid: this.uid,
			content: content,
			element_type,
			options: this.options
		}

		// Set the widget ID for widget elements
		if (this.widgetID.length > 0) {
			elementData.widget_id = this.widgetID
		}

		return elementData
	}

	getClone() {
		const configAsJSON = JSON.parse(JSON.stringify(this.toJSON()))
		regenerateUIDs(configAsJSON)

		// Add the instance to all elements
		return registerElement(configAsJSON, this.parent.uid)
	}

	setUid(uid) {
		this.uid = uid
	}

	on(type, callback) {
		this.callbacks[type] = this.callbacks[type] || []
		this.callbacks[type].push(callback)
	}

	off(type, callback) {
		const callbacks = this.callbacks[type] || []
		const callbackIndex = callbacks.indexOf(callback)

		if (callbackIndex !== -1) {
			this.callbacks[type].splice(callbackIndex, 1)
		}
	}

	trigger(type, ...data) {
		const callbacks = this.callbacks[type] || []

		callbacks.forEach(calback => {
			calback(...data)
		});
	}
}
