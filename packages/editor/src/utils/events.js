
class EventsManager {
	get getdocuments () {
		const documents = [document]

		// we are inside iframe
		const parentDocument = window.parent.document
		if (parentDocument !== document) {
			documents.push(parentDocument)
		} else {
			const iframe = document.getElementById('znpb-editor-iframe')

			if (iframe) {
				documents.push(iframe.contentDocument)
			}
		}

		return documents
	}

	addEventListener (type, callback, options) {
		this.getdocuments.forEach(doc => {
			doc.addEventListener(type, callback, options)
		})
	}

	removeEventListener (type, callback) {
		this.getdocuments.forEach(doc => {
			doc.removeEventListener(type, callback)
		})
	}
}

export default new EventsManager()
