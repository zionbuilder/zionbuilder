export const getManager = () => {
	let zIndex = 10000;

	const getZIndex = () => {
		zIndex++;
		return zIndex;
	};

	const removeZIndex = () => {
		zIndex--;
	};

	return {
		getZIndex,
		removeZIndex,
	};
};
