<template>
	<div
		ref="root"
		class="znpb-element-options__media-class-pseudo-holder"
		@click="(selectorIsOpen = !selectorIsOpen), (contentOpen = false), (newPseudoName = false)"
	>
		<span class="znpb-element-options__media-class-pseudo-name">
			{{ activePseudoSelector.name }}
		</span>

		<Icon
			v-if="hasContent"
			v-znpb-tooltip="__('Click to add content for pseudo selector.', 'zionbuilder')"
			icon="edit"
			:size="12"
			class="znpb-pseudo-selector__edit"
			@click.stop="(contentOpen = !contentOpen), (selectorIsOpen = false)"
		/>

		<Tooltip
			:show-arrows="false"
			:show="selectorIsOpen"
			:trigger="null"
			append-to="element"
			placement="bottom-end"
			tooltip-class="hg-popper--no-padding znpb-element-options__media-class-pseudo-selector-dropdown"
		>
			<template #content>
				<div class="znpb-element-options__media-class-pseudo-selector-list hg-popper-list">
					<PseudoDropdownItem
						v-for="(selectorConfig, index) in computedPseudoSelectors"
						:key="index"
						:selector="selectorConfig"
						:selectors-model="activePseudoSelectors"
						:clearable="selectorConfig.canBeDeleted"
						class="hg-popper-list__item"
						@remove-styles="deleteConfigForPseudoSelector"
						@selector-selected="onPseudoSelectorSelected"
						@delete-selector="deletePseudoSelectorAndStyles"
					/>
				</div>
			</template>

			<div
				class="znpb-element-options__media-class-pseudo-title"
				:class="{
					'znpb-element-options__media-class-pseudo-title--has-edit':
						activePseudoSelector.id === ':before' || activePseudoSelector.id === ':after',
				}"
			>
				<Icon icon="select" :rotate="selectorIsOpen ? 180 : null" />
			</div>
		</Tooltip>

		<div v-if="contentOpen" class="znpb-element-options__media-class-pseudo-title__before-after-content" @click.stop="">
			<BaseInput
				ref="pseudoContentInput"
				v-model="pseudoContentModel"
				:clearable="true"
				:placeholder="`Insert text ${activePseudoSelector.id} content`"
				@keypress.enter="contentOpen = false"
			/>
		</div>
		<div
			v-if="newPseudoName"
			class="znpb-element-options__media-class-pseudo-title__before-after-content"
			@click.stop=""
		>
			<BaseInput
				ref="pseudoNameInputRef"
				v-model="newPseudoModel"
				:clearable="true"
				:placeholder="__('Add new pseudo-selector ex: :hover::before ', 'zionbuilder')"
				@keypress.enter="createNewPseudoSelector"
			/>
		</div>
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { computed, ref, onBeforeUnmount, watch, nextTick } from 'vue';
import { cloneDeep, set, find } from 'lodash-es';

// Components
import PseudoDropdownItem from './PseudoDropdownItem.vue';

const { useResponsiveDevices, usePseudoSelectors } = window.zb.composables;

const props = defineProps({
	modelValue: {
		type: [Object, Array],
		required: false,
		default: {},
	},
});

const emit = defineEmits(['update:modelValue']);

const { activeResponsiveDeviceInfo } = useResponsiveDevices();
const { pseudoSelectors, activePseudoSelector, setActivePseudoSelector, deleteCustomSelector, addCustomSelector } =
	usePseudoSelectors();

// Refs / Data
const root = ref(null);
const pseudoNameInputRef = ref(null);
const pseudoContentInput = ref(null);
const contentOpen = ref(false);
const selectorIsOpen = ref(false);
const showContentTooltip = ref(false);
const newPseudoName = ref(false);
const customPseudoName = ref('');

const hasContent = computed(
	() => activePseudoSelector.value.id === ':before' || activePseudoSelector.value.id === ':after',
);
const activePseudoSelectors = computed(() => (props.modelValue || {} || {})[activeResponsiveDeviceInfo.value.id] || {});
const pseudoStyles = computed(() => (activePseudoSelectors.value || {})[activePseudoSelector.value.id] || {});
const pseudoContentModel = computed({
	get() {
		return pseudoStyles.value.content || '';
	},
	set(newValue) {
		const cloneModelValue = cloneDeep(props.modelValue);
		const newValues = set(
			cloneModelValue,
			`${activeResponsiveDeviceInfo.value.id}.${activePseudoSelector.value.id}.content`,
			newValue,
		);

		emit('update:modelValue', newValues);
	},
});

const computedPseudoSelectors = computed(() => {
	const savedSelectors = Object.keys(activePseudoSelectors.value);
	const customSelectors = savedSelectors.filter(selector => {
		return !find(pseudoSelectors.value, ['id', selector]);
	});

	// Combine selectors with custom selectors
	return [
		...pseudoSelectors.value,
		...customSelectors.map(selector => {
			return {
				name: selector,
				id: selector,
				canBeDeleted: true,
			};
		}),
	];
});

/**
 * emit the change of the pseudo selector
 */
function onPseudoSelectorSelected(pseudoConfig) {
	selectorIsOpen.value = false;

	setActivePseudoSelector(pseudoConfig || pseudoSelectors.value[0]);
	if (activePseudoSelector.value.id === 'custom') {
		newPseudoName.value = true;
	}
	if (
		pseudoContentModel.value === '' &&
		(activePseudoSelector.value.id === 'before' || activePseudoSelector.value.id === 'after')
	) {
		showContentTooltip.value = false;
		contentOpen.value = true;
	}
}

function createNewPseudoSelector() {
	newPseudoName.value = false;

	const newSel = {
		id: customPseudoName.value,
		name: customPseudoName.value,
		canBeDeleted: true,
	};

	addCustomSelector(newSel);
	setActivePseudoSelector(newSel);
}

/**
 * Close input if clicked outside of selector
 */
function closePanel(event) {
	if (!root.value.contains(event.target)) {
		contentOpen.value = false;
		selectorIsOpen.value = false;
		newPseudoName.value = false;
	}
}

function deleteConfigForPseudoSelector(pseudoSelectorId) {
	const newValues = {
		...props.modelValue,
		[activeResponsiveDeviceInfo.value.id]: {
			...props.modelValue[activeResponsiveDeviceInfo.value.id],
		},
	};

	delete newValues[activeResponsiveDeviceInfo.value.id][pseudoSelectorId];

	// Check if there are any remaining styles for this responsive device
	if (Object.keys(newValues[activeResponsiveDeviceInfo.value.id] || {}).length === 0) {
		delete newValues[activeResponsiveDeviceInfo.value.id];
	}

	emit('update:modelValue', newValues);
}

function deletePseudoSelectorAndStyles(selector) {
	deleteConfigForPseudoSelector(selector.id);
	deleteCustomSelector(selector);
}

// Lifecycle
onBeforeUnmount(() => {
	// Clear active pseudo selector
	setActivePseudoSelector(null);
});

const newPseudoModel = computed({
	get() {
		return customPseudoName.value;
	},
	set(newVal) {
		customPseudoName.value = newVal.split(' ').join('').toLowerCase();
	},
});

// Watchers
watch(hasContent, newValue => {
	if (newValue) {
		showContentTooltip.value = true;

		setTimeout(() => {
			showContentTooltip.value = false;
		}, 2000);
	}
});

watch(selectorIsOpen, newValue => {
	if (newValue) {
		document.addEventListener('click', closePanel);
	} else {
		document.removeEventListener('click', closePanel);
	}
});

watch(contentOpen, newValue => {
	if (newValue) {
		nextTick(() => pseudoContentInput.value.focus());
		document.addEventListener('click', closePanel);
	} else {
		pseudoContentInput.value.blur();
		document.removeEventListener('click', closePanel);
	}
});
watch(pseudoNameInputRef, newValue => {
	if (newValue) {
		nextTick(() => pseudoNameInputRef.value.focus());
		document.addEventListener('click', closePanel);
	} else {
		pseudoNameInputRef.value.blur();
		document.removeEventListener('click', closePanel);
	}
});

onBeforeUnmount(() => {
	document.removeEventListener('click', closePanel);
});
</script>

<style lang="scss">
.znpb-pseudo-selector {
	&__edit {
		padding: 10px 8px;
	}
}
.znpb-element-options__media-class-pseudo-name {
	padding-left: 10px;
	font-size: 13px;
	font-weight: 500;
	flex-grow: 1;
}
.znpb-element-options__media-class-pseudo {
	&-holder {
		display: flex;
		justify-content: space-between;
		align-items: center;
		flex: 4;
		color: var(--zb-input-text-color);
		background: var(--zb-input-bg-color);
		border: 2px solid var(--zb-input-border-color);
		border-radius: 3px;
		cursor: pointer;
		position: relative;
	}

	&-title {
		display: flex;
		color: var(--zb-surface-text-color);
		border-left: var(--zb-input-separator-width) solid var(--zb-input-separator-color);
		cursor: pointer;

		.znpb-editor-icon-wrapper {
			padding: 9px;
		}

		// &--has-edit {
		// 	margin-right: 5px;
		// }

		&__before-after-content {
			position: absolute;
			top: calc(100% + 5px);
			left: -2px;
			z-index: 20000;
			width: calc(100% + 4px);
			padding: 15px;
			color: var(--zb-dropdown-text-color);
			background-color: var(--zb-dropdown-bg-color);
			box-shadow: 0 2px 15px 0 var(--zb-dropdown-shadow);
			border: 1px solid var(--zb-dropdown-border-color);
			border-radius: 4px;

			input::placeholder {
				color: var(--zb-input-placeholder-color);
			}
		}
	}

	&-selector-dropdown {
		min-width: 130px;
	}

	&-selector-list {
		display: flex;
		flex-direction: column;
	}
}
</style>
