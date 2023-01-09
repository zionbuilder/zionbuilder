<template>
	<div class="znpb-element-styles__media-wrapper">
		<div class="znpb-element-styles__mediaInner">
			<ClassSelectorDropdown
				v-model="computedClasses"
				v-model:activeClass="computedActiveClass"
				:selector="selector"
				:title="title"
				:allow_class_assignments="allow_class_assignments"
				:active-model-value="computedStyles"
				@paste-style-model="onPasteToSelector"
			/>

			<PseudoSelectors v-model="computedStyles" />
		</div>
		<div v-if="computedClasses.length" class="znpb-element-styles__mediaActiveClasses">
			<span
				v-for="cssClass in computedClasses"
				:key="cssClass"
				class="znpb-element-styles__mediaActiveClass"
				:class="{ 'znpb-element-styles__mediaActiveClass--active': cssClass === computedActiveClass }"
				@click.prevent="toggleClass(cssClass)"
			>
				.{{ cssClass }}

				<Icon
					v-znpb-tooltip="__('Remove class', 'zionbuilder')"
					class="znpb-element-styles__mediaActiveClassRemove"
					icon="close"
					@click.stop.prevent="removeCssCLass(cssClass)"
				/>
			</span>
		</div>
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { computed } from 'vue';
import { merge, cloneDeep } from 'lodash-es';
import PseudoSelectors from './PseudoSelectors.vue';
import ClassSelectorDropdown from './ClassSelectorDropdown.vue';
import { useCSSClassesStore } from '/@/editor/store';

const cssClasses = useCSSClassesStore();

const props = withDefaults(
	defineProps<{
		modelValue: {
			classes?: string[];
		};
		title: string;
		selector: string;
		// eslint-disable-next-line vue/prop-name-casing
		allow_class_assignments: boolean;
		activeClass: string;
	}>(),
	{
		modelValue: () => ({}),
		allow_class_assignments: true,
	},
);

const emit = defineEmits(['update:modelValue', 'update:activeClass']);

const computedActiveClass = computed({
	get() {
		return props.activeClass;
	},
	set(newValue) {
		emit('update:activeClass', newValue);
	},
});

// Computed
const computedStyles = computed({
	get() {
		if (props.activeClass !== props.selector) {
			const activeClassConfig = cssClasses.getClassConfig(props.activeClass);
			if (activeClassConfig) {
				return activeClassConfig.styles;
			}

			// eslint-disable-next-line
			console.warn(`Class with id ${props.activeClass} not found`)
			return {};
		} else {
			return props.modelValue.styles;
		}
	},
	set(newValue) {
		if (props.activeClass !== props.selector) {
			const activeClassConfig = cssClasses.getClassConfig(props.activeClass);
			if (activeClassConfig) {
				activeClassConfig.styles = newValue;
			}
		} else {
			updateValues('styles', newValue);
		}
	},
});

const computedClasses = computed({
	get() {
		return props.modelValue ? props.modelValue.classes || [] : [];
	},
	set(newValue) {
		emit('update:modelValue', {
			...props.modelValue,
			classes: newValue,
		});
	},
});

function onPasteToSelector() {
	const clonedCopiedStyles = cloneDeep(cssClasses.copiedStyles);
	if (!props.modelValue.styles) {
		if (clonedCopiedStyles !== null) {
			updateValues('styles', clonedCopiedStyles);
		}
	} else {
		updateValues('styles', merge(props.modelValue.styles, clonedCopiedStyles));
	}
}

function updateValues(type: string, newValue: Record<string, unknown>) {
	const clonedValue = { ...props.modelValue };
	if (newValue === null && typeof clonedValue[type]) {
		// If this is used as layout, we need to delete the active pseudo selector
		delete clonedValue[type];
	} else {
		clonedValue[type] = newValue;
	}

	emit('update:modelValue', clonedValue);
}

function removeCssCLass(cssClass: string) {
	const existingClasses = [...computedClasses.value];
	const classIndex = existingClasses.indexOf(cssClass);

	if (classIndex === -1) {
		return;
	}

	existingClasses.splice(classIndex, 1);

	computedClasses.value = existingClasses;
	// Check if the active class was the one deleted
	if (props.activeClass === cssClass) {
		computedActiveClass.value = props.selector;
	}
}

function toggleClass(cssClass: string) {
	if (computedActiveClass.value === cssClass) {
		computedActiveClass.value = props.selector;
	} else {
		computedActiveClass.value = cssClass;
	}
}
</script>

<style lang="scss">
.znpb-element-styles__mediaInner {
	position: relative;
	display: flex;
	justify-content: space-between;
	align-items: center;
	flex-grow: 1;
}

.znpb-element-styles__mediaActiveClasses {
	margin-top: 10px;
}

.znpb-element-styles__mediaActiveClass {
	background: #3d44a7;
	padding: 0 0 0 5px;
	font-size: 9px;
	border-radius: 2px;
	color: #fff;
	display: inline-flex;
	align-items: center;
	cursor: pointer;
	margin-right: 4px;
}

.znpb-element-styles__mediaActiveClassRemove {
	padding: 3px 5px;
	cursor: pointer;
	font-size: 9px;
	background: #1d3d79;
	margin-left: 4px;
	border-radius: 0 2px 2px 0;

	&:hover {
		background: red;
		border-radius: 0 2px 2px 0;
	}
}

.znpb-element-styles__mediaActiveClass--active {
	background: #009500;

	.znpb-element-styles__mediaActiveClassRemove {
		background: #026b02;
	}
}
</style>
