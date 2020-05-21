<script>
export default {
	name: 'TabLink',
	props: {
		tab: {
			type: Object,
			required: true
		}
	},
	data () {
		return {}
	},
	render (h) {
		const title = this.tab.$slots.title || this.tab.name
		return h(
			'div',
			{
				class: {
					'znpb-tabs__header-item--active': this.tab.isActive,
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
