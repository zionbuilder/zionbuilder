<script>
import { h } from 'vue'

export default {
	name: 'TabLink',
	props: {
		tab: {
			type: Object,
			required: true
		},
		active: {
			type: Boolean,
			required: false,
			default: false
		}
	},
	data () {
		return {}
	},
	render () {
		// TODO: reimplement this this.tab.$slots.title
		const title = this.tab.name
		return h(
			'div',
			{
				class: {
					'znpb-tabs__header-item--active': this.active,
					[`znpb-tabs__header-item--${this.tab.name.toLowerCase()}`]: title,
					'znpb-tabs__header-item': true
				}
			},
			[title]
		)
	}
}
</script>
<style lang="scss">

</style>
