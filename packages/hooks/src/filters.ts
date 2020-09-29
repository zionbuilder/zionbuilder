export default () => {
	const filters = {}

	const addFilter = (id, callback) => {
		if (typeof filters[id] === 'undefined') {
			filters[id] = []
		}

		filters[id].push(callback)
	}

	const applyFilters = (id, value, ...params) => {
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


