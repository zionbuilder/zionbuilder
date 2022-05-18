<template>
	<PageTemplate class="znpb-librarySourcesPage">
		<h3>{{ $translate('library_share') }}</h3>

		<OptionsForm v-model="computedModel" :schema="schema" class="znpb-libraryShareForm" />
	</PageTemplate>
</template>

<script>
import { computed } from 'vue';
import { useBuilderOptions } from '@common/composables';

export default {
	name: 'LibraryPage',
	setup(props) {
		const { getOptionValue, updateOptionValue, debouncedSaveOptions } = useBuilderOptions();

		const computedModel = computed({
			get() {
				return getOptionValue('library_share', {});
			},
			set(newValue) {
				if (newValue === null) {
					updateOptionValue('library_share', {}, false);
				} else {
					updateOptionValue('library_share', newValue, false);
				}

				debouncedSaveOptions();
			},
		});

		const schema = window.ZnPbAdminPageData.schemas.library_share;

		return {
			computedModel,
			schema,
		};
	},
};
</script>

<style>
.znpb-librarySourcesPage .znpb-admin-content__center {
	display: flex;
	flex-direction: column;
	overflow: hidden;
}

.znpb-libraryShareForm {
	padding: 0;
	margin: 0 -5px;
}
</style>
