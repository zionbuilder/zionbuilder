<template>
	<PageTemplate>
		<h3>{{ $translate('appearance') }}</h3>

		<OptionsForm v-model="computedModel" :schema="schema" class="znpb-appearancePageForm" />

		<template #right>
			<div>
				<p class="znpb-admin-info-p">{{ $translate('builder_theme') }}</p>
				<p class="znpb-admin-info-p">{{ $translate('builder_theme_desc') }}</p>
			</div>
		</template>
	</PageTemplate>
</template>

<script>
import { computed, watch } from 'vue';
import { useBuilderOptionsStore } from '@common/store';

export default {
	name: 'Appearance',
	setup() {
		const { getOptionValue, updateOptionValue } = useBuilderOptionsStore();

		const computedModel = computed({
			get() {
				return getOptionValue('appearance', {});
			},
			set(newValue) {
				if (newValue === null) {
					updateOptionValue('appearance', {});
				} else {
					updateOptionValue('appearance', newValue);
				}
			},
		});

		const schema = window.ZnPbAdminPageData.appearance.schema;

		watch(
			() => computedModel.value.builder_theme,
			newValue => {
				if (document.body.classList.contains('toplevel_page_zionbuilder')) {
					if (newValue === 'dark') {
						document.body.classList.add('znpb-theme-dark');
					} else {
						document.body.classList.remove('znpb-theme-dark');
					}
				}
			},
		);

		return {
			computedModel,
			schema,
		};
	},
};
</script>

<style>
.znpb-appearancePageForm {
	padding: 0;
	margin: 0 -5px;
}
</style>
