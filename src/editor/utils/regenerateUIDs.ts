import { generateUID } from '/@/common/utils';

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

export const regenerateUIDsForContent = (elements: ZionElementConfig[]) => {
	return elements.map(element => regenerateUIDs(element));
};
