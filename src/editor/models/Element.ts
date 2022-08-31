import { each, update, get, isPlainObject } from 'lodash-es';
import { generateUID } from '/@/common/utils';
import { applyFilters } from '/@/common/modules/hooks';
import { regenerateUIDs } from '../utils';
import { useElementDefinitionsStore, useElementsStore } from '../store';
import { RenderAttributes } from './RenderAttributes';
import { serverRequest } from '../api';

export class Element {
	// Element data for DB
	public element_type = '';
	public _content: Element[] = [];
	public uid = '';
	// Make it ref so we can watch it
	public options: { [key: string]: any } = {};
	// Helpers
	public renderAttributes: RenderAttributes;
	public parentUid = '';
	public isHighlighted = false;
	public activeElementRename = false;
	public scrollTo = false;
	public isCutted = false;
	public widgetID = '';
	public callbacks: {} = {};
	public loading = false;
	public serverRequester = null;
	public addedTime = null;

	constructor(data, parentUid = '') {
		this.setElementData(data);
		this.renderAttributes = new RenderAttributes();
		this.parentUid = parentUid;
		this.serverRequester = this.createRequester();

		this.addedTime = Date.now();
	}

	createRequester() {
		const request = (data, successCallback, failCallback) => {
			// Pass to filter with all the extra arguments
			const parsedData = JSON.parse(
				JSON.stringify({
					...applyFilters('zionbuilder/server_request/element_requester_data', {}, this),
					...data,
					useCache: true,
				}),
			);

			return serverRequest.request(parsedData, successCallback, failCallback);
		};

		return {
			request,
		};
	}

	setElementData(data) {
		const {
			uid = generateUID(),
			content,
			options = {},
			element_type,
			widget_id: widgetID,
			...remainingProperties
		} = data;
		this.options = {
			...(isPlainObject(options) ? options : {}),
			_advanced_options: { ...(isPlainObject(options._advanced_options) ? options._advanced_options : {}) },
		};
		this.uid = uid;
		this.element_type = element_type;

		if (widgetID) {
			this.widgetID = widgetID;
		}

		if (remainingProperties) {
			Object.keys(remainingProperties).forEach(id => {
				const value = remainingProperties[id];
				this[id] = value;
			});
		}

		// Keep only the uid for content
		if (Array.isArray(content)) {
			this.addChildren(content);
		}
	}

	get content() {
		return this._content;
	}

	set content(newValue) {
		if (Array.isArray(newValue)) {
			newValue.forEach(element => {
				element.setParent(this.uid);
			});
		}
		this._content = newValue;
	}

	get isWrapper() {
		return this.elementTypeModel.wrapper;
	}

	get elementTypeModel() {
		const elementsDefinitionStore = useElementDefinitionsStore();
		return elementsDefinitionStore.getElementDefinition(this.element_type);
	}

	get parent() {
		const { getElement } = useElementsStore();
		return getElement(this.parentUid);
	}

	get name() {
		return get(this.options, '_advanced_options._element_name', this.elementTypeModel.name);
	}

	get elementCssId() {
		let cssID = this.getOptionValue('_advanced_options._element_id', this.uid);
		cssID = applyFilters('zionbuilder/element/css_id', cssID, this);
		return cssID;
	}

	getOptionValue(path, defaultValue = null) {
		return get(this.options, path, defaultValue);
	}

	updateOptionValue(path, newValue) {
		update(this.options, path, () => newValue);
	}

	setParent(parentUid: string) {
		this.parentUid = parentUid;
	}

	rename() {
		this.activeElementRename = true;
	}

	toggleVisibility() {
		this.isVisible = !this.isVisible;
	}

	highlight() {
		this.isHighlighted = true;
	}

	unHighlight() {
		this.isHighlighted = false;
	}

	addChild(element: Element | string | Object, index = -1) {
		let uid = null;
		let elementInstance = null;

		if (element instanceof Element) {
			elementInstance = element;
			uid = element.uid;
		} else {
			const { registerElement } = useElementsStore();
			elementInstance = registerElement(element, this.uid);
			uid = elementInstance.uid;
		}

		// Set the parent
		index = index === -1 ? this.content.length : index;
		elementInstance.parentUid = this.uid;
		this.content.splice(index, 0, elementInstance);
	}

	addChildren(elements, index = -1) {
		each(elements, element => {
			this.addChild(element, index);

			// In case we need to insert multiple elements at an index higher then the last item, we need to increment the index
			index = index !== -1 ? index + 1 : index;
		});
	}

	getIndexInParent() {
		return this.parent.content.indexOf(this);
	}

	removeChild(element) {
		const index = this.content.indexOf(element);
		this.content.splice(index, 1);
	}

	replaceChild(oldElement, newElement) {
		const index = this.content.indexOf(oldElement);
		this.content.splice(index, 1, newElement);
	}

	move(newParent: Element, index = -1) {
		this.parent.removeChild(this);
		const { getElement } = useElementsStore();
		const element = getElement(this.uid);
		newParent.addChild(element, index);
	}

	deleteChild(child: string | Element) {
		let element = null;
		if (typeof child === 'string') {
			const { getElement } = useElementsStore();
			element = getElement(child);
		} else if (typeof child === Element) {
			element = child;
		}

		if (element) {
			element.delete();
		} else {
			console.error(`Could not find element for deletion!`, child);
		}
	}

	// ####DONE
	toJSON(): { [key: string]: any } {
		const content = this.content.map(element => {
			return element.toJSON();
		});

		const element_type = this.element_type;

		const elementData = {
			uid: this.uid,
			content: content,
			element_type,
			options: this.options,
		};

		// Set the widget ID for widget elements
		if (this.widgetID.length > 0) {
			elementData.widget_id = this.widgetID;
		}

		return elementData;
	}

	getClone() {
		const configAsJSON = JSON.parse(JSON.stringify(this.toJSON()));
		regenerateUIDs(configAsJSON);

		// Add the instance to all elements
		const { registerElement } = useElementsStore();
		return registerElement(configAsJSON, this.parent.uid);
	}

	on(type, callback) {
		this.callbacks[type] = this.callbacks[type] || [];
		this.callbacks[type].push(callback);
	}

	off(type, callback) {
		const callbacks = this.callbacks[type] || [];
		const callbackIndex = callbacks.indexOf(callback);

		if (callbackIndex !== -1) {
			this.callbacks[type].splice(callbackIndex, 1);
		}
	}

	setData(key, value) {
		this[key] = value;
	}

	unsetData(key) {
		delete this[key];
	}

	trigger(type, ...data) {
		const callbacks = this.callbacks[type] || [];

		callbacks.forEach(calback => {
			calback(...data);
		});
	}
}
