import { useContentStore } from '../store';
import { generateUID } from '/@/common/utils';
import { each, update, get, isPlainObject, debounce } from 'lodash-es';

export class ZionElement {
	// Element data for DB
	public element_type = '';
	public content: string[] = [];
	public uid = '';
	// Make it ref so we can watch it
	public options: { [key: string]: unknown } = {};
	// Helpers
	public renderAttributes: RenderAttributes;
	public parent = '';
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
		this.parent = parent;
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

	delete() {
		const contentStore = useContentStore();
		contentStore.deleteElement(this.uid);
	}

	toJSON(): ZionElementConfig {
		const contentStore = useContentStore();

		const content = this.content.map(child => {
			const element = contentStore.getElement(child);

			return element.toJSON();
		});

		return {
			uid: this.uid,
			content: content,
			element_type: this.element_type,
			options: this.options,
		};
	}
}
