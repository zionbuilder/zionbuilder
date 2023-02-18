<template>
	<BasePanel
		:panel-name="i18n.__('Options', 'zionbuilder')"
		panel-id="panel-global-settings"
		class="znpb-general-options-panel-wrapper"
	>
		<div class="znpb-accordions-wrapper znpb-fancy-scrollbar">
			<OptionsForm v-model="savedValues" :schema="optionsSchema" :show-changes="false" />
		</div>
	</BasePanel>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { computed } from 'vue';
import BasePanel from './BasePanel.vue';
import { useUIStore, usePageSettingsStore } from '../store';

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
		title: i18n.__('Global CSS classes', 'zionbuilder'),
		child_options: {
			global_css_classes: {
				type: 'global_css_classes',
			},
		},
	},
};

const optionsSchema = Object.assign({}, window.ZnPbInitialData.page_settings.schema, cssClassesSchema);
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
