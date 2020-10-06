import { merge } from 'lodash-es'

export type genericObject = {
	[key: string]: any
}

export default class Model {
	[key: string]: any

	constructor (values: genericObject = {}) {
		Object.assign(this, merge(this.defaults(), values))
	}

	defaults () {
		return {}
	}

	getValuesWithDefaults (values: genericObject) {
		return
	}

	set (id: string, value: any) {
		this[id] = value
	}


}