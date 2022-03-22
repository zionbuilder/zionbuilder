export const createI18n = (initialStrings = {}) => {
	let strings: translateString = {};

	const addStrings = (newStrings: translateString) => {
		strings = {
			...strings,
			...newStrings,
		};
	};

	const translate = (stringId: string) => {
		if (typeof strings[stringId] !== 'undefined') {
			return strings[stringId];
		}

		// eslint-disable-next-line
		console.error(`String with id ${stringId} was not found.`)

		return '';
	};

	if (initialStrings) {
		addStrings(initialStrings);
	}

	return {
		addStrings,
		translate,
	};
};
