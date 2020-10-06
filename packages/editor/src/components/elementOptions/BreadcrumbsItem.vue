<template>
	<div
		@click.stop="onItemClick"
		@mouseenter.capture="highlightElement"
		@mouseleave="unHighlightElement"
		class="znpb-element-options__vertical-breadcrumbs-item"
		:class="{'znpb-element-options__vertical-breadcrumbs-item--active': getActiveElementUid===elementUid}"
	>

		<span>{{elementName}}</span>
		<template v-if="parents.length > 0">
			<Breadcrumbs
				class="znpb-element-options__vertical-breadcrumbs-wrapper--inner"
				v-for="(parent, i) in parents"
				:key="i"
				:elementUid="parent.uid"
				:parents="parent"
				:active-element-uid="getActiveElementUid"
			/>
		</template>
	</div>
</template>

<script>
import templateElementMixin from '../../mixins/templateElement.js'
import { mapGetters, mapActions } from 'vuex'

export default {
	name: 'BreadcrumbsItem',
	mixins: [
		templateElementMixin
	],
	components: {
		Breadcrumbs: () => import('./Breadcrumbs.vue')
	},
	props: {
		parents: {
			type: Array,
			required: false
		}
	},
	computed: {
		...mapGetters([
			'getActiveElementUid'
		])

	},
	methods: {
		...mapActions([
			'setActiveElement'
		]),

		onItemClick (elementUid) {
			this.setActiveElement(this.elementUid)
			this.$zb.panels.openPanel('PanelElementOptions')
		}
	}
}
</script>
