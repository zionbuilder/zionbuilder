<script>
import { h } from 'vue'

export default {
	functional: true,
	name: 'ElementStyles',
	props: {
		styles: {
			type: String
		}
	},
	render (h, context) {
		if (context.styles) {
			return h('style', {
				domProps: {
					innerHTML: context.styles
				}
			})
		}
		return null
	}
}
</script>
