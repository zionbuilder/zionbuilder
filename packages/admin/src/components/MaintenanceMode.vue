<template>

	<PageTemplate>
		<h3>{{$translate('maintenance_mode')}}</h3>

		<OptionsForm
			:schema="schema"
			v-model="computedModel"
			class="znpb-maintenanceModeForm"
		/>

		<template #right>
			<div>
				<p class="znpb-admin-info-p">{{$translate('system_info')}}</p>
				<p class="znpb-admin-info-p">{{$translate('system_info_desc')}}</p>
			</div>
		</template>
	</PageTemplate>
</template>

<script>
import { computed } from 'vue'
import { useBuilderOptions } from '@zionbuilder/composables'

export default {
	name: 'MaintenanceMode',
	setup (props, { emit }) {
		const { getOptionValue, updateOptionValue } = useBuilderOptions()

		const computedModel = computed({
			get () {
				return getOptionValue('maintenance_mode', {})
			},
			set (newValue) {
				if (newValue === null) {
					updateOptionValue('maintenance_mode', {})
				} else {
					updateOptionValue('maintenance_mode', newValue)
				}

			}
		})

		const schema = window.ZnPbAdminPageData.maintenance_mode.schema

		return {
			computedModel,
			schema
		}
	}
}
</script>

<style>
.znpb-maintenanceModeForm {
	padding: 0;
	margin: 0 -5px;
}
</style>