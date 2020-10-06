import { merge } from 'lodash-es'
import Collection from './Collection'

export type genericObject = {
	[key: string]: any
}

export default class Model {
	[key: string]: any
	private readonly collection: Collection

	constructor (values: genericObject = {}, collection: Collection) {
		Object.assign(this, merge(this.defaults(), values))

		// Assign the collection
		this.collection = collection
	}

	defaults () {
		return {}
	}

	getCollection(): Collection {
		return this.collection
	}

	getValuesWithDefaults (values: genericObject) {
		return
	}

	set (id: string, value: any) {
		this[id] = value
	}


}