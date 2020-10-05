export const createInstance = () => {
	const documents = []

	const getDocuments = () => {
		return documents
	}

	const addDocument = (document) => {
		documents.push(document)
	}

	const addEventListener = (type, callback, options) => {
		documents.forEach(doc => {
			doc.addEventListener(type, callback, options)
		})
	}

	const removeDocument = (document) => {
		const index = documents.indexOf(document)

		if (index !== null) {
			documents.splice(index, 1)
		}
	}

	const removeEventListener = (type, callback) => {
		documents.forEach(doc => {
			doc.removeEventListener(type, callback)
		})
	}

	return {
		getDocuments,
		addDocument,
		removeDocument,
		addEventListener,
		removeEventListener
	}
}



// class EventsManager {
// 	get getdocuments () {
// 		const documents = [document]

// 		// we are inside iframe
// 		const parentDocument = window.parent.document
// 		if (parentDocument !== document) {
// 			documents.push(parentDocument)
// 		} else {
// 			const iframe = document.getElementById('znpb-editor-iframe')

// 			if (iframe) {
// 				documents.push(iframe.contentDocument)
// 			}
// 		}

// 		return documents
// 	}

// 	addEventListener (type, callback, options) {
// 		this.getdocuments.forEach(doc => {
// 			doc.addEventListener(type, callback, options)
// 		})
// 	}

// 	removeEventListener (type, callback) {
// 		this.getdocuments.forEach(doc => {
// 			doc.removeEventListener(type, callback)
// 		})
// 	}
// }

// export default new EventsManager()
