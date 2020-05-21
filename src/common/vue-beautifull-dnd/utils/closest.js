const matchFunction = Element.prototype.matches ||
	Element.prototype.matchesSelector ||
	Element.prototype.mozMatchesSelector ||
	Element.prototype.msMatchesSelector ||
	Element.prototype.oMatchesSelector ||
	Element.prototype.webkitMatchesSelector

function matches (element, value, context = null) {
	if (!value) {
		return false
	} else if (value === '> *') {
		return matches(element.parentNode, context)
	} else if (value.nodeType > 0) {
		return element === value
	} else if (typeof value === 'string') {
		return matchFunction.call(element, value)
	} else if (value instanceof NodeList || value instanceof Array) {
		return [...value].includes(element)
	} else if (typeof value === 'function') {
		return value(element)
	} else {
		return null
	}
}

export default function closest (element, target, context = null) {
	let current = element

	do {
		if (matches(current, target, context)) {
			return current
		}

		if (current === context) {
			return false
		}
		current = current.parentNode
	} while (current && current !== document.body && current !== document)

	return null
}
