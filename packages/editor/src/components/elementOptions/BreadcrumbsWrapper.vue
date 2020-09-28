<template>
	<div
		class="znpb-element-options__breadcrumbs znpb-fancy-scrollbar"
	>
		<Breadcrumbs
			v-if="parents.children.length>0 "
			:element-uid="parents.uid"
			:parents="parents"
			:active-element-uid="getActiveElementUid"
		/>
		<span v-else>This element has no children</span>

	</div>
</template>

<script>
import { mapGetters } from 'vuex'
import Breadcrumbs from './Breadcrumbs.vue'

export default {
	name: 'BreadcrumbsWrapper',
	components: {
		Breadcrumbs
	},
	data () {
		return {}
	},
	computed: {
		...mapGetters([
			'getParentsAndChildren',
			'getActiveElementUid'
		]),
		parents () {
			let par = this.getParentsAndChildren
			if (par.hasOwnProperty('children')) {
				return par.children[0]
			} else return null
		}
	}
}
</script>
