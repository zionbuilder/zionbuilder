<template>
	<PageTemplate>
		<h3>{{$translate('allowed_post_types')}}</h3>
		<div class="znpb-admin-posts-wrapper">
			<PostTypeTab v-for="postType in dataSets.post_types" :post="postType"/>
		</div>
		<template #right>
			<p class="znpb-admin-info-p">{{$translate('set_allowed_types')}}</p>
		</template>
	</PageTemplate>

</template>

<script>

import PostTypeTab from './PostTypeTab.vue'
import { useDataSets } from '@zb/components'

export default {
	name: 'AllowedPostTypes',
	components: {
		PostTypeTab
	},
	setup () {
		const { dataSets } = useDataSets()

		return {
			dataSets
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
