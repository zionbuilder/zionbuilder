export default function getOptionValue (savedValues, path, defaultValue) {
	const option = savedValues || {}
	const pathArray = path.split('.')
	const pathLength = pathArray.length

	let returnValue = defaultValue
	let activeValue = option

	for (let [index, pathItem] of pathArray.entries()) {
		if (!activeValue || typeof activeValue[pathItem] === 'undefined') {
			break
		}

		if (index === pathLength - 1) {
			returnValue = activeValue[pathItem]
		}

		activeValue = activeValue[pathItem]
	}

	return returnValue
}
