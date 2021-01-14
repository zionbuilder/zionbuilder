<template>
	<PageTemplate>
		<h3>{{$translate('allowed_post_types')}}</h3>
		<div class="znpb-admin-posts-wrapper">

			<div
				v-for="post in dataSets.post_types"
				:key="post.id"
				class="znpb-admin-post-types-tab"
			>

				<span class="znpb-admin-post-types-tab__title">{{post.name}}</span>
				<InputCheckbox
					class="znpb-admin-checkbox-wrapper"
					:rounded="true"
					:optionValue="post.id"
					v-model="allowedPostTypes"
				/>

			</div>
		</div>
		<template #right>
			<p class="znpb-admin-info-p">
				{{$translate('set_allowed_types')}}
			</p>
		</template>
	</PageTemplate>

</template>

<script>
import { computed } from 'vue'
import { useDataSets } from '@zb/components'
import { useBuilderOptions } from '@zionbuilder/composables'

export default {
	name: 'AllowedPostTypes',
	setup () {
		const { dataSets } = useDataSets()
		const { getOptionValue, updateOptionValue, deleteOptionValue } = useBuilderOptions()

		const allowedPostTypes = computed({
			get: () => getOptionValue('allowed_post_types'),
			set: (newValue) => updateOptionValue('allowed_post_types', newValue)
		})
		return {
			dataSets,
			allowedPostTypes
		}
	}
}
</script>
<style lang="scss">

.znpb-admin-posts-wrapper {
	display: grid;

	grid-column-gap: 20px;
	grid-template-columns: 1fr 1fr;

	@media (max-width: 991px) {
		grid-template-columns: 1fr;
	}
}

.znpb-admin-post-types-tab {
	@extend %list-item-helper;
	padding: 17px 20px;

	&__title {
		color: $surface-active-color;
		font-weight: 500;
	}
}

.znpb-admin-checkbox-wrapper {
	width: 24px;
	height: 24px;
}
</style>
