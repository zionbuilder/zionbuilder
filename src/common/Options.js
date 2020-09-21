import { applyFilters } from '@zb/filters'
import { getOptionValue, getStyles } from '@/utils'
import Vue from '@zb/vue'
import { getSchema } from '@zb/schemas'

/**
 * Will parse the option schema in order to get the render attributes
 * and custom css
 */
export default class Options {
	constructor (schema, model, selector = '', options = {}) {
		// TODO: work on data after moving to computed getter
		this.model = JSON.parse(JSON.stringify(model))
		this.schema = schema
		this.selector = selector
		this.options = options

		this.customCSS = {
			default: {},
			laptop: {},
			tablet: {},
			mobile: {}
		}

		this.renderAttributes = {}
	}

	startLoading () {
		if (typeof this.options.onLoadingStart === 'function') {
			this.options.onLoadingStart()
		}
	}

	endLoading () {
		if (typeof this.options.onLoadingEnd === 'function') {
			this.options.onLoadingEnd()
		}
	}

	parseData () {
		// Allow external data modification
		const options = applyFilters('zionbuilder/options/model', this.model, this)

		// Set defaults and extract render attributes and custom css
		this.parseOptions(this.schema, options)

		return {
			options: options,
			renderAttributes: this.renderAttributes,
			customCSS: this.getCustomCSS()
		}
	}

	/**
	 * Returns the valid schema by processing dependencies, placeholders and default value
	 *
	 * @param {*} schema
	 * @param {*} model
	 */
	getValidSchema (schema, model) {
		let returnSchema = {}
		schema = schema || this.schema
		model = model || this.model

		Object.keys(schema).forEach((optionId) => {
			const singleOptionSchema = schema[optionId]
			let dependencyPassed = this.checkDependency(singleOptionSchema, model)

			if (!dependencyPassed) {
				return false
			}

			// Set data sources
			if (typeof singleOptionSchema.data_source !== 'undefined') {
				if (singleOptionSchema.data_source === 'fonts') {
					singleOptionSchema.options = window.zb.editor.store.getters.getFontListForOption
					delete singleOptionSchema.data_source
				}
			}

			// Special cases for inputs
			if (singleOptionSchema.type === 'textarea') {
				singleOptionSchema.type = 'textarea'
			}

			// Check for special schemas
			if (singleOptionSchema.type === 'element_styles') {
				singleOptionSchema.child_options = getSchema('styles')
			} else if (singleOptionSchema.type === 'typography') {
				singleOptionSchema.child_options = getSchema('typography')
			} else if (singleOptionSchema.type === 'background_image') {
				singleOptionSchema.child_options = getSchema('background_image')
			}

			if (typeof singleOptionSchema.is_layout !== 'undefined' && singleOptionSchema.is_layout) {
				if (typeof singleOptionSchema.child_options !== 'undefined') {
					returnSchema = {
						...returnSchema,
						...this.getValidSchema(singleOptionSchema.child_options)
					}
				}
			} else {
				if (typeof singleOptionSchema.child_options !== 'undefined') {
					if (singleOptionSchema.type === 'repeater') {

					} else {
						const childSchema = this.getValidSchema(singleOptionSchema.child_options, model[optionId])
						singleOptionSchema.child_options = childSchema
					}
				}
			}

			returnSchema[optionId] = singleOptionSchema
		})

		return schema
	}

	// TODO: move to utils
	isIterable (schema) {
		return Array.isArray(schema) || (schema === Object(schema) && typeof schema !== 'function')
	}

	compilePlaceholders (schema) {
		// Don't proceed if the object is not iterable
		if (!this.isIterable(schema)) {
			return this.compilePlaceholder(schema)
		} else {
			for (const prop in schema) {
				// Don't process sync values
				if (prop !== 'sync') {
					if (schema.hasOwnProperty(prop)) {
						schema[prop] = this.compilePlaceholders(schema[prop])
					}
				}
			}
		}

		return schema
	}

	compilePlaceholder (value) {
		if (typeof value !== 'string') {
			return value
		}

		const replacements = [
			{
				search: /%%ELEMENT_TYPE%%/g,
				replacement: this.replaceElementType
			},
			{
				search: /%%ELEMENT_UID%%/g,
				replacement: this.replaceElementUid
			},
			{
				search: /%%RESPONSIVE_DEVICE%%/g,
				replacement: this.replaceResponsiveDevice
			},
			{
				search: /%%PSEUDO_SELECTOR%%/g,
				replacement: this.replacePseudoSelector
			}
		]

		replacements.forEach((replacementConfig) => {
			value = value.replace(replacementConfig.search, replacementConfig.replacement)
		})

		return value
	}

	/**
	 * Replace %%ELEMENT_TYPE%% with the element name
	 */
	replaceElementType () {
		return this.elementInfo.data.elementTypeConfig.name
	}
	/**
	 * Replace %%ELEMENT_UID%% constant with the element UID
	 */
	replaceElementUid () {
		return this.elementInfo.data.uid
	}
	/**
	 * Replace %%RESPONSIVE_DEVICE%% constant with the element UID
	 */
	replaceResponsiveDevice () {
		return this.getActiveDevice.id
	}
	/**
	 * Replace %%PSEUDO_SELECTOR%% constant with the element UID
	 */
	replacePseudoSelector () {
		return this.getActivePseudoSelector.id
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
					// Check for images
					this.setPropperImage(optionId, singleOptionSchema, model)

					// Get render attributes
					this.setRenderAttributes(singleOptionSchema, model[optionId], index)

					// Get custom css
					this.setCustomCSS(singleOptionSchema, model[optionId], index)
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

	setPropperImage (optionId, schema, model) {
		if (schema.type === 'image' && schema.show_size === true && model[optionId]) {
			const imageConfig = model[optionId]

			// Only start loading if we need to fetch the image from server
			if (imageConfig && imageConfig.image && imageConfig.image_size && imageConfig.image_size !== 'full') {
				this.startLoading()
				window.zb.utils.getImage(model[optionId]).then((image) => {
					if (image) {
						this.setImage(model, optionId, image)
					}
				}).finally(() => {
					this.endLoading()
				})
			}
		}
	}

	setImage (optionsModel, optionId, newValue) {
		const oldImage = (optionsModel[optionId] || {}).image
		if (oldImage === newValue) {
			return
		}

		const newValues = {
			...optionsModel[optionId],
			image: newValue
		}

		Vue.set(optionsModel, optionId, newValues)
	}

	addRenderAttribute (tagId, attribute, value, replace = false) {
		if (!this.renderAttributes[tagId]) {
			this.renderAttributes[tagId] = {}
		}

		const currentAttributes = this.renderAttributes[tagId]

		if (!currentAttributes[attribute]) {
			currentAttributes[attribute] = []
		}

		if (replace) {
			currentAttributes[attribute] = [value]
		} else {
			currentAttributes[attribute].push(value)
		}
	}

	setRenderAttributes (schema, model, index = null) {
		const CSSDeviceMap = {
			default: '',
			laptop: '--lg',
			tablet: '--md',
			mobile: '--sm'
		}

		if (schema.render_attribute) {
			schema.render_attribute.forEach(config => {
				// create render attribute for tag id if doesn't exists
				let tagId = config.tag_id || 'wrapper'
				tagId = index === null ? tagId : `${tagId}${index}`
				const attribute = config.attribute || 'class'
				let attributeValue = config.value || ''

				if (schema.responsive_options && typeof model === 'object' && model !== null) {
					Object.keys(model).forEach(deviceId => {
						// Don't proceed if we do not have a value
						if (!model[deviceId] || typeof CSSDeviceMap[deviceId] === 'undefined') {
							return
						}

						const deviceSavedValue = model[deviceId]
						attributeValue = config.value || ''
						attributeValue = attributeValue.replace('{{RESPONSIVE_DEVICE_CSS}}', CSSDeviceMap[deviceId])
						attributeValue = attributeValue.replace('{{VALUE}}', deviceSavedValue) || deviceSavedValue

						this.addRenderAttribute(tagId, attribute, attributeValue)
					})
				} else {
					// Don't proceed if we do not have a value
					if (!model) {
						return
					}

					attributeValue = attributeValue.replace('{{VALUE}}', model) || model
					this.addRenderAttribute(tagId, attribute, attributeValue)
				}
			})
		}
	}

	setCustomCSS (schema, model, index = null) {
		if (schema.css_style && Array.isArray(schema.css_style)) {
			schema.css_style.forEach(cssStyleConfig => {
				if (schema.responsive_options && typeof model === 'object' && model !== null) {
					this.extractResponsiveCSSRules(schema.type, cssStyleConfig, model, index)
				} else {
					this.extractCSSRule('default', schema.type, cssStyleConfig, model, index)
				}
			})
		}
	}

	extractResponsiveCSSRules (optionType, cssStyleConfig, model, index) {
		if (typeof model !== 'object' || model === null) {
			return ''
		}

		Object.keys(model).forEach(device => {
			const deviceValue = model[device]
			this.extractCSSRule(device, optionType, cssStyleConfig, deviceValue, index)
		})
	}

	extractCSSRule (device, optionType, cssStyleConfig, model, index) {
		let { selector, value } = cssStyleConfig

		if (!selector || !value) {
			return
		}

		selector = selector.replace('{{ELEMENT}}', this.selector)
		value = value.replace('{{VALUE}}', model)

		if (index !== null) {
			selector = selector.replace('{{INDEX}}', index)
		}

		if (optionType === 'element_styles') {
			const mediaStyles = optionValue.styles || {}
			const styles = getStyles(formattedSelector, mediaStyles)

			if (styles) {
				this.addCustomCSS(device, selector, styles)
			}
		} else {
			this.addCustomCSS(device, selector, value)
		}
	}

	addCustomCSS (device, selector, css) {
		if (typeof this.customCSS[device] === 'undefined') {
			return
		}

		this.customCSS[device][selector] = this.customCSS[device][selector] || []
		this.customCSS[device][selector].push(css)
	}

	getCustomCSS () {
		const CSSDeviceMap = {
			laptop: '991.98px',
			tablet: '767.98px',
			mobile: '575.98px'
		}

		let returnedCSS = ''

		Object.keys(this.customCSS).forEach(device => {
			const deviceSlectors = this.customCSS[device]
			const extractedCSS = this.extractStyles(deviceSlectors)

			if (extractedCSS.length === 0) {
				return
			}

			if (device === 'default') {
				returnedCSS += extractedCSS
			} else {
				if (!CSSDeviceMap[device]) {
					return
				}

				const deviceWidth = CSSDeviceMap[device]
				returnedCSS += `@media (max-width: ${deviceWidth}) { ${extractedCSS} }`
			}
		})

		return returnedCSS
	}

	extractStyles (stylesData) {
		let returnedStyles = ''
		if (typeof stylesData === 'object' && stylesData !== null) {
			Object.keys(stylesData).forEach(selector => {
				const styleCSSArray = stylesData[selector]
				returnedStyles += `${selector} { ${styleCSSArray.join(';')} }`
			})
		}

		return returnedStyles
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

	getValue (optionPath, defaultValue) {
		return getOptionValue(this.model, optionPath, defaultValue)
	}
}
