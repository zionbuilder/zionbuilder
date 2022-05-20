<template>
	<PageTemplate>
		<h3>{{ $translate('maintenance_mode') }}</h3>

		<OptionsForm v-model="computedModel" :schema="schema" class="znpb-maintenanceModeForm" />
	</PageTemplate>
</template>

<script>
import { computed } from 'vue';
import { useBuilderOptionsStore } from '@common/store';

export default {
	name: 'MaintenanceMode',
	setup(props, { emit }) {
		const { getOptionValue, updateOptionValue } = useBuilderOptionsStore();

		const computedModel = computed({
			get() {
				return getOptionValue('maintenance_mode', {});
			},
			set(newValue) {
				if (newValue === null) {
					updateOptionValue('maintenance_mode', {});
				} else {
					updateOptionValue('maintenance_mode', newValue);
				}
			},
		});

		const schema = window.ZnPbAdminPageData.maintenance_mode.schema;

		return {
			computedModel,
			schema,
		};
	},
};
</script>

<style>
.znpb-maintenanceModeForm {
	padding: 0;
	margin: 0 -5px;
}
</style>
