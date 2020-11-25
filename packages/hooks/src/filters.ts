
export default () => {
	const filters: Hook = {}

	const addFilter = (id: string, callback: CallbackFunction) => {
		if (typeof filters[id] === 'undefined') {
			filters[id] = []
		}

		filters[id].push(callback)
	}

	const applyFilters = (id: string, value: any, ...params: any[]) => {
		if (typeof filters[id] !== 'undefined') {
			filters[id].forEach(callback => {
				value = callback(value, ...params)
			})
		}

		return value
	}

	return {
		addFilter,
		applyFilters
	}
}


