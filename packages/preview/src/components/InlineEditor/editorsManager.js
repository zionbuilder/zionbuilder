export default {
	activeEditor: null,
	closeEditor () {
		if (this.activeEditor) {
			this.activeEditor.hideEditor()
		}
		this.activeEditor = null
	},
	opendEditor (editor) {
		if (this.activeEditor === editor) {
			return
		}
		this.closeEditor()
		this.activeEditor = editor
	}
}
