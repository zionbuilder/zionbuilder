<script>
export default {
	name: 'RenderTagGroup',
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
		},
		repeaterValue: {
			type: Array,
			default () {
				return []
			}
		}
	},
	render (createElement, context) {
		const tagId = context.props.tagId
		const children = context.children || []
		const elementInstance = context.injections.elementInfo.elementInstance
		const renderAttributes = elementInstance.renderAttributes

		const addRenderAttributeToVnode = function (vNode, index) {
			const currentTagId = `${tagId}${index}`
			const extraData = vNode.data

			if (!extraData) {
				return
			}

			// Check to see if we have tags associated to this tagId
			if (typeof renderAttributes[currentTagId] !== 'undefined') {
				const { class: cssClasses, ...remainingAttributes } = renderAttributes[currentTagId]

				// Set css classes
				if (cssClasses) {
					extraData.class = extraData.class || {}
					cssClasses.forEach(cssClass => {
						if (Array.isArray(extraData.class)) {
							extraData.class.push(cssClass)
						} else if (typeof extraData.class === 'object' && extraData.class !== null) {
							extraData.class[cssClass] = true
						}
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
		}

		const vNodes = []
		for (let i = 0; i < context.props.repeaterValue.length; i++) {
			const vNode = context.scopedSlots.item({
				item: context.props.repeaterValue[i],
				index: i
			})[0]
			addRenderAttributeToVnode(vNode, i)
			vNodes.push(vNode)
		}

		return [vNodes]
	}
}
</script>
