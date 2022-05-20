<template>
	<PageTemplate class="znpb-performancePage">
		<h3>{{ $translate('performance') }}</h3>

		<OptionsForm v-model="computedModel" :schema="schema" class="znpb-performanceForm" />
	</PageTemplate>
</template>

<script setup>
import { computed } from 'vue';
import { useBuilderOptionsStore } from '@common/store';

const { getOptionValue, updateOptionValue, debouncedSaveOptions } = useBuilderOptionsStore();

const computedModel = computed({
	get() {
		return getOptionValue('performance', {});
	},
	set(newValue) {
		if (newValue === null) {
			updateOptionValue('performance', {}, false);
		} else {
			updateOptionValue('performance', newValue, false);
		}

		debouncedSaveOptions();
	},
});

const schema = window.ZnPbAdminPageData.schemas.performance;
</script>

<style lang="scss">
.znpb-performancePage .znpb-admin-content__center {
	display: flex;
	flex-direction: column;
	overflow: hidden;
}

.znpb-performanceForm {
	padding: 0;
	margin: 0 -5px;
}
</style>
