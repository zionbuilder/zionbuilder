let zIndex = 9999
const activeZindexes = []
export const getZindex = () => {
	zIndex++
	let returnedZindex = zIndex

	const lastZindex = Math.max(...activeZindexes) || 9999
	if (lastZindex >= zIndex) {
		returnedZindex = lastZindex + 1
	}

	if (activeZindexes.indexOf(zIndex) === -1) {
		activeZindexes.push(returnedZindex)
	}

	return returnedZindex
}

export const removeZindex = (zIndexToRemove) => {
	const zIndexPosition = activeZindexes.indexOf(zIndexToRemove)
	if (zIndexPosition !== -1) {
		activeZindexes.splice(zIndexPosition, 1)
	}

	zIndex--
}
