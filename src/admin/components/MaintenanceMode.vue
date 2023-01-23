<template>
	<PageTemplate>
		<h3>{{ i18n.__('Maintenance mode', 'zionbuilder') }}</h3>

		<OptionsForm v-model="computedModel" :schema="schema" class="znpb-maintenanceModeForm" />
	</PageTemplate>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { computed } from 'vue';

const { useBuilderOptionsStore } = window.zb.store;
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
</script>

<style>
.znpb-maintenanceModeForm {
	padding: 0;
	margin: 0 -5px;
}
</style>
