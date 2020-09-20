export default class {
	constructor (schemas = {}) {
		this.schemas = schemas
	}

	addSchemas = (schemas) => {
		this.schemas = {
			...this.schemas,
			...schemas
		}
	}

	getSchema = (id) => {
		if (typeof this.schemas[id] === 'undefined') {
			console.warn(`Schema with id ${id} was not found`)
			return {}
		}

		// Prevent Vue reactivity and changes from usages
		return Object.freeze(this.schemas[id])
	}
}
