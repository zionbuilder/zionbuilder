<template>
	<PageTemplate>
		<h3>{{$translate('allowed_post_types')}}</h3>
		<div class="znpb-admin-posts-wrapper">
			<PostTypeTab v-for="(post,i) in getPostTypes" :key="i" :post="post"/>
		</div>
		<template v-slot:right>
			<p class="znpb-admin-info-p">{{$translate('set_allowed_types')}}</p>
		</template>
	</PageTemplate>

</template>

<script>
import { mapGetters } from 'vuex'
import PostTypeTab from './PostTypeTab.vue'
import { useDataSets } from '@zionbuilder/models'
export default {
	name: 'AllowedPostTypes',
	components: {
		PostTypeTab
	},
	data () {
		return {
			loaded: false
		}
	},
	computed: {
		...mapGetters([
			'getPostTypes'
		]),
		getPostTypes () {
			const { getPostTypes } = useDataSets()
			return getPostTypes()
		}
	}
}
</script>
<style lang="scss">

.znpb-admin-posts-wrapper {
	display: grid;

	grid-column-gap: 20px;
	grid-template-columns: 1fr 1fr;
}
</style>
