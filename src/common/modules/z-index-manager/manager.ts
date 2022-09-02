export const getManager = () => {
	let zIndex = 10000

	const getZindex = () => {
		zIndex++
		return zIndex
	}

	const removeZindex = () => {
		zIndex--
	}

	return {
		getZindex,
		removeZindex
	}
}