export default function (scope) {
	if (scope.document.selection && scope.document.selection.empty) {
		scope.document.selection.empty()
	} else if (scope.getSelection) {
		var sel = scope.getSelection()
		sel.removeAllRanges()
	}
}
