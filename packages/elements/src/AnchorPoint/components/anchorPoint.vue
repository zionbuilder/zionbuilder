<template>
	<div class="zb-anchorPoint">
		<slot name="start" />

		<span v-html="`#${getCssID}`"></span>
		<Icon
			icon="element-anchor-point"
			:size="30"
			color="#B2B2B2"
		/>
		<slot name="end" />
	</div>
</template>

<script>
export default {
	name: 'anchor_point',
	props: ['options', 'element', 'api'],
	computed: {
		getCssID () {
			return (this.options._advanced_options || {})._element_id || this.element.uid
		}
	},
	mounted () {
		console.log('props', this.element)
	}
}
</script>

