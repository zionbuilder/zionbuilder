<template>
	<div :id="panelId" ref="root" :class="getCssClass" :style="panelStyles" class="znpb-editor-panel">
		<slot name="before-header"></slot>

		<!-- start panel header -->
		<div v-if="showHeader" ref="panelHeader" class="znpb-panel__header" @mousedown="onMouseDown">
			<h4 v-if="!hasHeaderSlot" class="znpb-panel__header-name">
				{{ panelName }}
			</h4>

			<slot name="header" />

			<Icon v-if="showClose" icon="close" :size="14" class="znpb-panel__header-icon-close" @click.stop="closePanel" />

			<slot name="header-suffix"></slot>
		</div>
		<!-- end panel header -->

		<slot name="after-header" />

		<!-- content -->
		<div class="znpb-panel__content_wrapper">
			<slot />
		</div>
		<!-- resize divs -->
		<div
			v-if="!UIStore.isAnyPanelDragging && allowHorizontalResize"
			class="znpb-editor-panel__resize znpb-editor-panel__resize--horizontal"
			@mousedown="activateHorizontalResize"
		/>
		<div
			v-if="panel.isDetached && !UIStore.isAnyPanelDragging && allowVerticalResize"
			class="znpb-editor-panel__resize znpb-editor-panel__resize--vertical"
			@mousedown="activateVerticalResize"
		/>
	</div>
</template>
<script lang="ts" setup>
import { computed, ref, onBeforeUnmount, onMounted, nextTick, useSlots } from 'vue';
import rafSchd from 'raf-schd';
import { useWindows } from '../composables';
import { useUIStore } from '../store';

const props = withDefaults(
	defineProps<{
		cssClass?: string | Record<string, string>;
		panelName?: string;
		panelId: string;
		expanded?: boolean;
		showHeader?: boolean;
		showClose?: boolean;
		allowHorizontalResize?: boolean;
		allowVerticalResize?: boolean;
		closeOnEscape?: boolean;
		panel: ZionPanel;
	}>(),
	{
		cssClass: '',
		panelName: '',
		expanded: false,
		showHeader: true,
		showClose: true,
		allowHorizontalResize: true,
		allowVerticalResize: true,
		closeOnEscape: true,
	},
);

const slots = useSlots();
const emit = defineEmits(['close-panel']);

const UIStore = useUIStore();
const { addEventListener, removeEventListener } = useWindows();

// Normal data
let boundingClientRect = null;

// REFS
const root = ref(null);
const panelOffset = ref(null);
const initialPosition = ref({
	posY: 0,
	posX: 0,
});
const dragginMoved = ref({
	x: null,
	y: null,
});

// COMPUTED
const getCssClass = computed(() => {
	let classes = '';
	classes += props.panel.isDetached ? ' znpb-editor-panel--detached' : ' znpb-editor-panel--attached';
	classes += props.cssClass ? props.cssClass : '';
	classes += props.panel.isDragging ? ' znpb-editor-panel--dragging' : '';

	if (!props.panel.isDetached) {
		classes +=
			UIStore.getPanelPlacement(props.panel.id) === 'right' ? ' znpb-editor-panel--right' : ' znpb-editor-panel--left';
	}
	return classes;
});

const panelStyles = computed(() => {
	const cssStyles = {
		width: props.panel.width + 'px',
		height: props.panel.isDetached ? (props.panel.height ? props.panel.height + 'px' : '90%') : '100%',
		order: UIStore.getPanelOrder(props.panel.id),

		// Positions for detached
		top:
			!props.panel.isDragging && props.panel.isDetached && props.panel.offsets.posY !== 0
				? props.panel.offsets.posY + 'px'
				: null,
		left: !props.panel.isDragging && props.panel.isDetached ? props.panel.offsets.posX + 'px' : null,
		position: props.panel.isDetached ? 'fixed' : 'relative',

		// Dragging transform
		transform: props.panel.isDragging ? `translate3d(${dragginMoved.value.x}px, ${dragginMoved.value.y}px, 0)` : null,
		zIndex: props.panel.isDragging ? 99 : null,
	};
	return cssStyles;
});

const hasHeaderSlot = computed(() => {
	return !!slots.header;
});

// METHODS
let initialMovePosition = {
	posX: null,
	posY: null,
};

let availableStickElements = [];
let oldIndex = null;
let newIndex = null;
function onMouseDown(event) {
	UIStore.setIframePointerEvents(true);
	document.body.style.userSelect = 'none';

	const { clientX, clientY } = event;

	// Get initial position
	oldIndex = UIStore.getPanelIndex(props.panel.id);

	boundingClientRect = root.value.getBoundingClientRect();
	const parentClientRect = root.value.parentNode.getBoundingClientRect();
	initialMovePosition = {
		posX: clientX,
		posY: clientY,
		startPanelRect: boundingClientRect,
		parentClientRect: parentClientRect,
		oldTop: boundingClientRect.top,
		oldLeft: boundingClientRect.left,
	};

	dragginMoved.value = {
		x: boundingClientRect.left - parentClientRect.left,
		y: boundingClientRect.top - parentClientRect.top,
	};

	window.addEventListener('mousemove', rafMovePanel);
	window.addEventListener('mouseup', onMouseUp);
}

function movePanel(event) {
	const { posX, posY, oldTop, oldLeft, parentClientRect } = initialMovePosition;
	const { height: parentHeight, width: parentWidth } = parentClientRect;
	const { clientY, clientX } = event;

	if (!props.panel.isDragging) {
		const xMoved = Math.abs(posX - clientX);
		const yMoved = Math.abs(posY - clientY);
		const dragThreshold = 5;

		// Check if we should detach the panel
		if (xMoved > dragThreshold || yMoved > dragThreshold) {
			UIStore.updatePanel(props.panel.id, 'isDetached', true);
			UIStore.updatePanel(props.panel.id, 'isDragging', true);

			nextTick(() => {
				// Recalculate the height of the panel
				boundingClientRect = root.value.getBoundingClientRect();

				// Set available stick elements
				UIStore.openPanels.forEach(panel => {
					// We don't care about detached panels
					if (panel.isDetached || panel.id === props.panel.id) {
						return;
					}

					const boundingClient = document.getElementById(panel.id).getBoundingClientRect();

					availableStickElements.push({
						panel,
						boundingClient,
					});
				});
			});
		}
	} else {
		if (!boundingClientRect) {
			return;
		}

		const maxBottom = parentHeight - boundingClientRect.height;
		const newPositionY = oldTop + clientY - posY - parentClientRect.top;
		const newTop = newPositionY < 0 ? 0 : newPositionY;
		const MinMaxTop = newTop > maxBottom ? maxBottom : newTop;

		const movedAmmount = oldLeft + clientX - posX;
		const MinMaxLeft = movedAmmount - parentClientRect.left;

		dragginMoved.value = {
			x: MinMaxLeft,
			y: MinMaxTop,
		};

		// Check left or right
		availableStickElements.forEach(availableStickLocation => {
			const { boundingClient, panel: possibleHoverPanel } = availableStickLocation;
			const realLeft = boundingClient.left;
			const realRight = boundingClient.left + boundingClient.width;

			// Check to see if we are hovering the panel
			if (MinMaxLeft >= boundingClient.left && MinMaxLeft <= boundingClient.left + boundingClient.width) {
				if (MinMaxLeft < realLeft + 50) {
					UIStore.setPanelPlaceholder({
						visibility: true,
						left: realLeft,
						placeBefore: true,
						panel: possibleHoverPanel,
					});

					newIndex = UIStore.getPanelIndex(possibleHoverPanel.id);
				} else if (movedAmmount + boundingClientRect.width > boundingClient.left + boundingClient.width - 50) {
					const left =
						boundingClient.left + boundingClient.width >= window.innerWidth
							? boundingClient.left + boundingClient.width - 5
							: realRight;

					UIStore.setPanelPlaceholder({
						visibility: true,
						left: left,
						placeBefore: false,
						panel: possibleHoverPanel,
					});

					newIndex = UIStore.getPanelIndex(possibleHoverPanel.id) + 1;
				} else {
					UIStore.setPanelPlaceholder({
						visibility: false,
						left: null,
						placeBefore: null,
						panel: null,
					});

					newIndex = null;
				}
			}
		});
	}
}

const rafMovePanel = rafSchd(movePanel);

function updatePosition(oldIndex, newIndex) {
	const list = [...UIStore.panelsOrder];
	if (oldIndex >= newIndex) {
		list.splice(newIndex, 0, list.splice(oldIndex, 1)[0]);
	} else {
		list.splice(newIndex - 1, 0, list.splice(oldIndex, 1)[0]);
	}

	UIStore.panelsOrder = list;
}

function onMouseUp() {
	// Cleanup
	document.body.style.userSelect = null;
	UIStore.setIframePointerEvents(false);
	availableStickElements = [];

	// Cancel the animation frame
	rafMovePanel.cancel();
	props.panel.isDragging = false;

	dragginMoved.value = {
		x: null,
		y: null,
	};

	window.removeEventListener('mousemove', rafMovePanel);
	window.removeEventListener('mouseup', onMouseUp);

	// Save the panel position
	panelOffset.value = root.value.getBoundingClientRect();

	props.panel.offsets = {
		posX: panelOffset.value.left,
		posY: panelOffset.value.top,
	};

	initialPosition.value = {
		posX: panelOffset.value.left,
		posY: panelOffset.value.top,
	};

	if (null !== oldIndex && null !== newIndex) {
		UIStore.updatePanel(props.panel.id, 'isDetached', false);
		updatePosition(oldIndex, newIndex);
	}

	UIStore.saveUI();

	UIStore.setPanelPlaceholder({
		visibility: false,
		panel: null,
	});
}

const rafResizeHorizontal = rafSchd(resizeHorizontal);
let initialHMouseX = null;
let initialWidth = null;
function activateHorizontalResize(event) {
	UIStore.setIframePointerEvents(true);

	document.body.style.userSelect = 'none';
	document.body.style.cursor = 'w-resize';

	initialHMouseX = event.clientX;
	initialWidth = props.panel.width;

	// Attach events
	window.addEventListener('mousemove', rafResizeHorizontal);
	window.addEventListener('mouseup', deactivateHorizontal);
}

function resizeHorizontal(event) {
	const draggedHorizontal = event.clientX - initialHMouseX;
	const width =
		UIStore.getPanelPlacement(props.panel.id) === 'left' || props.panel.isDetached
			? draggedHorizontal + initialWidth
			: -draggedHorizontal + initialWidth;

	UIStore.updatePanel(props.panel.id, 'width', width < 360 ? 360 : width);
}

function deactivateHorizontal() {
	UIStore.setIframePointerEvents(false);

	document.body.style.userSelect = null;
	document.body.style.cursor = null;

	window.removeEventListener('mousemove', rafResizeHorizontal);
	window.removeEventListener('mouseup', deactivateHorizontal);
	UIStore.saveUI();
}

const rafResizeVertical = rafSchd(resizeVertical);
let initialVMouseY = null;
let initialHeight = null;
function activateVerticalResize(event) {
	UIStore.setIframePointerEvents(true);
	document.body.style.userSelect = 'none';
	document.body.style.cursor = 'n-resize';

	initialHeight = root.value.clientHeight;
	initialVMouseY = event.clientY;

	window.addEventListener('mousemove', rafResizeVertical);
	window.addEventListener('mouseup', deactivateVertical);
}

function resizeVertical(event) {
	UIStore.updatePanel(props.panel.id, 'isDetached', true);

	const draggedVertical = event.clientY - initialVMouseY;
	const newHeightValue = initialHeight + draggedVertical;

	if (event.clientY < window.innerHeight) {
		UIStore.updatePanel(
			props.panel.id,
			'height',
			newHeightValue > root.value.parentNode.clientHeight ? root.value.parentNode.clientHeight : newHeightValue,
		);
	}
}

function deactivateVertical() {
	UIStore.setIframePointerEvents(false);

	document.body.style.userSelect = null;
	document.body.style.cursor = null;

	window.removeEventListener('mousemove', rafResizeVertical);
	window.removeEventListener('mouseup', deactivateVertical);
	UIStore.saveUI();
}

/**
 * Close panel on escape key
 */
function onKeyDown(event) {
	if (event.which === 27) {
		closePanel();
		event.stopImmediatePropagation();
	}
}

// LIFECYCLE
onMounted(() => {
	if (props.closeOnEscape) {
		addEventListener('keydown', onKeyDown);
	}

	panelOffset.value = root.value.getBoundingClientRect();
	props.panel.offsets = {
		posX: panelOffset.value.left,
		posY: panelOffset.value.top,
	};

	initialPosition.value = {
		posX: panelOffset.value.left,
		posY: panelOffset.value.top,
	};
});

function closePanel() {
	UIStore.closePanel(props.panel.id);
	emit('close-panel');
}

onBeforeUnmount(() => {
	window.removeEventListener('mousemove', rafResizeHorizontal);
	window.removeEventListener('mouseup', deactivateHorizontal);
	window.removeEventListener('mousemove', rafResizeVertical);
	window.removeEventListener('mouseup', deactivateVertical);
	removeEventListener('keydown', onKeyDown);

	// Reset document styles
	document.body.style.cursor = null;
	document.body.style.userSelect = null;
});
</script>
<style lang="scss">
/* style panel */
.znpb-editor-panel {
	position: relative;
	z-index: 1;
	display: flex;
	flex-direction: column;
	min-height: 320px;
	background: var(--zb-surface-color);

	&--attached {
		height: 100%;

		& .znpb-editor-panel__resize--vertical {
			bottom: 0;
		}
	}
	&:after {
		clear: both;
	}
	&--top {
		height: 100%;
	}
	&--left {
		// box-shadow: 2px 0 0 0 var(--zb-surface-border-color);
		border-right: var(--zb-panel-sideborder) solid var(--zb-surface-border-color);
		.znpb-editor-panel__resize--horizontal {
			right: -6px;
		}
	}

	&--right {
		// box-shadow: -2px 0 0 0 var(--zb-surface-border-color);
		border-left: var(--zb-panel-sideborder) solid var(--zb-surface-border-color);
		.znpb-editor-panel__resize--horizontal {
			left: -6px;
			&::before {
				right: 0;
				left: auto;
			}
		}
	}
	&--right + &--right {
		box-shadow: none;
	}
	&--detached {
		box-shadow: 0 0 0 var(--zb-panel-sideborder) var(--zb-surface-border-color);
		border: none;
		.znpb-editor-panel__resize--horizontal {
			right: -6px;
		}
	}
}

.znpb-panel__header {
	position: relative;
	display: flex;
	justify-content: space-between;
	align-items: center;
	flex-shrink: 0;
	background-color: var(--zb-surface-color);
	border-bottom: 1px solid var(--zb-panel-header-border);
	cursor: move;

	& > .znpb-editor-icon-wrapper {
		margin-right: 15px;
		color: var(--zb-surface-icon-color);
		transition: color 0.15s ease-out;
		cursor: pointer;

		.zion-icon.zion-svg-inline {
			margin: 0 auto;
		}
		&:last-child {
			margin-right: 0;
		}
		&:hover {
			color: var(--zb-surface-icon-active-color);
		}
	}

	&-icon-close {
		padding: 21.5px 20px 21.5px 15px;
	}
}
h4.znpb-panel__header-name {
	padding: 19px 0 19px 20px;
	color: var(--zb-surface-text-active-color);
	font-size: 14px;
	font-weight: 500;
	line-height: 1.4;
	cursor: pointer;

	& > .znpb-editor-icon-wrapper {
		margin-left: 5px;
		color: var(--zb-surface-icon-color);

		&:hover {
			color: var(--zb-surface-icon-active-color);
			background: none;
		}
	}
}
.znpb-editor-panel__resize {
	position: absolute;
	z-index: 5;

	&--horizontal {
		top: 0;
		width: 6px;
		height: 100%;
		&::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			width: 0;
			height: 100%;
			background-color: var(--zb-secondary-color);
			transition: width 0.1s;
			opacity: 0.6;
		}
		&:hover {
			cursor: ew-resize;
		}
		&:hover::before {
			width: 100%;
		}
	}

	&--vertical {
		right: 0;
		bottom: -5px;
		width: 100%;
		height: 5px;
		&:hover {
			background-color: var(--zb-secondary-color);
			cursor: n-resize;
			opacity: 0.6;
		}
	}
}

.znpb-panel__content_wrapper {
	display: flex;
	flex-direction: column;
	overflow: hidden;
	height: 100%;
}

.znpb-color-picker--backdrop .znpb-editor-panel__resize {
	display: none;
}

.znpb-editor-panel--dragging {
	pointer-events: none;
}
</style>
