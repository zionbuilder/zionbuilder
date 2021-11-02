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