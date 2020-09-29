<script>
export default {
	name: 'RenderTag',
	functional: true,
	inject: {
		elementInfo: {
			default: {}
		}
	},
	props: {
		tagId: {
			type: String,
			required: true
		}
	},
	render (createElement, context) {
		const tagId = context.props.tagId
		const children = context.children

		if (children.length === 0) {
			console.warn('RenderTag must have only one child. Remaining childs will not be rendered. For rendering repeater option data please use RemderTagGroup')
			return
		}

		if (children && children.length > 1) {
			console.warn('RenderTag must have only one child. Remaining childs will not be rendered')
			return
		}

		const vNode = children[0]
		const elementInstance = context.injections.elementInfo.elementInstance
		const renderAttributes = elementInstance.renderAttributes
		const extraData = vNode.data

		// Check to see if we have tags associated to this tagId
		if (typeof renderAttributes[tagId] !== 'undefined') {
			const { class: cssClasses, ...remainingAttributes } = renderAttributes[tagId]

			// Set css classes
			if (cssClasses) {
				extraData.class = extraData.class || {}
				cssClasses.forEach(cssClass => {
					extraData.class[cssClass] = true
				})
			}

			// Set other attributes
			if (Object.keys(remainingAttributes).length > 0) {
				extraData.attrs = {
					...(extraData.attrs || {}),
					...remainingAttributes
				}
			}
		}

		return [children]
	}
}
</script>
