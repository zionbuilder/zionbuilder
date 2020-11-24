export const compileElement = (elementUid, pageContent) => {
	let elementData = pageContent[elementUid]
	let data = {
		...elementData
	}

	if (elementData.content && elementData.content.length > 0) {
		data.content = elementData.content.map(childElementUid => {
			return compileElement(childElementUid, pageContent)
		})
	}

	return data
}
