<template>
	<div class="znpb-element-styles__wrapper">
		<div class="znpb-element-styles__media-wrapper">
			<ClassSelectorDropdown
				v-model="computedClasses"
				v-model:activeClass="activeClass"
				:selector="selector"
				:title="title"
				:allow_class_assignments="allow_class_assignments"
				:active-model-value="modelValue.styles"
				@paste-style-model="onPasteToSelector"
			/>

			<PseudoSelectors v-model="computedStyles" />
		</div>

		<OptionsForm
			v-model="computedStyles"
			:schema="getSchema('element_styles')"
			class="znpb-element-styles-option__options-wrapper"
		/>
	</div>
</template>

<script>
import { merge, cloneDeep } from 'lodash-es';
import PseudoSelectors from './PseudoSelectors.vue';
import ClassSelectorDropdown from './ClassSelectorDropdown.vue';
import { useOptionsSchemas } from '@common/composables';
import { useCSSClasses } from '../../../composables';

export default {
	name: 'ElementStyles',
	components: {
		PseudoSelectors,
		ClassSelectorDropdown,
	},
	props: {
		modelValue: {
			type: Object,
			default: {},
		},
		title: {
			type: String,
		},
		selector: {
			type: String,
		},
		allow_class_assignments: {
			type: Boolean,
			required: false,
			default: true,
		},
	},
	setup(props, { emit }) {
		const { getSchema } = useOptionsSchemas();
		const { getClassConfig, updateCSSClass } = useCSSClasses();

		function onPasteToSelector() {
			const { copiedStyles } = useCSSClasses();
			const clonedCopiedStyles = cloneDeep(copiedStyles.value);
			if (!props.modelValue.styles) {
				updateValues('styles', clonedCopiedStyles);
			} else {
				updateValues('styles', merge(props.modelValue.styles, clonedCopiedStyles));
			}
		}

		function updateValues(type, newValue) {
			const clonedValue = { ...props.modelValue };
			if (newValue === null && typeof clonedValue[type]) {
				// If this is used as layout, we need to delete the active pseudo selector
				delete clonedValue[type];
			} else {
				clonedValue[type] = newValue;
			}

			emit('update:modelValue', clonedValue);
		}

		return {
			getSchema,
			getClassConfig,
			updateCSSClass,
			onPasteToSelector,
			updateValues,
		};
	},
	data() {
		return {
			activeClass: null,
		};
	},
	computed: {
		computedClasses: {
			get() {
				return this.modelValue ? this.modelValue.classes || [] : [];
			},
			set(newValue) {
				this.$emit('update:modelValue', {
					...this.modelValue,
					classes: newValue,
				});
			},
		},
		computedStyles: {
			get() {
				if (this.activeClass !== this.selector) {
					const activeClassConfig = this.getClassConfig(this.activeClass);
					if (activeClassConfig) {
						return activeClassConfig.styles;
					}

					// eslint-disable-next-line
					console.warn(`Class with id ${this.activeClass} not found`)
					return {};
				} else {
					return this.modelValue.styles;
				}
			},
			set(newValues) {
				if (this.activeClass !== this.selector) {
					this.updateCSSClass(this.activeClass, {
						styles: newValues,
					});
				} else {
					this.updateValues('styles', newValues);
				}
			},
		},
	},
	created() {
		this.activeClass = this.selector;
	},
};
</script>
<style lang="scss">
.znpb-element-styles {
	&__wrapper {
		display: flex;
		flex-direction: column;
		height: 100%;
	}

	&__media-wrapper {
		position: relative;
		display: flex;
		justify-content: space-between;
		align-items: center;
		flex-grow: 1;
		margin: 0 5px;
	}
}
.znpb-options-form-wrapper.znpb-element-styles-option__options-wrapper {
	padding: 20px 0 0;
}
</style>
