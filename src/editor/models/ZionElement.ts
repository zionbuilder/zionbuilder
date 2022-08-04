import { useContentStore, useElementDefinitionsStore } from '../store';
import { generateUID } from '/@/common/utils';
import { each, update, get, isPlainObject, debounce } from 'lodash-es';
import { type ElementType } from '../models/ElementType';

export class ZionElement {
	// Element data for DB
	public element_type = '';
	public content: string[] = [];
	public uid = '';
	// Make it ref so we can watch it
	public options: { [key: string]: unknown } = {};
	// Helpers
	public renderAttributes: RenderAttributes;
	public parentUID = '';
	public elementDefinition: ElementType;
	public isHighlighted = false;
	public activeElementRename = false;
	public scrollTo = false;
	public isCut = false;
	public widgetID = '';
	public loading = false;
	public serverRequester = null;
	public addedTime = 0;

	constructor(elementData: ZionElementConfig, parent: string) {
		const contentStore = useContentStore();
		const elementDefinitionStore = useElementDefinitionsStore();

		this.elementDefinition = elementDefinitionStore.getElementDefinition(elementData.element_type);

		const parsedElement = Object.assign(
			{},
			{
				uid: generateUID(),
				content: [],
				options: {},
			},
			elementData,
		);

		// Set the options
		const options = isPlainObject(parsedElement.options) ? parsedElement.options : {};

		// Check the type of advanced options
		if (typeof options._advanced_options !== 'undefined' && !isPlainObject(parsedElement.options._advanced_options)) {
			options._advanced_options = {};
		}

		this.uid = parsedElement.uid;
		this.element_type = parsedElement.element_type;

		// UI logic
		this.parentUID = parent;
		this.addedTime = Date.now();

		// Set children
		const content: string[] = [];
		if (Array.isArray(elementData.content)) {
			// Register children
			elementData.content.forEach(el => {
				const childElement = contentStore.registerElement(el, elementData.uid);
				content.push(childElement.uid);
			});
		}

		this.content = content;
	}

	get parent() {
		const contentStore = useContentStore();
		return contentStore.getElement(this.parentUID);
	}

	get name(): string {
		return <string>get(this.options, '_advanced_options._element_name', this.elementDefinition.name);
	}

	set name(newName) {
		window.zb.run('editor/elements/rename', {
			elementUID: this.uid,
			newName,
		});
	}

	setName(newName: string) {
		this.updateOptionValue('_advanced_options._element_name', newName);
	}

	getOptionValue(path: string, defaultValue = null) {
		return get(this.options, path, defaultValue);
	}

	updateOptionValue(path: string, newValue: unknown) {
		update(this.options, path, () => newValue);
	}

	highlight() {
		if (!this.isHighlighted) {
			this.isHighlighted = true;
		}
	}

	unHighlight() {
		if (this.isHighlighted) {
			this.isHighlighted = false;
		}
	}

	// Element visibility
	get isVisible(): boolean {
		return <boolean>get(this.options, '_isVisible', true);
	}

	set isVisible(isVisible: boolean) {
		window.zb.run('editor/elements/set_visibility', {
			elementUID: this.uid,
			isVisible,
		});
	}

	setVisibility(isVisible: boolean) {
		update(this.options, '_isVisible', () => isVisible);
	}

	get indexInParent() {
		if (this.parent) {
			return this.parent.content.indexOf(this.uid);
		}

		return 0;
	}

	delete() {
		window.zb.run('editor/elements/delete', {
			elementUID: this.uid,
		});
	}

	toJSON(): ZionElementConfig {
		const contentStore = useContentStore();

		const content = this.content.map(child => {
			const element = contentStore.getElement(child);

			return element.toJSON();
		});

		return JSON.parse(
			JSON.stringify({
				uid: this.uid,
				content: content,
				element_type: this.element_type,
				options: this.options,
			}),
		);
	}
}
