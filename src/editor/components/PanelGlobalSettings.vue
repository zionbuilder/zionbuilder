<template>
	<BasePanel
		:panel-name="$translate('global_settings_panel')"
		panel-id="panel-global-settings"
		class="znpb-general-options-panel-wrapper"
	>
		<div class="znpb-accordions-wrapper znpb-fancy-scrollbar">
			<OptionsForm v-model="savedValues" :schema="optionsSchema" :show-changes="false" />
		</div>
	</BasePanel>
</template>
<script>
import { computed } from 'vue';
import BasePanel from './BasePanel.vue';
import { useOptionsSchemas } from '@/common/composables';
import { translate } from '@/common/modules/i18n';
import { useUIStore, usePageSettingsStore } from '../store';

export default {
	name: 'PanelGlobalSettings',
	components: {
		BasePanel,
	},
	setup() {
		const UIStore = useUIStore();
		const pageSettings = usePageSettingsStore();
		const { getSchema } = useOptionsSchemas();

		const savedValues = computed({
			get() {
				return pageSettings.settings;
			},
			set(newValues) {
				pageSettings.updatePageSettings(newValues);
			},
		});

		const cssClassesSchema = {
			global_css: {
				type: 'accordion_menu',
				title: translate('global_css_classes'),
				child_options: {
					global_css_classes: {
						type: 'global_css_classes',
					},
				},
			},
		};

		const optionsSchema = Object.assign({}, getSchema('pageSettingsSchema'), cssClassesSchema);

		return {
			savedValues,
			UIStore,
			optionsSchema,
		};
	},
};
</script>
<style lang="scss">
.znpb-general-options-panel-wrapper {
	.znpb-panel__content_wrapper {
		position: relative;
		padding-top: 0;
	}

	.znpb-accordions-wrapper {
		display: flex;
		flex-direction: column;
		flex-grow: 1;
	}
}
</style>
