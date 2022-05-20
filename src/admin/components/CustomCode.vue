<template>
	<PageTemplate class="znpb-admin-content-wrapper">
		<h3>{{ $translate('custom_code') }}</h3>

		<OptionsForm v-model="computedModel" :schema="schema" class="znpb-appearancePageForm" />
	</PageTemplate>
</template>

<script setup>
import { computed } from 'vue';
import { useBuilderOptionsStore } from '@common/store';

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
