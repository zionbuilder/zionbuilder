<template>
	<PageTemplate>
		<h3>{{ __('Appearance', 'zionbuilder') }}</h3>

		<OptionsForm v-model="computedModel" :schema="schema" class="znpb-appearancePageForm" />

		<template #right>
			<div>
				<p class="znpb-admin-info-p">{{ __('Builder theme', 'zionbuilder') }}</p>
				<p class="znpb-admin-info-p">
					{{
						__(
							'By changing the builder theme, it will be applied on all pages where the builder is active, as well as all the builder admin pages',
							'zionbuilder',
						)
					}}
				</p>
			</div>
		</template>
	</PageTemplate>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { computed, watch } from 'vue';
import { useBuilderOptionsStore } from '@zb/store';

const { getOptionValue, updateOptionValue } = useBuilderOptionsStore();
const schema = window.ZnPbAdminPageData.appearance.schema;

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
</script>

<style>
.znpb-appearancePageForm {
	padding: 0;
	margin: 0 -5px;
}
</style>
