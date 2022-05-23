import isSupportedBrowser from './helpers/isSupportedBrowser';
import observe from './helpers/intersectionObserver';

/**
 * Main plugin function
 *
 * @param {} options
 */
const animateJs = function (options) {
	let elements;
	options = {
		animationClass: 'animated',
		selector: 'ajs__element',
		watchForChanges: true,
		mode: 'css_class',
		once: true,
		...options,
	};

	const selector = options.selector;

	if (typeof selector === 'string') {
		elements = document.querySelectorAll(`.${selector}`);
	} else if ((typeof selector === 'object' && selector[0] && selector[0].nodeType === 1) || selector instanceof Array) {
		elements = [...selector];
	} else if (typeof selector === 'object' && selector.nodeType === 1) {
		elements = [selector];
	} else {
		console.warn('You need to specify the selector.');
		return;
	}

	/**
	 * Apply animation
	 *
	 * Will apply the animation class to the provided element
	 *
	 * @param HTMLElement domNode The DOM node for which you want to apply the animation
	 */
	const applyAnimation = function (domNode) {
		if (domNode.dataset.ajsAnimation) {
			domNode.classList.add(domNode.dataset.ajsAnimation, options.animationClass);
		}
	};

	// if the browser is not supported, animate all items
	if (!isSupportedBrowser()) {
		elements.forEach(element => {
			applyAnimation(element);
		});
	}

	/**
	 * Callback to run when the element enters the viewport
	 *
	 * @param [IntersectionObserverEntry] entries
	 * @param IntersectionObserver observer
	 */
	const onElementInViewport = function (entries, observer) {
		entries.forEach(entry => {
			if (entry.isIntersecting) {
				if (options.mode === 'css_class' && options.animationClass) {
					applyAnimation(entry.target);
				} else if (options.mode === 'event') {
					const event = new Event('inViewport');
					entry.target.dispatchEvent(event);
				}

				// Remove the element from elements list
				if (options.once) {
					observer.unobserve(entry.target);
				}
			}
		});
	};

	const observer = observe(elements, onElementInViewport);

	const destroy = function () {
		observer.disconnect();
		elements = null;
	};

	return {
		destroy,
	};
};

window.animateJs = animateJs;

/**
 * Export Public Plugin API
 */
export default animateJs;
