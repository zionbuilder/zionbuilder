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
	setup (props) {
		// TODO: reimplement this this.tab.$slots.title
		const title = props.tab.titleSlot ? props.tab.titleSlot() : props.tab.name
		return () => h(
			'div',
			{
				class: {
					'znpb-tabs__header-item--active': props.active,
					[`znpb-tabs__header-item--${props.tab.name.toLowerCase()}`]: props.tab.name,
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
