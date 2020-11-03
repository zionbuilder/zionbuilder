import { getOptionValue, getStyles } from '@zb/utils'
import { applyFilters, getImage } from '@zb/hooks'
/**
 * Will parse the option schema in order to get the render attributes
 * and custom css
 */
export default class Options {
	constructor (schema = {}, model = {}, selector, options) {
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




	setPropperImage (optionId, schema, model) {
		if (schema.type === 'image' && schema.show_size === true && model[optionId]) {
			const imageConfig = model[optionId]

			// Only start loading if we need to fetch the image from server
			if (imageConfig && imageConfig.image && imageConfig.image_size && imageConfig.image_size !== 'full') {
				this.startLoading()
				getImage(model[optionId]).then((image) => {
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

		optionsModel[optionId] = newValues
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



	getValue (optionPath, defaultValue) {
		return getOptionValue(this.model, optionPath, defaultValue)
	}
}