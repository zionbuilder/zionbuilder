<template>

	<PageTemplate>
		<h3>{{$translate('appearance')}}</h3>

		<OptionsForm
			:schema="schema"
			v-model="computedModel"
			class="znpb-appearancePageForm"
		/>

		<template #right>
			<div>
				<p class="znpb-admin-info-p">{{$translate('builder_theme')}}</p>
				<p class="znpb-admin-info-p">{{$translate('builder_theme_desc')}}</p>
			</div>
		</template>
	</PageTemplate>
</template>

<script>
import { computed } from 'vue'
import { useBuilderOptions } from '@zionbuilder/composables'

export default {
	name: 'Appearance',
	setup (props, { emit }) {
		const { getOptionValue, updateOptionValue } = useBuilderOptions()

		const computedModel = computed({
			get () {
				return getOptionValue('appearance', {})
			},
			set (newValue) {
				if (newValue === null) {
					updateOptionValue('appearance', {})
				} else {
					updateOptionValue('appearance', newValue)
				}

			}
		})

		const schema = window.ZnPbAdminPageData.appearance.schema

		return {
			computedModel,
			schema
		}
	}
}
</script>

<style>
.znpb-appearancePageForm {
	padding: 0;
	margin: 0 -5px;
}
</style>