import { provide, inject } from 'vue';

export const useElementProvide = () => {
	const provideElement = element => {
		provide('ZionElement', element);
	};

	const injectElement = () => {
		const element = inject('ZionElement');
		if (!element) {
			// throw error, no store provided
			console.error('No element was provided');
		}
		return element;
	};

	return {
		provideElement,
		injectElement,
	};
};
