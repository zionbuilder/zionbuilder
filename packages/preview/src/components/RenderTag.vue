<script>
import { inject, h, cloneVNode } from 'vue'

export default {
	name: 'RenderTag',
	props: {
		tagId: {
			type: String,
			required: true
		}
	},
	setup (props, { attrs, emit, slots }) {
		const elementInfo = inject('elementInfo')
		const renderAttributes = inject('renderAttributes')
		const childSlot = slots.default()
		const tagId = props.tagId

		if (typeof renderAttributes.value[tagId] !== 'undefined') {
			if (childSlot.length === 0) {
				console.warn('RenderTag must have only one child. Remaining childs will not be rendered. For rendering repeater option data please use RemderTagGroup')
				return
			}

			if (childSlot.length > 1) {
				console.warn('RenderTag must have only one child. Remaining childs will not be rendered')
				return
			}

			const clonedNode = childSlot.map(vnode => cloneVNode(vnode, renderAttributes.value[tagId]))

			return () => clonedNode
		}

		return () => childSlot

	}
}
</script>
