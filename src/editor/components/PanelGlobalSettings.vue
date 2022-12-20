<template>
	<BasePanel
		:panel-name="__('Options', 'zionbuilder')"
		panel-id="panel-global-settings"
		class="znpb-general-options-panel-wrapper"
	>
		<div class="znpb-accordions-wrapper znpb-fancy-scrollbar">
			<OptionsForm v-model="savedValues" :schema="optionsSchema" :show-changes="false" />
		</div>
	</BasePanel>
</template>

<script>
import { __ } from '@wordpress/i18n';
import { computed } from 'vue';
import BasePanel from './BasePanel.vue';
import { useUIStore, usePageSettingsStore } from '../store';

// Common API
const { translate } = window.zb.i18n;

export default {
	name: 'PanelGlobalSettings',
	components: {
		BasePanel,
	},
	setup() {
		const UIStore = useUIStore();
		const pageSettings = usePageSettingsStore();

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

		const optionsSchema = Object.assign({}, window.ZnPbInitialData.page_settings.schema, cssClassesSchema);

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
