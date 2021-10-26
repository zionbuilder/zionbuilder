<template>
	<div
		ref="root"
		:class="getCssClass"
		:style="panelStyles"
		:id='panelId'
		class="znpb-editor-panel"
	>
		<slot name="before-header"></slot>

		<!-- start panel header -->
		<div
			class="znpb-panel__header"
			v-if="showHeader"
			@mousedown.self="onMouseDown"
			@mouseup.self="onMouseUp"
			ref="panelHeader"
		>
			<h4
				v-if="!hasHeaderSlot"
				class="znpb-panel__header-name"
			>
				{{ panelName }}
			</h4>

			<slot name="header" />

			<Icon
				v-if="showClose"
				icon="close"
				:size="14"
				class="znpb-panel__header-icon-close"
				@click.stop="$emit('close-panel')"
			/>

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
			class="znpb-editor-panel__resize znpb-editor-panel__resize--horizontal"
			@mousedown="activateHorizontalResize"
			v-if="!isAnyPanelDragging && allowHorizontalResize"
		/>
		<div
			class="znpb-editor-panel__resize znpb-editor-panel__resize--vertical"
			@mousedown="activateVerticalResize"
			v-if="panel.isDetached && !isAnyPanelDragging && allowVerticalResize"
		/>
	</div>
</template>
<script>
import { computed, ref, onBeforeUnmount, onMounted, watch } from 'vue'
import rafSchd from 'raf-schd'
import { useUI, useWindows, useEditorData } from '@composables'

export default {
	name: 'BasePanel',
	props: {
		cssClass: {
			type: [String, Object],
			required: false,
			default: ''
		},
		panelName: {
			type: String,
			required: true
		},
		panelId: {
			type: String,
			required: false
		},
		expanded: {
			type: Boolean,
			required: false,
			default: false
		},
		showHeader: {
			type: Boolean,
			required: false,
			default: true
		},
		showClose: {
			type: Boolean,
			required: false,
			default: true
		},
		allowHorizontalResize: {
			type: Boolean,
			required: false,
			default: true
		},
		allowVerticalResize: {
			type: Boolean,
			required: false,
			default: true
		},
		closeOnEscape: {
			type: Boolean,
			required: false,
			default: true
		},
		panel: {}
	},
	setup (props, { slots, emit }) {
		const { isAnyPanelDragging, openPanels, getPanel, panelPlaceholder, setPanelPlaceholder, getMainbarPosition, getIframeOrder, iFrame, setIframePointerEvents, saveUI } = useUI()
		const { addEventListener, removeEventListener } = useWindows()
		const { editorData } = useEditorData()

		// REFS
		const root = ref(null)
		const panelOffset = ref(null)
		const initialPosition = ref({
			posY: 0,
			posX: 0
		})
		const dragginMoved = ref({
			x: null,
			y: null
		})

		// COMPUTED
		const panelPosition = computed(() => {
			return props.panel.panelPos > getIframeOrder() ? 'right' : 'left'
		})

		const getCssClass = computed(() => {
			let classes = ''
			classes += props.panel.isDetached ? ' znpb-editor-panel--detached' : ' znpb-editor-panel--attached'
			classes += props.cssClass ? props.cssClass : ''

			if (!props.panel.isDetached) {
				classes += panelPosition.value === 'right' ? ' znpb-editor-panel--right' : ' znpb-editor-panel--left'
			}
			return classes
		})

		const panelStyles = computed(() => {
			const cssStyles = {
				'min-width': props.panel.width + 'px',
				width: props.panel.width + 'px',
				height: props.panel.isDetached ? (props.panel.height ? props.panel.height + 'px' : '90%') : '100%',
				order: props.panel.panelPos,

				// Positions for detached
				top: !props.panel.isDragging && props.panel.isDetached && props.panel.offsets.posY !== 0 ? props.panel.offsets.posY + 'px' : null,
				left: !props.panel.isDragging && props.panel.isDetached ? props.panel.offsets.posX + 'px' : null,
				position: props.panel.isDetached ? 'fixed' : 'relative',

				// Dragging transform
				transform: props.panel.isDragging ? `translate3d(${dragginMoved.value.x}px, ${dragginMoved.value.y}px, 0)` : null,
				zIndex: props.panel.isDragging ? 9 : null

			}
			return cssStyles
		})

		const hasHeaderSlot = computed(() => {
			return !!slots.header
		})

		// METHODS
		let initialMovePosition = {
			posX: null,
			posY: null,
		}
		const rafMovePanel = rafSchd(movePanel)
		function onMouseDown (event) {
			setIframePointerEvents(true)
			document.body.style.userSelect = 'none'

			const { clientX, clientY } = event

			const boundingClientRect = root.value.getBoundingClientRect()
			initialMovePosition = {
				posX: clientX,
				posY: clientY,
				oldTop: boundingClientRect.top,
				oldLeft: boundingClientRect.left,
				oldHeight: boundingClientRect.height,
			}

			dragginMoved.value = {
				x: boundingClientRect.left,
				y: boundingClientRect.top
			}

			window.addEventListener('mousemove', rafMovePanel)
			window.addEventListener('mouseup', onMouseUp)
		}

		function movePanel (event) {
			const { posX, posY, oldTop, oldLeft } = initialMovePosition
			const { pageY, pageX } = event
			const boundingClientRect = root.value.getBoundingClientRect()

			if (!props.panel.isDragging) {
				const xMoved = Math.abs(posX - pageX)
				const yMoved = Math.abs(posY - pageY)
				const dragThreshold = 5

				// Check if we should detach the panel
				if (xMoved > dragThreshold || yMoved > dragThreshold) {
					props.panel.set('isDetached', true)
					props.panel.set('isDragging', true)
				}
			} else {
				const maxBottom = window.innerHeight - boundingClientRect.height
				const newTop = oldTop + pageY - posY < 0 ? 0 : oldTop + pageY - posY
				const MinMaxTop = newTop > maxBottom ? maxBottom : newTop

				let MinMaxLeft = oldLeft + pageX - posX
				const maxLeft = window.innerWidth - boundingClientRect.width
				if (oldLeft + pageX - posX <= 0) {
					MinMaxLeft = 0
				} else if (oldLeft + pageX - posX > maxLeft) {
					MinMaxLeft = maxLeft
				}

				dragginMoved.value = {
					x: MinMaxLeft,
					y: MinMaxTop
				}
			}
		}

		function onMouseUp () {
			document.body.style.userSelect = null
			setIframePointerEvents(false)

			// Since we have user RafScheduler we need to stop this in an animation frame in order to prevent
			// the situation were move panel fires after mouseUp
			window.requestAnimationFrame(() => {
				props.panel.isDragging = false

				dragginMoved.value = {
					x: null,
					y: null
				}
			})

			// Save the panel position
			panelOffset.value = root.value.getBoundingClientRect()
			props.panel.offsets = {
				posX: panelOffset.value.left,
				posY: panelOffset.value.top
			}

			initialPosition.value = {
				posX: panelOffset.value.left,
				posY: panelOffset.value.top
			}

			window.removeEventListener('mousemove', rafMovePanel)
			window.removeEventListener('mouseup', onMouseUp)

		}

		const rafResizeHorizontal = rafSchd(resizeHorizontal)
		let initialHMouseX = null
		let initialWidth = null
		function activateHorizontalResize (event) {
			setIframePointerEvents(true)

			document.body.style.userSelect = 'none'
			document.body.style.cursor = 'w-resize'

			initialHMouseX = event.clientX
			initialWidth = props.panel.width

			// Attach events
			window.addEventListener('mousemove', rafResizeHorizontal)
			window.addEventListener('mouseup', deactivateHorizontal)
		}

		function resizeHorizontal (event) {
			const draggedHorizontal = event.clientX - initialHMouseX
			const width = panelPosition.value === 'left' || props.panel.isDetached ? draggedHorizontal + initialWidth : -draggedHorizontal + initialWidth

			props.panel.set('width', width < 360 ? 360 : width, true)
		}

		function deactivateHorizontal () {
			setIframePointerEvents(false)

			document.body.style.userSelect = null
			document.body.style.cursor = null

			window.removeEventListener('mousemove', rafResizeHorizontal)
			window.removeEventListener('mouseup', deactivateHorizontal)
			saveUI()
		}

		const rafResizeVertical = rafSchd(resizeVertical)
		let initialVMouseY = null
		let initialHeight = null
		function activateVerticalResize (event) {
			setIframePointerEvents(true)
			document.body.style.userSelect = 'none'
			document.body.style.cursor = 'n-resize'

			initialHeight = root.value.clientHeight
			initialVMouseY = event.clientY

			window.addEventListener('mousemove', rafResizeVertical)
			window.addEventListener('mouseup', deactivateVertical)
		}

		function resizeVertical (event) {
			props.panel.set('isDetached', true)

			const draggedVertical = event.clientY - initialVMouseY
			const newHeightValue = initialHeight + draggedVertical

			if (event.clientY < window.innerHeight) {
				props.panel.set('height', newHeightValue > root.value.parentNode.clientHeight ? root.value.parentNode.clientHeight : newHeightValue)
			}
		}

		function deactivateVertical () {
			setIframePointerEvents(false)

			document.body.style.userSelect = null
			document.body.style.cursor = null

			window.removeEventListener('mousemove', rafResizeVertical)
			window.removeEventListener('mouseup', deactivateVertical)
			saveUI()
		}


		/**
		 * Close panel on escape key
		 */
		function onKeyDown (event) {
			if (event.which === 27) {
				emit('close-panel')
				event.stopImmediatePropagation()
			}
		}

		// LIFECYCLE
		onMounted(() => {
			if (props.closeOnEscape) {
				addEventListener('keydown', onKeyDown)
			}

			panelOffset.value = root.value.getBoundingClientRect()
			props.panel.offsets = {
				posX: panelOffset.value.left,
				posY: panelOffset.value.top
			}

			initialPosition.value = {
				posX: panelOffset.value.left,
				posY: panelOffset.value.top
			}
		})


		onBeforeUnmount(() => {
			window.removeEventListener('mousemove', rafResizeHorizontal)
			window.removeEventListener('mouseup', deactivateHorizontal)
			window.removeEventListener('mousemove', rafResizeVertical)
			window.removeEventListener('mouseup', deactivateVertical)
			removeEventListener('keydown', onKeyDown)

			// Reset document styles
			document.body.style.cursor = null
			document.body.style.userSelect = null
		})

		return {
			// refs
			root,

			// Computed
			getCssClass,
			panelStyles,
			isAnyPanelDragging,
			hasHeaderSlot,

			// Methods
			onMouseDown,
			onMouseUp,
			activateHorizontalResize,
			activateVerticalResize
		}
	}
}
</script>
<style lang="scss">
/* style panel */
.znpb-editor-panel {
	position: relative;
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
		border-right: var(--zb-panel-sideborder) solid
		var(--zb-surface-border-color);
		.znpb-editor-panel__resize--horizontal {
			right: -6px;
		}
	}

	&--left + &--left, &--right + &--right {
		border-left: 1px solid var(--zb-surface-border-color);
	}
	&--right {
		// box-shadow: -2px 0 0 0 var(--zb-surface-border-color);
		border-left: var(--zb-panel-sideborder) solid
		var(--zb-surface-border-color);
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
		box-shadow: 0 0 0 var(--zb-panel-sideborder)
		var(--zb-surface-border-color);
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
		transition: color .15s ease-out;
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
	z-index: 1;

	&--horizontal {
		top: 0;
		width: 6px;
		height: 100%;
		&::before {
			content: "";
			position: absolute;
			top: 0;
			left: 0;
			width: 0;
			height: 100%;
			background-color: var(--zb-secondary-color);
			transition: width .1s;
			opacity: .6;
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
			opacity: .6;
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
</style>
