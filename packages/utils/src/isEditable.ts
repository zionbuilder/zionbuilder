export function isEditable() {
	let el = document.activeElement; // focused element
	if (el && ~['input', 'textarea'].indexOf(el.tagName.toLowerCase())) {
		return !el.readOnly && !el.disabled;
	}

	el = getSelection().anchorNode; // selected node
	if (!el) return undefined; // no selected node
	el = el.parentNode; // selected element
	return el.isContentEditable;
}