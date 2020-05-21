<template>
	<PageTemplate>
		<h3>{{$translate('allowed_post_types')}}</h3>
		<div class="znpb-admin-posts-wrapper">
			<PostTypeTab v-for="(post,i) in getPostTypes" :key="i" :post="post"/>
		</div>
		<template slot="right">
			<p class="znpb-admin-info-p">{{$translate('set_allowed_types')}}</p>

		</template>
	</PageTemplate>

</template>

<script>
import { mapGetters } from 'vuex'
import PostTypeTab from './PostTypeTab.vue'

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
		])

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
