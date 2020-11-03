import { applyFilters } from '@zb/hooks'

export class Options {
	// Private keys
	_options: {[key: string]: any} = {}

	constructor(data, schema, options) {
		this._options = options

		// Allow external data modification
		this.data = applyFilters('zionbuilder/options/model', data, this)

		// Set defaults and extract render attributes and custom css
		this.parseOptions(schema, this.data)

		// Parse data
		// const cssSelector = `#${element.elementCssId}`

		// const optionsInstance = new Options(schema, model, cssSelector, {
		// 	onLoadingStart: this.onLoadingStart,
		// 	onLoadingEnd: this.onLoadingEnd
		// })

		// let { options, renderAttributes, customCSS } = optionsInstance.parseData()

		// if (this.element.elementTypeModel.style_elements) {
		// 	Object.keys(this.element.elementTypeModel.style_elements).forEach(styleId => {
		// 		if (options._styles && options._styles[styleId] && options._styles[styleId].styles) {
		// 			const styleConfig = this.element.elementTypeModel.style_elements[styleId]
		// 			const formattedSelector = styleConfig.selector.replace('{{ELEMENT}}', `#${this.element.elementCssId}`)
		// 			customCSS += getStyles(formattedSelector, options._styles[styleId].styles)
		// 		}
		// 	})
		// }

		// // Filter the custom css
		// customCSS = applyFilters('zionbuilder/element/custom_css', customCSS, optionsInstance, this)

		// this.options = options
		// this.renderAttributes = renderAttributes
		// this.customCSS = customCSS

		// // Add custom css classes
		// this.applyCustomClassesToRenderTags()


		// Give access to all options
		Object.assign(this, data)
	}

	parseData () {


		return {
			options: options,
			renderAttributes: this.renderAttributes,
			customCSS: this.getCustomCSS()
		}
	}

	parseOptions (schema, model, index = null) {
		Object.keys(schema).forEach((optionId) => {
			const singleOptionSchema = schema[optionId]
			let dependencyPassed = this.checkDependency(singleOptionSchema, model)

			if (!dependencyPassed) {
				return false
			}

			if (typeof singleOptionSchema.is_layout !== 'undefined' && singleOptionSchema.is_layout) {
				if (typeof singleOptionSchema.child_options !== 'undefined') {
					this.parseOptions(singleOptionSchema.child_options, model)
				}
			} else {
				if (typeof model[optionId] !== 'undefined') {
					if (this._options.parsers)  {
						this._options.parsers.forEach(callback => {
							callback(singleOptionSchema, model[optionId], index)
						})
					}

					// Check for images
					// this.setPropperImage(optionId, singleOptionSchema, model)

					// // Get custom css
					// this.setCustomCSS(singleOptionSchema, model[optionId], index)
				} else if (typeof singleOptionSchema.default !== 'undefined') {
					model[optionId] = singleOptionSchema.default
				}

				if (typeof singleOptionSchema.child_options !== 'undefined') {
					if (singleOptionSchema.type === 'repeater') {
						if (typeof model[optionId] !== 'undefined' && Array.isArray(model[optionId])) {
							model[optionId].forEach((optionValue, index) => {
								this.parseOptions(singleOptionSchema.child_options, optionValue, index)
							})
						}
					} else {
						const savedValue = typeof model[optionId] !== 'undefined' ? model[optionId] : []
						this.parseOptions(singleOptionSchema.child_options, savedValue)

						if (Object.keys(savedValue).length > 0) {
							model[optionId] = savedValue
						}
					}
				}
			}
		})

		return model
	}

	checkDependency (optionSchema, model) {
		let passedDependency = true

		if (optionSchema.dependency) {
			optionSchema.dependency.forEach((dependencyConfig) => {
				if (!passedDependency) {
					return
				}

				passedDependency = this.checkSingleDependency(dependencyConfig, model)
			})
		}

		return passedDependency
	}

	checkSingleDependency (dependencyConfig, model) {
		const { type = 'includes', option, option_path: optionPath, value: searchValue } = dependencyConfig
		let optionValue = null

		if (option) {
			optionValue = typeof model[option] !== 'undefined' ? model[option] : null
		} else if (optionPath) {
			optionValue = getOptionValue(this.model, optionPath)
		}

		if (type === 'includes' && searchValue.includes(optionValue)) {
			return true
		} else if (type === 'not_in' && !searchValue.includes(optionValue)) {
			return true
		}

		return false
	}

}