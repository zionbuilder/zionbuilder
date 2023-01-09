<template>
	<templateRender />
</template>

<script lang="ts" setup>
import ElementStyles from './ElementStyles.vue';
import Options from '../modules/Options';
import { h } from 'vue';
const { usePseudoSelectors, useOptionsSchemas } = window.zb.composables;

const props = defineProps<{
	cssClasses?: Record<string, unknown> | Array<Record<string, unknown>>;
	pageSettingsModel?: Record<string, unknown>;
	pageSettingsSchema?: Record<string, unknown>;
}>();

const { getSchema } = useOptionsSchemas();
const optionsSchema = getSchema('styles');

const templateRender = () => {
	const { activePseudoSelector } = usePseudoSelectors();
	const returnVnodes = [];

	const createVnode = function (styles) {
		return h(ElementStyles, {
			styles,
		});
	};

	// PageSettings
	const pageSettingsOptionsInstance = new Options(props.pageSettingsSchema, props.pageSettingsModel);
	const { customCSS: pageSettingsCustomCSS } = pageSettingsOptionsInstance.parseData();

	returnVnodes.push(createVnode(pageSettingsCustomCSS));

	// Custom css classes
	if (typeof props.cssClasses === 'object' && props.cssClasses !== null) {
		Object.keys(props.cssClasses).forEach(cssClassId => {
			const styleData = props.cssClasses[cssClassId];
			const optionsInstance = new Options(optionsSchema, styleData, [`.zb .${styleData.id}`]);

			const parsedOptions = optionsInstance.parseData();

			let customCSS = window.zb.editor.getCssFromSelector([`.zb .${styleData.id}`], parsedOptions.options);

			// Generate the styles on hover
			if (activePseudoSelector.value && activePseudoSelector.value.id === ':hover') {
				const optionsInstance = new Options(optionsSchema, styleData, [`.zb .${styleData.id}`]);

				const parsedOptions = optionsInstance.parseData();

				customCSS += window.zb.editor.getCssFromSelector([`.zb .${styleData.id}`], parsedOptions.options, {
					forcehoverState: true,
				});
			}

			returnVnodes.push(createVnode(customCSS));
		});
	}

	return returnVnodes;
};
</script>
