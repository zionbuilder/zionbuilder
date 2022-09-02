export function isEditable(el = document.activeElement): boolean {
	if (el && ~['input', 'textarea'].indexOf(el.tagName.toLowerCase())) {
		return !el.readOnly && !el.disabled;
	}

	// Check if current element is an iframe
	if (el && el.contentDocument) {
		return isEditable(el.contentDocument.activeElement);
	}

	return el.isContentEditable;
}
