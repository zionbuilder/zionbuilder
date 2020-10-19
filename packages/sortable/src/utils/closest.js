function matches (element, value, context = null) {
	if (!value) {
		return false
	} else if (value === '> *') {
		return matches(element.parentElement, context)
	} else if (value instanceof HTMLElement && value.nodeType > 0) {
		console.log(element, value);
		return element === value
	} else if (typeof value === 'string') {
		return element.matches(value)
	} else if (value instanceof NodeList || value instanceof Array) {
		return [...value].includes(element)
	} else if (typeof value === 'function') {
		return value(element)
	}

	return false
}

export default function closest (element, target, context = null) {
	let current = element

	do {
		if (current && matches(current, target, context)) {
			return current
		}

		if (current === context) {
			return false
		}

		current = current.parentElement
	} while (current && current !== document.body)

	return null
}
