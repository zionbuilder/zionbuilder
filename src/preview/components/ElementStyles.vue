<script>
export default {
	functional: true,
	name: 'ElementStyles',
	props: {
		styles: {
			type: String
		}
	},
	render (h, context) {
		if (context.props.styles) {
			return h('style', {
				domProps: {
					innerHTML: context.props.styles
				}
			})
		}
		return null
	}
}
</script>
