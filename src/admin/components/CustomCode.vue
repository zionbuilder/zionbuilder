<template>
	<PageTemplate class="znpb-admin-content-wrapper">
		<h3>{{ i18n.__('Custom code', 'zionbuilder') }}</h3>

		<OptionsForm v-model="computedModel" :schema="schema" class="znpb-appearancePageForm" />
	</PageTemplate>
</template>

<script setup lang="ts">
import * as i18n from '@wordpress/i18n';
import { computed } from 'vue';

const { useBuilderOptionsStore } = window.zb.store;
const { getOptionValue, updateOptionValue, deleteOptionValue, debouncedSaveOptions } = useBuilderOptionsStore();
const schema = window.ZnPbAdminPageData.custom_code.schema;

const computedModel = computed({
	get() {
		return getOptionValue('custom_code', {});
	},
	set(newValue) {
		if (newValue === null) {
			deleteOptionValue('custom_code', false);
		} else {
			updateOptionValue('custom_code', newValue, false);
		}

		debouncedSaveOptions();
	},
});
</script>

<style></style>
