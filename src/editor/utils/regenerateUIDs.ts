import { unset } from 'lodash-es';

const { generateUID } = window.zb.utils;

export const regenerateUIDs = (element: ZionElementConfig) => {
	const uid = generateUID();

	element.uid = uid;

	if (Array.isArray(element.content)) {
		element.content = element.content.map(element => {
			return regenerateUIDs(element);
		});
	}

	return element;
};

export const removeElementID = (element: ZionElementConfig) => {
	unset(element, 'options._advanced_options._element_id');

	if (Array.isArray(element.content)) {
		element.content = element.content.map(element => {
			return removeElementID(element);
		});
	}

	return element;
};

export const regenerateUIDsForContent = (elements: ZionElementConfig[]) => {
	return elements.map(element => regenerateUIDs(element));
};
