export function updateOptionValue (options, path, newValue) {
	// console.log(options)
	const newOptions = { ...options }
	const pathArray = path.split('.')
	const pathLength = pathArray.length
	let activeValue = newOptions

	pathArray.forEach((pathItem, index) => {
		if (index === pathLength - 1) {
			activeValue[pathItem] = newValue
		} else {
			activeValue[pathItem] = { ...activeValue[pathItem] } || {}
		}
		activeValue = activeValue[pathItem]
	})

	return newOptions
}
