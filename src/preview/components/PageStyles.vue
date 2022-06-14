<script>
import ElementStyles from './ElementStyles.vue';
import Options from '../modules/Options';
import { h } from 'vue';
import { usePseudoSelectors, useOptionsSchemas } from '/@/common/composables';

export default {
	name: 'PageStyles',
	props: {
		cssClasses: {
			type: [Object, Array],
		},
		pageSettingsModel: {
			type: Object,
		},
		pageSettingsSchema: {
			type: Object,
		},
	},
	setup(props) {
		const { getSchema } = useOptionsSchemas();
		const optionsSchema = getSchema('styles');

		return () => {
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
	},
};
</script>
