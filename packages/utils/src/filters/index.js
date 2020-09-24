const filters = {}

export function addFilter (id, callback) {
	if (typeof filters[id] === 'undefined') {
		filters[id] = []
	}

	filters[id].push(callback)
}

export function applyFilters (id, value, ...params) {
	if (typeof filters[id] !== 'undefined') {
		filters[id].forEach(callback => {
			value = callback(value, ...params)
		})
	}

	return value
}
