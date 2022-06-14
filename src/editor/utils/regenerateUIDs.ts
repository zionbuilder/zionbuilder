import { generateUID } from '/@/common/utils';

export const regenerateUIDs = element => {
	const uid = generateUID();

	element.uid = uid;

	if (Array.isArray(element.content)) {
		element.content = element.content.map(element => {
			return regenerateUIDs(element);
		});
	}

	return element;
};

export const regenerateUIDsForContent = (elements: []) => {
	return elements.map(element => regenerateUIDs(element));
};
