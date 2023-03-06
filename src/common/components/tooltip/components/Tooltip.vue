<template>
	<component :is="tag" ref="root" @click="onClick" @mouseenter="onMouseEnter" @mouseleave="onMouseLeave">
		<Teleport v-if="getAppendToElement" :disabled="appendTo === 'element'" to="body">
			<div v-if="isVisible" ref="popperContentRef" class="hg-popper" :class="tooltipClass" :style="getStyle">
				{{ content }}

				<slot name="content" />

				<span v-if="showArrows" data-popper-arrow="true" class="hg-popper--with-arrows" />
			</div>
		</Teleport>

		<slot />
	</component>
</template>

<script lang="ts">
let preventOutsideClickPropagation = false;
</script>

<script lang="ts" setup>
import { ref, Ref, watch, computed, onBeforeUnmount, onMounted, nextTick } from 'vue';
import { merge } from 'lodash-es';
import { createPopper } from '@popperjs/core';
import { getZIndex, removeZIndex } from '../../../modules/z-index-manager';

const props = withDefaults(
	defineProps<{
		modifiers?: Record<string, unknown>[];
		tag?: string;
		content?: string | null;
		show?: boolean;
		openDelay?: number;
		closeDelay?: number;
		enterable?: boolean;
		hideAfter?: number | null;
		showArrows?: boolean;
		appendTo?: string;
		trigger?: 'click' | 'hover' | null;
		closeOnOutsideClick?: boolean;
		closeOnEscape?: boolean;
		popperRef?: Record<string, unknown> | null;
		tooltipClass?: string;
		tooltipStyle?: Record<string, unknown>;
		placement?: string;

		// Popper JS props
		strategy?: string;
	}>(),
	{
		modifiers: () => [],
		tag: 'div',
		content: null,
		show: false,
		openDelay: 10,
		closeDelay: 10,
		enterable: true,
		hideAfter: null,
		showArrows: true,
		appendTo: 'body',
		trigger: 'hover',
		closeOnOutsideClick: false,
		closeOnEscape: false,
		popperRef: null,
		tooltipClass: '',
		tooltipStyle: () => ({}),
		placement: 'top',
		strategy: 'absolute',
	},
);

const emit = defineEmits(['show', 'hide', 'update:show']);

const root = ref(null);
const popperContentRef: Ref<HTMLElement | null> = ref(null);
const popperSelector = ref(null);
const ownerDocument = ref(null);
const isVisible = ref(!!props.show);
const zIndex = ref(9);

// normal vars
let popperInstance = null;
let timeout: NodeJS.Timeout | null = null;

const getStyle = computed(() => {
	return {
		...props.tooltipStyle,
		'z-index': zIndex.value,
	};
});

const popperOptions = computed(() => {
	const options = {};
	const instanceOptions = JSON.parse(JSON.stringify(props));
	instanceOptions.modifiers = props.modifiers || [];

	// Apply offset for arrow
	if (props.showArrows) {
		const hasOffsetModifier = instanceOptions.modifiers.find(modifier => modifier.name === 'offset');

		if (!hasOffsetModifier) {
			instanceOptions.modifiers.push({
				name: 'offset',
				options: {
					offset: [0, 10],
				},
			});
		}
	}

	return merge(options, instanceOptions);
});

watch(
	() => props.closeOnOutsideClick,
	newValue => {
		if (newValue) {
			ownerDocument.value.addEventListener('click', onOutsideClick, true);
		} else {
			ownerDocument.value.removeEventListener('click', onOutsideClick, true);
		}
	},
);

watch(
	() => props.hideAfter,
	newValue => {
		if (newValue) {
			onHideAfter();
		}
	},
);

watch(
	() => props.show,
	newValue => {
		isVisible.value = !!newValue;
	},
);

watch(isVisible, (newValue, oldValue) => {
	if (newValue) {
		zIndex.value = getZIndex();
		nextTick(() => {
			onTransitionEnter();
		});
	} else {
		onTransitionLeave();
	}

	if (!!newValue !== !!oldValue) {
		if (newValue) {
			zIndex.value = getZIndex();
		} else if (zIndex.value) {
			removeZIndex();

			zIndex.value = null;
		}
	}
});

onBeforeUnmount(() => {
	if (ownerDocument.value) {
		ownerDocument.value.removeEventListener('click', onOutsideClick, true);
		ownerDocument.value.removeEventListener('keyup', onKeyUp);
	}

	// Destroy popper instance
	destroyPopper();

	// Send the close emit if the tooltip is open
	if (isVisible.value) {
		emit('hide');
	}

	if (zIndex.value) {
		removeZIndex();
		zIndex.value = null;
	}
});

onMounted(() => {
	if (props.show) {
		zIndex.value = getZIndex();
		onTransitionEnter();
	}
});

function onTransitionEnter() {
	instantiatePopper();
	emit('show');
	emit('update:show', true);
}

function onTransitionLeave() {
	destroyPopper();
	emit('hide');
	emit('update:show', false);
}

function getAppendToElement() {
	// Get content document
	if (props.appendTo !== 'element') {
		return ownerDocument.value.querySelector(props.appendTo);
	}

	return root.value;
}

function showPopper() {
	isVisible.value = true;
}

function hidePopper() {
	isVisible.value = false;
}

function destroyPopper() {
	if (popperInstance) {
		popperInstance.destroy();
		popperInstance = null;
	}

	removePopperEvents();

	preventOutsideClickPropagation = false;
}

function instantiatePopper() {
	popperSelector.value = props.popperRef || root.value;
	ownerDocument.value = popperSelector.value.ownerDocument || root.value.ownerDocument;

	if (popperInstance && popperInstance.destroy) {
		popperInstance.destroy();
		popperInstance = null;
	}

	if (popperSelector.value) {
		nextTick(() => {
			popperInstance = createPopper(popperSelector.value, popperContentRef.value, popperOptions.value);
		});
	}

	onHideAfter();
	addPopperEvents();
}

function onMouseEnter() {
	if (props.trigger !== 'hover') {
		return;
	}

	if (timeout) {
		clearTimeout(timeout);
	}

	timeout = setTimeout(() => {
		showPopper();
	}, props.openDelay);
}

function onMouseLeave() {
	if (props.trigger !== 'hover') {
		return;
	}

	if (timeout) {
		clearTimeout(timeout);
	}

	timeout = setTimeout(() => {
		hidePopper();
	}, props.closeDelay);
}

function onHideAfter() {
	if (props.hideAfter) {
		if (timeout) {
			clearTimeout(timeout);
		}

		timeout = setTimeout(() => {
			hidePopper();
		}, props.hideAfter);
	}
}

function onClick(event) {
	if (props.trigger !== 'click') {
		return;
	}

	if (popperContentRef.value && popperContentRef.value.contains(event.target)) {
		return;
	}

	isVisible.value = !isVisible.value;
}

function onOutsideClick(event) {
	// Only hide if visible
	if (!isVisible.value || preventOutsideClickPropagation) {
		return;
	}

	preventOutsideClickPropagation = true;

	// Prevent clicks on popper selector
	if (
		popperSelector.value &&
		typeof popperSelector.value.contains === 'function' &&
		popperSelector.value.contains(event.target)
	) {
		preventOutsideClickPropagation = false;
		return;
	}

	if (popperContentRef.value && popperContentRef.value.contains(event.target)) {
		preventOutsideClickPropagation = false;
		return;
	}

	// Hide popper if clicked outside
	hidePopper();
	emit('update:show', false);

	preventOutsideClickPropagation = false;
}

function onKeyUp(event) {
	if (event.which === 27) {
		hidePopper();
		event.stopPropagation();
		event.stopImmediatePropagation();
	}
}

function scheduleUpdate() {
	if (popperInstance) {
		popperInstance.update();
	}
}

function addPopperEvents() {
	if (props.closeOnOutsideClick) {
		ownerDocument.value.addEventListener('click', onOutsideClick, true);
	}

	// Attache close on escape
	if (props.closeOnEscape) {
		ownerDocument.value.addEventListener('keyup', onKeyUp);
	}
}

function removePopperEvents() {
	if (ownerDocument.value) {
		ownerDocument.value.removeEventListener('click', onOutsideClick, true);
	}

	// Attache close on escape
	if (props.closeOnEscape && ownerDocument.value) {
		ownerDocument.value.removeEventListener('keyup', onKeyUp);
	}
}

const api = {
	showPopper,
	hidePopper,
	destroyPopper,
	scheduleUpdate,
};

defineExpose(api);
</script>

<style lang="scss">
.hg-popper {
	z-index: 9999;
	box-sizing: border-box;
	padding: 10px;
	color: var(--zb-dropdown-text-color);
	font-family: var(--zb-font-stack);
	font-size: 13px;
	font-weight: normal;
	background-color: var(--zb-dropdown-bg-color);
	box-shadow: var(--zb-dropdown-shadow);
	border: 1px solid var(--zb-dropdown-border-color);
	border-radius: 4px;

	& * {
		box-sizing: border-box;
	}

	&-list {
		padding: 6px 0;
		margin: 0;
		list-style-type: none;
		background: var(--zb-dropdown-bg-color);

		&__item {
			padding: 8px 16px;
			color: var(--zb-dropdown-text-color);
			font-weight: 500;
			text-align: left;
			cursor: pointer;

			&:hover {
				color: var(--zb-dropdown-text-active-color);
				background-color: var(--zb-dropdown-bg-active-color);
			}
		}
	}

	&--with-arrows {
		z-index: -1;

		&:before {
			content: '';
			top: 0;
			left: 0;
			z-index: -1;
			display: block;
			width: 8px;
			height: 8px;
			background: var(--zb-dropdown-bg-color);
			transform: rotate(45deg);
		}
	}

	&[data-popper-placement^='top'] {
		margin-bottom: 5px;
	}

	&[data-popper-placement^='top'] &--with-arrows {
		bottom: -4px;
	}

	&[data-popper-placement^='bottom'] {
		margin-top: 5px;
	}

	&[data-popper-placement^='bottom'] &--with-arrows {
		top: -4px;
		left: 50%;
	}

	&[data-popper-placement^='left'] {
		margin-right: 5px;
	}

	&[data-popper-placement^='left'] &--with-arrows {
		top: 50%;
		right: -4px;
		margin-top: -5px;
	}

	&[data-popper-placement^='right'] {
		margin-left: 5px;
	}
	&[data-popper-placement^='right'] &--with-arrows {
		top: 50%;
		left: -4px;
		margin-top: -5px;
	}

	&.hg-popper--big-arrows .hg-popper--with-arrows {
		border-width: 12px;
	}

	&.hg-popper--no-padding {
		padding: 0;
		// border: 0;
	}
	&.hg-popper--no-bg {
		background: transparent;
		box-shadow: none;
	}
	&.znpb-class-selector__popper {
		padding: 15px;
	}

	ul {
		list-style-type: none;
	}
}

body .hg-popper-tooltip {
	z-index: 99999;
	max-width: 200px;
	padding: 5px 10px;
	font-size: 12px;
	font-weight: normal;
	line-height: 1.6;
	text-align: center;
}
</style>
