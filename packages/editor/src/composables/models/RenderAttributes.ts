export class RenderAttributes {
	public renderAttributes = {}

	parseValue (schema, model, index = null) {
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

				if (schema.responsive_options && model !== null) {
					// Check to see if the option is saved as string
					if (model && typeof model !== 'object') {
						model = {
							default: model
						}
						console.log(model);
					}

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

	getRenderAttribute (tagID) {
		return this.renderAttributes[tagID]
	}

	clear () {
		this.renderAttributes = {}
	}
}