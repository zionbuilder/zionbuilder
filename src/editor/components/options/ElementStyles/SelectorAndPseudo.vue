<template>
	<div class="znpb-element-styles__media-wrapper">
		<div class="znpb-element-styles__mediaInner">
			<ClassSelectorDropdown
				v-model:active-global-class="computedActiveGlobalClass"
				:name="computedTitle"
				:element="element"
				:allow-class-assignment="allowClassAssignment"
				:assigned-classes="computedClasses"
				:assigned-static-classes="computedStaticClasses"
				:active-style-element-id="activeStyleElementId"
				@add-class="onAddClass"
				@remove-class="onRemoveClass"
				@remove-static-class="removeStaticClass"
				@paste-styles="onPasteStyles"
				@add-static-class="onAddStaticClass"
			/>

			<PseudoSelectors v-model="computedStyles" />
		</div>

		<div v-if="computedClasses.length || computedStaticClasses.length" class="znpb-element-styles__mediaActiveClasses">
			<span
				v-for="cssClass in computedAssignedClasses"
				:key="cssClass.selector"
				class="znpb-element-styles__mediaActiveClass"
				:class="{
					'znpb-element-styles__mediaActiveClass--active': cssClass.uid === computedActiveGlobalClass,
					'znpb-element-styles__mediaActiveClass--static': cssClass.type === 'static_class',
				}"
				@click.prevent="cssClass.type !== 'static_class' && toggleClass(cssClass.uid)"
			>
				.{{ cssClass.selector }}

				<Icon
					v-znpb-tooltip="i18n.__('Remove class', 'zionbuilder')"
					class="znpb-element-styles__mediaActiveClassRemove"
					icon="close"
					@click.stop.prevent="cssClass.onRemove(cssClass.uid)"
				/>
			</span>
		</div>
	</div>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { computed } from 'vue';
import { merge } from 'lodash-es';
import PseudoSelectors from './PseudoSelectors.vue';
import ClassSelectorDropdown from './ClassSelectorDropdown.vue';
import { useCSSClassesStore } from '/@/editor/store';

const cssClasses = useCSSClassesStore();

const props = defineProps<{
	element: ZionElement;
	activeStyleElementId: string;
	activeGlobalClass: string | null;
}>();

const emit = defineEmits(['update:active-global-class']);

const computedActiveGlobalClass = computed({
	get() {
		return props.activeGlobalClass;
	},
	set(newValue) {
		emit('update:active-global-class', newValue);
	},
});

const computedClasses = computed({
	get() {
		return props.element.getOptionValue(`_styles.${props.activeStyleElementId}.classes`, []) as string[];
	},
	set(newValue: string[]) {
		props.element.updateOptionValue(`_styles.${props.activeStyleElementId}.classes`, newValue);
	},
});

const computedAssignedClasses = computed(() => {
	const assignedClasses: {
		type: string;
		selector: string;
		uid: string;
		onRemove?: (cssClass: string) => void;
	}[] = [];

	computedClasses.value.forEach(cssClass => {
		const classConfig = cssClasses.getClassConfig(cssClass);
		if (classConfig) {
			assignedClasses.push({
				type: 'class',
				selector: classConfig.id,
				uid: classConfig.uid,
				onRemove: () => {
					onRemoveClass(classConfig.uid);
				},
			});
		}
	});

	computedStaticClasses.value.forEach(cssClass => {
		assignedClasses.push({
			type: 'static_class',
			selector: cssClass,
			uid: cssClass,
			onRemove: () => {
				removeStaticClass(cssClass);
			},
		});
	});

	return assignedClasses;
});

const computedStaticClasses = computed({
	get() {
		return props.element.getOptionValue(`_styles.${props.activeStyleElementId}.static_classes`, []) as string[];
	},
	set(newValue: string[]) {
		props.element.updateOptionValue(`_styles.${props.activeStyleElementId}.static_classes`, newValue);
	},
});

function toggleClass(cssClass: string) {
	if (computedActiveGlobalClass.value === cssClass) {
		computedActiveGlobalClass.value = null;
	} else {
		computedActiveGlobalClass.value = cssClass;
	}
}

function onRemoveClass(cssClass: string) {
	const existingClasses = [...computedClasses.value];
	let classIndex = existingClasses.indexOf(cssClass);

	if (classIndex === -1) {
		// Check if the class is assigned using the old "selector" id instead of the uid
		const classConfig = cssClasses.getClassConfig(cssClass);
		if (classConfig) {
			classIndex = existingClasses.indexOf(classConfig.id);
		}
	}

	if (classIndex === -1) {
		return;
	}

	existingClasses.splice(classIndex, 1);
	// Check if the active class was the one deleted
	if (computedActiveGlobalClass.value === cssClass) {
		computedActiveGlobalClass.value = null;
	}

	computedClasses.value = existingClasses;
}

/**
 * Removes a static class from the element
 *
 * @param cssClass string
 */
function removeStaticClass(cssClass: string) {
	const existingClasses = [...computedStaticClasses.value];
	const classIndex = existingClasses.indexOf(cssClass);

	if (classIndex === -1) {
		return;
	}

	existingClasses.splice(classIndex, 1);

	computedStaticClasses.value = existingClasses;
}

const allowClassAssignment = computed(() => {
	return props.element.elementDefinition.style_elements[props.activeStyleElementId].allow_class_assignment;
});

// Computed
const computedStyles = computed({
	get() {
		if (computedActiveGlobalClass.value) {
			const activeClassConfig = cssClasses.getClassConfig(computedActiveGlobalClass.value);
			if (activeClassConfig) {
				return activeClassConfig.styles;
			}

			// eslint-disable-next-line
			console.warn(`Class with id ${computedActiveGlobalClass.value} not found`)
			return {};
		} else {
			return props.element.getOptionValue(`_styles.${props.activeStyleElementId}.styles`, {});
		}
	},
	set(newValue) {
		if (computedActiveGlobalClass.value) {
			const activeClassConfig = cssClasses.getClassConfig(computedActiveGlobalClass.value);
			if (activeClassConfig) {
				activeClassConfig.styles = newValue;
			}
		} else {
			props.element.updateOptionValue(`_styles.${props.activeStyleElementId}.styles`, newValue);
		}
	},
});

const computedTitle = computed(() => {
	if (computedActiveGlobalClass.value) {
		const activeClassConfig = cssClasses.getClassConfig(computedActiveGlobalClass.value);

		if (activeClassConfig) {
			return activeClassConfig.name;
		}

		// eslint-disable-next-line
		console.warn(`Class with id ${computedActiveGlobalClass.value} not found`)
		return '';
	} else {
		return props.element.elementDefinition.style_elements[props.activeStyleElementId].title;
	}
});

function onAddClass(cssClass: string) {
	// Check to see if the class already exists
	if (computedClasses.value.includes(cssClass)) {
		computedActiveGlobalClass.value = cssClass;
		return;
	} else {
		const existingClasses = [...computedClasses.value];
		existingClasses.push(cssClass);
		computedClasses.value = existingClasses;

		computedActiveGlobalClass.value = cssClass;
	}
}

function onAddStaticClass(cssClass: string) {
	// Check to see if the class already exists
	if (computedStaticClasses.value.includes(cssClass)) {
		return;
	} else {
		const existingClasses = [...computedStaticClasses.value];
		existingClasses.push(cssClass);
		computedStaticClasses.value = existingClasses;
	}
}

function onPasteStyles() {
	const copiedStyles = cssClasses.copiedStyles;

	if (copiedStyles) {
		computedStyles.value = merge(computedStyles.value || {}, copiedStyles);
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
	width: 100%;
}

.znpb-element-styles__mediaActiveClasses {
	margin-top: 10px;
	width: 100%;
}

.znpb-element-styles__mediaActiveClass {
	position: relative;
	background: var(--zb-surface-color);
	padding: 3px 5px;
	font-size: 10px;
	border-radius: 2px;
	color: var(--zb-surface-text-color);
	display: inline-flex;
	align-items: center;
	cursor: pointer;
	margin-right: 5px;
	user-select: none;
	margin-bottom: 5px;
}

.znpb-element-styles__mediaActiveClass--active {
	background: var(--zb-secondary-color);
	color: #fff;
}

.znpb-element-styles__mediaActiveClassRemove {
	position: absolute;
	top: -5px;
	right: -5px;
	padding: 3px;
	cursor: pointer;
	font-size: 6px;
	background: #909090;
	border-radius: 50%;
	opacity: 0;
	visibility: hidden;
	transition: all 0.2s;
	color: #fff;

	&:hover {
		background: #ff5757;
	}
}

.znpb-element-styles__mediaActiveClass:hover .znpb-element-styles__mediaActiveClassRemove {
	opacity: 1;
	visibility: visible;
}

.znpb-element-styles__mediaActiveClass--static {
	cursor: default;
	background: #141414;
	color: #fff;
}
</style>
