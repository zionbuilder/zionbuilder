
import { getOptionValue, updateOptionValue } from '@zb/utils'

export default class {
	constructor (schema, values) {
		this.schema = schema
		this.values = values
	}

	getSchema () {
		return this.schema
	}

	getModel () {
		return this.values
	}

	parseSchema (schema, values) {
		return schema
	}

	parseModel (values) {
		return values
	}

	updateValues = (values) => {
		this.values = values
	}

	updateValue = (optionId, value) => {
		const paths = optionId.split('.')

		paths.reduce((acc, key, index) => {
			if (index === paths.length - 1) {
				acc[key] = value
				return true
			}

			acc[key] = acc[key] || {}
			return acc[key]
		}, this.values)
	}

	getValues = () => {
		return this.values
	}

	getValue = (optionPath, defaultValue) => {
		return getOptionValue(this.values, optionPath, defaultValue)
	}
}
