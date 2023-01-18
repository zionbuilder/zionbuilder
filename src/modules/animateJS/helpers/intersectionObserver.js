/**
 * Observe
 *
 * Will use intersection observer to observe a list of nodes
 *
 * @param {*} elements
 * @param {*} callback
 * @param {*} options
 */
const observe = function (elements, callback, options = {}) {
	const optionsWithDefaults = {
		...options,
	};

	const observer = new IntersectionObserver(callback, optionsWithDefaults);

	elements.forEach(element => {
		observer.observe(element);
	});

	return observer;
};

export default observe;
