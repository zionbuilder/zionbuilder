import { generateUID, getOptionValue, updateOptionValue } from '@zb/utils'
import { each, update, isPlainObject, cloneDeep } from 'lodash-es'
import { useElements } from '../useElements'
import { useElementTypes } from '../useElementTypes'
import { useElementFocus } from '../useElementFocus'
import { Options } from './Options'
import { RenderAttributes } from './RenderAttributes'

const { registerElement, unregisterElement, getElement } = useElements()
const { getElementType } = useElementTypes()

export class Element {
	// Element data for DB
	public element_type: string = ''
	public content: string[] = []
	public uid:string = ''
	// Helpers
	public receivedOptions = {}
	private _options: Object = {}
	public renderAttributes: RenderAttributes
	public parentUid: string = ''
	public isHighlighted: boolean = false
	public activeElementRename: boolean = false
	public scrollTo: boolean = false
	public isCutted: boolean = false
	public widgetID: string = ''

	constructor(data, parentUid = '') {
		const {
			uid = generateUID(),
			content,
			options = {},
			element_type,
			widget_id: widgetID
		} = data

		this.uid = uid
		this.element_type = element_type

		if (widgetID) {
			this.widgetID = widgetID
		}

		// Setup options
		this.receivedOptions = isPlainObject(options) ? options : {}

		this.renderAttributes = new RenderAttributes()
		this.options = this.receivedOptions

		// Keep only the uid for content
		if (Array.isArray(content)) {
			this.addChildren(content)
		}

		this.parentUid = parentUid
	}

	get options () {
		return this._options
	}

	set options (newValues) {
		const schema = this.elementTypeModel.options || {}

		// Clear render attributes
		this.renderAttributes.clear()

		// this.customCSS = new CustomCSS()
		this._options = new Options(cloneDeep(newValues), schema, {
			parsers: [
				this.renderAttributes.parseValue.bind(this.renderAttributes)
			]
		})
	}

	get isWrapper () {
		return this.elementTypeModel.wrapper
	}

	get elementTypeModel () {
		return getElementType(this.element_type)
	}

	get parent () {
		return getElement(this.parentUid)
	}

	get name () {
		return getOptionValue(this.options, '_advanced_options._element_name') || this.elementTypeModel.name
	}

	set name (newName) {
		update(this.options, '_advanced_options._element_name', () => newName)
	}

	// Element visibility
	get isVisible () {
		return getOptionValue(this.options, '_isVisible', true)
	}

	set isVisible (visbility) {
		update(this.options, '_isVisible', () => visbility)
	}

	get elementCssId () {
		return (this.options._advanced_options || {})._element_id || this.uid
	}

	rename () {
		this.activeElementRename = true
	}

	toggleVisibility () {
		update(this.options, '_isVisible', () => !this.isVisible)
	}

	focus () {
		const { focusElement } = useElementFocus()
		focusElement(this)
	}

	highlight () {
		this.isHighlighted = true
	}

	unHighlight () {
		this.isHighlighted = false
	}

	addChild (element: Element | string | Object, index = -1) {
		let uid = null
		let elementInstance = null

		if (typeof element === 'string') {
			elementInstance = getElement(element)
			uid = element
		} else if (element instanceof Element) {
			elementInstance = element
			uid = element.uid
		} else {
			elementInstance = registerElement(element, this.uid)
			uid = elementInstance.uid
		}

		// Set the parent
		elementInstance.parentUid = this.uid
		this.content.splice(index, 0, uid)
	}

	addChildren (elements, index = -1) {
		each(elements, (element) => {
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
	 * Duplicates the current element instance and replaces the UID's
	 * for it and all it's nested elements
	 */
	duplicate () {
		const indexInParent = this.parent.content.indexOf(this.uid)
		const elementAsJSON = this.getClone()

		this.parent.addChild(elementAsJSON, indexInParent + 1)
	}

	/**
	 * Will delete the element and all it's childrens
	 */
	delete () {
		if (this.parent) {
			this.parent.removeChild(this.uid)
		}
		unregisterElement(this.uid)
	}

	deleteChild (child: string | Element) {
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

	toJSON(): {[key:string]: any} {
		const content = this.content.map(elementUID => {
			const element = getElement(elementUID)

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

	getClone () {
		const uid = generateUID()
		const cloneConfig = JSON.parse(JSON.stringify({
			uid,
			content: this.content,
			element_type: this.element_type,
			options: this.options
		}))

		const clonedContent = cloneConfig.content.map((childElementUID: string) => {
			const elementInstance = getElement(childElementUID)
			// Add the instance to all elements
			const cloneInstance = registerElement(elementInstance.getClone(), uid)

			return cloneInstance.uid
		})

		// Replace content with new one that has updated uids
		cloneConfig.content = clonedContent
		// Add the instance to all elements
		const clonedInstance = registerElement(cloneConfig, this.parent.uid)

		// Return the duplicated element isntance
		return clonedInstance
	}
}
