const intersectionObserverSupport = function () {
    return 'IntersectionObserver' in window && 'IntersectionObserverEntry' in window && 'intersectionRatio' in window.IntersectionObserverEntry.prototype
}

const mutationObserverSupport = function () {
    return "MutationObserver" in window
}

const browserSupports = function () {
    return intersectionObserverSupport && mutationObserverSupport
}

export default intersectionObserverSupport