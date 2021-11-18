<template>
	<PageTemplate class="znpb-admin-content-wrapper">
		<h3>{{$translate('custom_code')}}</h3>

		<OptionsForm
			:schema="schema"
			v-model="computedModel"
			class="znpb-appearancePageForm"
		/>
	</PageTemplate>
</template>

<script>
import { computed } from 'vue'
import { useBuilderOptions } from '@zionbuilder/composables'
import { debounce } from 'lodash-es'

export default {
	name: 'CustomCode',
	setup () {
		const { getOptionValue, updateOptionValue, saveOptionsToDB, deleteOptionValue } = useBuilderOptions()
		const debouncedSaveOptions = debounce(saveOptionsToDB, 700)

		const computedModel = computed({
			get () {
				return getOptionValue('custom_code', {})
			},
			set (newValue) {
				console.log({ newValue });
				if (newValue === null) {
					deleteOptionValue('custom_code', false)
				} else {
					updateOptionValue('custom_code', newValue, false)
				}

				debouncedSaveOptions()
			}
		})


		const schema = window.ZnPbAdminPageData.custom_code.schema

		return {
			computedModel,
			schema
		}
	}
}
</script>

<style>
</style>