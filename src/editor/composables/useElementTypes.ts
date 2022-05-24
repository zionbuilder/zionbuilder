import { find } from 'lodash-es';
import { computed } from 'vue';
import { ElementType } from '../models/ElementType';
import { translate } from '@common/modules/i18n';

const elementTypes: [] = [];

elementTypes.push(
	new ElementType({
		element_type: 'contentRoot',
		wrapper: true,
		show_in_ui: false,
	}),
);

export function useElementTypes() {
	function addElementTypes(elements: []) {
		elements.forEach(elementConfig => {
			elementTypes.push(new ElementType(elementConfig));
		});
	}

	const addElementType = config => {
		elementTypes.push(new ElementType(config));
	};

	const getVisibleElements = computed(() => {
		return elementTypes.filter(element => {
			return element.show_in_ui;
		});
	});

	const getElementType = (elementType: string) => {
		return (
			find(elementTypes, { element_type: elementType }) ||
			new ElementType({
				element_type: 'invalid',
				name: translate('invalid_element'),
			})
		);
	};

	const registerElementComponent = config => {
		const { elementType, component } = config;
		const element = getElementType(elementType);

		if (!element) {
			console.warn(`element with ${elementType} could not be found.`);
			return;
		}

		element.registerComponent(component);
	};

	const getElementIcon = (elementType: string) => {
		const element = getElementType(elementType);
		return element.icon ? element.icon : null;
	};

	const getElementImage = (elementType: string) => {
		const element = getElementType(elementType);
		return element.thumb ? element.thumb : null;
	};

	function resetElementComponents() {
		elementTypes.value.forEach(elementType => {
			elementType.resetComponent();
		});
	}

	return {
		elementTypes,
		getVisibleElements,
		getElementIcon,
		getElementImage,
		addElementType,
		getElementType,
		registerElementComponent,
		resetElementComponents,
		addElementTypes,
	};
}
