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
import { ref, watch } from 'vue';
import { merge, cloneDeep } from 'lodash-es';
import PseudoSelectors from './PseudoSelectors.vue';
import ClassSelectorDropdown from './ClassSelectorDropdown.vue';
import { useCSSClassesStore } from '/@/editor/store';

const { useOptionsSchemas } = window.zb.composables;

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
		const cssClasses = useCSSClassesStore();
		const activeClass = ref(props.selector);

		watch(
			() => props.selector,
			newValue => {
				activeClass.value = newValue;
			},
		);

		function onPasteToSelector() {
			const clonedCopiedStyles = cloneDeep(cssClasses.copiedStyles);
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
			cssClasses,
			onPasteToSelector,
			updateValues,
			activeClass,
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
					const activeClassConfig = this.cssClasses.getClassConfig(this.activeClass);
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
					this.cssClasses.updateCSSClass(this.activeClass, {
						styles: newValues,
					});
				} else {
					this.updateValues('styles', newValues);
				}
			},
		},
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
