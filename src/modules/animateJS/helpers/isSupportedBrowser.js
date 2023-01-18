const intersectionObserverSupport = function () {
	return (
		'IntersectionObserver' in window &&
		'IntersectionObserverEntry' in window &&
		'intersectionRatio' in window.IntersectionObserverEntry.prototype
	);
};

export default intersectionObserverSupport;
