import { Element } from './Element'

export class Options {
	// Private keys
	_element: Element
	_schema: {[key: string]: any} = {}

	constructor(data, element) {
		this._element = element
		this._schema = element.elementTypeModel.options || {}

		// Parse data

		// Give access to all options
		Object.assign(this, data)
	}

	setupModel (model) {
		const cssSelector = `#${this.element.elementCssId}`
		const optionsInstance = new Options(schema, model, cssSelector, {
			onLoadingStart: this.onLoadingStart,
			onLoadingEnd: this.onLoadingEnd
		})

		let { options, renderAttributes, customCSS } = optionsInstance.parseData()

		if (this.element.elementTypeModel.style_elements) {
			Object.keys(this.element.elementTypeModel.style_elements).forEach(styleId => {
				if (options._styles && options._styles[styleId] && options._styles[styleId].styles) {
					const styleConfig = this.element.elementTypeModel.style_elements[styleId]
					const formattedSelector = styleConfig.selector.replace('{{ELEMENT}}', `#${this.element.elementCssId}`)
					customCSS += getStyles(formattedSelector, options._styles[styleId].styles)
				}
			})
		}

		// Filter the custom css
		customCSS = applyFilters('zionbuilder/element/custom_css', customCSS, optionsInstance, this)

		this.options = options
		this.renderAttributes = renderAttributes
		this.customCSS = customCSS

		// Add custom css classes
		this.applyCustomClassesToRenderTags()
	}

}