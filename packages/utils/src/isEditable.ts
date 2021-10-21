export function isEditable() {
	let el = document.activeElement; // focused element
	if (el && ~['input', 'textarea'].indexOf(el.tagName.toLowerCase())) {
		return !el.readOnly && !el.disabled;
	}

	return el.isContentEditable;
}