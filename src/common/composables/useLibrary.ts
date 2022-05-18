import { applyFilters } from '@zb/hooks';
import { ref, Ref } from 'vue';
import { LibrarySource, Source, LocalLibrary } from './models/Library';

// TODO: move this in editor
const activeElement: Ref<null | object> = ref(null);

const librarySources: Ref<Object> = ref({});

export const useLibrary = () => {
	// TODO: move this in editor
	function unsetActiveElementForLibrary() {
		activeElement.value = null;
	}

	// TODO: move this in editor
	function setActiveElementForLibrary(element, config = {}) {
		if (activeElement.value && activeElement.value.element === element) {
			return;
		}

		activeElement.value = {
			element,
			config,
		};
	}

	// TODO: move this in editor
	function getElementForInsert() {
		const { element, config } = activeElement.value;
		const { placement = 'inside' } = config;

		if (placement === 'inside' && (element.isWrapper || element.element_type === 'contentRoot')) {
			return {
				element,
			};
		} else {
			const index = element.getIndexInParent() + 1;

			return {
				element: element.parent,
				index,
			};
		}
	}

	// TODO: move this in editor
	function insertElement(newElement) {
		const { element, index = -1 } = getElementForInsert();
		newElement = Array.isArray(newElement) ? newElement : [newElement];
		element.addChildren(newElement, index);
	}

	/**
	 *	Will register multiple sources
	 *
	 * @param sources The list of sources that needs to be registered
	 */
	function addSources(sources: Object) {
		Object.keys(sources).forEach(sourceID => {
			addSource(sources[sourceID]);
		});
	}

	function getSourceType(sourceType: string) {
		const sourceTypes = applyFilters('zionbuilder/library/sourceTypes', {
			local: LocalLibrary,
		});

		return typeof sourceTypes[sourceType] !== 'undefined' ? sourceTypes[sourceType] : LibrarySource;
	}

	/**
	 * Adds a new library source
	 *
	 * @param source The source object that needs to be added
	 */
	function addSource(source: Source) {
		const sourceType = getSourceType(source.type);
		librarySources.value[source.id] = new sourceType(source);
	}

	/**
	 * Returns a specific library source based on id
	 *
	 * @param sourceID The source id for the library to be retrieved
	 * @returns
	 */
	function getSource(sourceID: string) {
		return librarySources.value[sourceID];
	}

	return {
		activeElement,
		setActiveElementForLibrary,
		unsetActiveElementForLibrary,
		insertElement,

		// Methods
		addSources,
		addSource,
		getSource,

		// Refs
		librarySources,
	};
};
