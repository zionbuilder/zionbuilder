export function flattenTemplateData (templateData) {
	let flattenedData = {}
	if (Array.isArray(templateData)) {
		templateData.forEach(elementConfig => {
			let childContent = []
			if (elementConfig.content && elementConfig.content.length > 0) {
				childContent = []

				let childFlattenedData = flattenTemplateData(elementConfig.content)
				flattenedData = { ...flattenedData, ...childFlattenedData }

				if (Array.isArray(elementConfig.content)) {
					elementConfig.content.forEach(childContentData => {
						childContent.push(childContentData.uid)
					})
				}
			}
			elementConfig.uid = elementConfig.uid || generateUID()
			flattenedData[elementConfig.uid] = {
				...elementConfig,
				content: childContent
			}
		})
	}

	return flattenedData
}

export function generateElement (config) {
	const defaults = {
		content: [],
		options: {}
	}

	const validElement = {
		...defaults,
		...config
	}

	validElement.uid = validElement.uid || generateUID()

	return validElement
}

export function generateElements (config) {
	let parentElements = []
	let childElements = {}
	let slots = {}
	const defaults = {
		content: [],
		options: {}
	}

	// don't proceed if we have not received a valid config
	if (Array.isArray(config) && config.length > 0) {
		config.forEach((element) => {
			const validElement = {
				...defaults,
				...element
			}

			validElement.uid = validElement.uid || generateUID()
			const childElementsConfigs = generateElements(element.content)
			validElement.content = childElementsConfigs.parentElements

			if (Object.keys(childElementsConfigs.slots).length > 0) {
				slots = {
					...slots,
					...childElementsConfigs.slots
				}
				validElement.slots = childElementsConfigs.slots
			}

			childElements = {
				...childElements,
				...childElementsConfigs.childElements,
				[validElement.uid]: validElement
			}

			if (validElement.slot) {
				slots[validElement.slot] = [validElement.uid]
			}

			parentElements.push(validElement.uid)
		})
	}

	return {
		parentElements,
		childElements,
		slots
	}
}

function find (list, f) {
	const { length } = list
	let index = 0
	let value

	while (++index < length) {
		value = list[index]

		if (f(value, index, list)) {
			return value
		}
	}
}

export function deepCopy (obj, cache = []) {
	// just return if obj is immutable value
	if (obj === null || typeof obj !== 'object') return obj

	// if obj is hit, it is in circular structure
	const hit = find(cache, c => c.original === obj)

	if (hit) return hit.copy

	const copy = Array.isArray(obj) ? [] : {}
	// put the copy into cache at first
	// because we want to refer it in recursive deepCopy
	cache.push({
		original: obj,
		copy
	})

	Object.keys(obj).forEach(key => {
		copy[key] = deepCopy(obj[key], cache)
	})

	return copy
}

/**
 * Generate a unique ID based on current date
 */
export const generateUID = (function (index, lastDateInSeconds) {
	// Get shorter indexes by setting the start date to 2018 year
	const startDate = new Date('2019')
	return function () {
		const d = new Date()
		const n = d - startDate

		// Set initial date number
		if (lastDateInSeconds === false) {
			lastDateInSeconds = n
		}

		// Reset the index if the date has changed
		if (lastDateInSeconds !== n) {
			index = 0
		}

		// Set the last date so we can compare it on multiple iterations
		lastDateInSeconds = n

		// Increase the unique index
		index += 1

		// Return the unique index
		return 'uid' + n + index
	}
}(0, false))

export function youtubeUrlParser (url) {
	let regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/
	let match = url.match(regExp)
	return (match && match[7].length === 11) ? match[7] : false
}
