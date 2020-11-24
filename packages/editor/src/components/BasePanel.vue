<template>
	<div
		ref="panelContainer"
		:class="getCssClass"
		:style="panelStyles"
		:id='panelId'
		class="znpb-editor-panel"
	>
		<!-- start panel header -->
		<div
			class="znpb-panel__header"
			v-if="showHeader"
			@mousedown.self="startDrag"
			@mouseup.self="disablePanelMove"
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
		</div>

		<!-- end panel header -->
		<!-- content -->
		<div class="znpb-panel__content_wrapper">
			<slot />
		</div>
		<!-- resize divs -->
		<div
			class="znpb-editor-panel__resize znpb-editor-panel__resize--horizontal"
			@mousedown="activateHorizontalResize"
			v-if="!isAnyPanelDragging"
		/>
		<div
			class="znpb-editor-panel__resize znpb-editor-panel__resize--vertical"
			@mousedown="activateVerticalResize"
			v-if="!isAnyPanelDragging"
		/>
	</div>
</template>
<script>

import rafSchd from 'raf-schd'
import { usePanels, useEditorInteractions, useWindows } from '@composables'

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
	setup () {
		const { isAnyPanelDragging, openPanels, getPanel, panelPlaceholder, setPanelPlaceholder } = usePanels()
		const { getMainbarPosition, getIframeOrder, iFrame } = useEditorInteractions()
		const { addEventListener, removeEventListener } = useWindows()

		return {
			isAnyPanelDragging,
			openPanels,
			getPanel,
			panelPlaceholder,
			setPanelPlaceholder,
			getIframeOrder,
			getMainbarPosition,
			iFrame,
			addEventListener,
			removeEventListener
		}
	},
	data () {
		return {
			unit: 'px',
			userSel: null,
			isDragging: false,
			initialPosition: null,
			lastPositionX: 0,
			lastPositionY: 0,
			position: {
				posX: 0,
				posY: 0,
				toPanelRight: null,
				toPanelLeft: null
			},
			panelOffset: null,
			initialHeight: null,
			initialWidth: null,
			initialVMouseY: null,
			initialHMouseX: null,
			touchesTop: null,
			direction: null,
			placement: null
		}
	},
	mounted () {
		if (this.closeOnEscape) {
			this.addEventListener('keydown', this.onKeyDown)
		}
		this.panelOffset = this.$refs.panelContainer.getBoundingClientRect()
		this.position = {
			posX: this.panelOffset.left,
			posY: this.panelOffset.top
		}
		this.lastPositionX = this.panelOffset.left
		this.lastPositionY = this.panelOffset.top
	},
	computed: {
		selectors () {
			let selectors = []
			this.openPanels.forEach(panel => {
				selectors.push('#' + panel.id)
			})
			selectors.push('#znpb-editor-iframe')
			selectors.push('#znpb-panel-placeholder')
			return selectors
		},
		orders () {
			const orders = []
			const panels = this.openPanels.forEach(panel => {
				orders.push(panel.panelPos)
			})

			orders.push(this.getIframeOrder())
			return orders
		},
		panelsAndIframe () {
			const panels = {}
			this.openPanels.forEach(panel => {
				panels[panel.id] = panel.panelPos
			})
			panels['znpb-editor-iframe'] = this.getIframeOrder()
			return panels
		},
		filteredOpenedPanels () {
			const panels = {}
			this.openPanels.forEach(panel => {
				if (panel.id !== this.panelId) {
					panels[panel.id] = panel.panelPos
				}
			})
			panels['znpb-editor-iframe'] = this.getIframeOrder()
			return panels
		},
		panelPosition () {
			return this.panel.panelPos > this.getIframeOrder() ? 'right' : 'left'
		},
		hasHeaderSlot () {
			return !!this.$slots['header']
		},
		/**
		 * Returns the css styles that will be applied to the main panel
		 */
		panelStyles () {
			const panel = this.panel
			const cssStyles = {
				'min-width': panel.width.value + panel.width.unit,
				width: panel.width.value + panel.width.unit,
				height: (panel.height.unit === 'auto' && this.isDragging) ? '90%' : !panel.isDetached ? '99.9%' : panel.height.value + panel.height.unit,
				top: (!this.isDragging && panel.isDetached) ? this.position.posY + this.unit : null,
				left: (!this.isDragging && panel.isDetached) ? this.position.posX + this.unit : null,
				position: panel.isDetached ? 'fixed' : 'relative',
				order: panel.panelPos,
				userSelect: this.userSel,
				transform: (this.isDragging) ? `translate3d(${this.position.posX}${this.unit},${this.position.posY}${this.unit},0)` : null
			}
			return cssStyles
		},
		getCssClass () {
			let classes = ''
			classes += this.panel.isDetached ? ' znpb-editor-panel--detached' : ' znpb-editor-panel--attached'
			classes += this.cssClass ? this.cssClass : ''
			classes += this.touchesTop ? ' znpb-editor-panel--top' : ''
			if (!this.panel.isDetached) {
				classes += this.panel.panelPos > this.getIframeOrder() ? ' znpb-editor-panel--right' : ' znpb-editor-panel--left'
			}
			return classes
		}

	},
	methods: {
		onKeyDown (event) {
			if (event.which === 27) {
				this.$emit('close-panel')
				event.stopImmediatePropagation()
			}
		},

		startDrag (event) {
			this.$refs.panelContainer.style.pointerEvents = 'none'
			this.panel.set('isDragging', true)
			window.addEventListener('mousemove', this.movePanel)
			window.addEventListener('mouseup', this.disablePanelMove)
			this.iFrame.set('pointerEvents', true)
			this.panelOffset = this.$refs.panelContainer.getBoundingClientRect()
			this.lastPositionX = this.panelOffset.left
			this.lastPositionY = this.panelOffset.top
			this.position.posX = this.panelOffset.left
			this.position.posY = this.panelOffset.top

			this.position.toPanelRight = this.panelOffset.right - event.clientX
			this.position.toPanelLeft = event.clientX - this.panelOffset.left

			this.isDragging = true

			this.panel.set('isDetached', true)
			this.userSel = 'none'

			this.initialPosition = {
				posX: event.clientX,
				posY: event.clientY
			}
		},
		movePanel (event) {
			let target = null
			let order = null
			let left = null
			let visibility = null
			let closest = null
			this.touchesTop = event.clientY < 10
			this.panelOffset = this.$refs.panelContainer.getBoundingClientRect()
			this.position.posX = this.lastPositionX + event.pageX - this.initialPosition.posX
			this.position.posY = this.lastPositionY + event.pageY - this.initialPosition.posY
			const touchesRight = event.clientX + this.position.toPanelRight > window.innerWidth - 10 - (this.getMainbarPosition() === 'right' ? 60 : 0)
			const touchesLeft = event.clientX - this.position.toPanelLeft < (this.getMainbarPosition() === 'left' ? 60 : 10)

			if (document.elementFromPoint(event.clientX, event.clientY)) {
				target = document.elementFromPoint(event.clientX, event.clientY)
				if (target) {
					closest = target.closest([...this.selectors])
				}
				if (closest) {
					const overlappedPanelId = closest.id
					const idProps = this.getPanel(overlappedPanelId)
					if (idProps && !idProps.isDetached) {
						const domElement = document.getElementById(overlappedPanelId)
						if (event.clientX > domElement.offsetLeft + (idProps.width.value / 2) && event.clientX < domElement.offsetLeft + idProps.width.value) {
							order = idProps.panelPos + 1
							this.placement = 'after'
							left = domElement.offsetLeft + idProps.width.value - (Math.max(...this.orders) === idProps.panelPos && this.getMainbarPosition() === 'right' ? 5 : 0)
						}
						if (event.clientX > domElement.offsetLeft && event.clientX < domElement.offsetLeft + idProps.width.value / 2) {
							order = idProps.panelPos
							this.placement = 'before'
							left = domElement.offsetLeft
						}
						visibility = true
					}
				} else {
					visibility = false
				}

				if (touchesLeft) {
					visibility = true
					order = Math.min(...this.orders) - 1
					if (this.getMainbarPosition() === 'left') {
						left = 60
					}
				} else if (touchesRight) {
					left = window.innerWidth - 5
					if (this.getMainbarPosition() === 'right') {
						left = window.innerWidth - 65
					} else {
						left = window.innerWidth - 5
					}
					visibility = true
					order = Math.max(...this.orders) + 1
				}
				if (left >= window.innerWidth) {
					left -= 5
				}
				this.setPanelPlaceholder({
					visibility,
					order,
					left
				})
			}

			const maxBottom = window.innerHeight - this.panelOffset.height
			let newTop = this.position.posY < 0 ? 0 : this.position.posY
			this.position.posY = newTop > maxBottom ? maxBottom : newTop

			const maxLeft = window.innerWidth - this.panelOffset.width
			const minLeft = 0
			if (this.position.posX <= minLeft) {
				this.position.posX = 0
			} else if (this.position.posX > maxLeft) {
				this.position.posX = maxLeft
			}
		},
		setOrderAndPlaceholder (order, visibility, placement) {
			// Sets openedPanels and previewIframe positions consecutive
			for (const id in this.filteredOpenedPanels) {
				const panelOrder = this.filteredOpenedPanels[id]

				let otherPanelOrder = null

				if (placement === 'before' && panelOrder >= order) {
					otherPanelOrder = panelOrder + 1
				} else if (placement === 'after' && panelOrder > order) {
					otherPanelOrder = panelOrder + 1
				} else {
					otherPanelOrder = panelOrder
				}
				if (id === 'znpb-editor-iframe') {
					this.iFrame.set('order', otherPanelOrder)
				} else {
					this.panel.set('panelPos', otherPanelOrder)
				}

				this.panel.set('panelPos', order)
			}
			this.mapOrders()
		},
		mapOrders () {
			// maps panels from 1 to openedPanels.length
			let orders = []
			for (const id in this.panelsAndIframe) {
				orders.push([id, this.panelsAndIframe[id]])
			}
			orders.sort(function (a, b) {
				return a[1] - b[1]
			})
			orders.forEach((order, index) => {
				if (order[0] === 'znpb-editor-iframe') {
					this.iFrame.set('order', index + 1)
				} else {
					this.panel.set('panelPos', index + 1)
				}
			})
		},
		disablePanelMove () {
			this.$refs.panelContainer.style.pointerEvents = null
			this.panel.set('isDragging', false)
			window.removeEventListener('mousemove', this.movePanel)
			window.removeEventListener('mouseup', this.disablePanelMove)
			const order = this.panelPlaceholder.order

			this.isDragging = false
			if (this.panelPlaceholder.visibility) {
				if (order !== this.panel.panelPos) {
					this.setOrderAndPlaceholder(order, this.panelPlaceholder.visibility, this.placement)
				}
				this.panel.set('isDetached', false)
			}

			this.initialPosition = null
			this.iFrame.set('pointerEvents', false)
			this.userSel = null
			this.setPanelPlaceholder({
				...this.panelPlaceholder,
				visibility: false
			})
		},

		deactivateSelection () {
			document.body.style.userSelect = 'none'
		},
		resetSelection () {
			document.body.style.userSelect = null
		},
		activateHorizontalResize () {
			this.iFrame.set('pointerEvents', true)
			this.deactivateSelection()
			this.initialHMouseX = event.clientX
			this.initialWidth = this.panel.width.value
			this.rafResizeHorizontal = rafSchd(this.resizeHorizontal)
			window.addEventListener('mousemove', this.rafResizeHorizontal)
			window.addEventListener('mouseup', this.deactivateHorizontal)
		},
		resizeHorizontal (event) {
			document.body.style.cursor = 'w-resize'
			const draggedHorizontal = event.clientX - this.initialHMouseX

			const width = this.panelPosition === 'left' || this.panel.isDetached ? draggedHorizontal + this.initialWidth : -draggedHorizontal + this.initialWidth

			this.panel.set('width', {
				value: width < 360 ? 360 : width,
				unit: 'px'
			})
		},
		deactivateHorizontal () {
			window.removeEventListener('mousemove', this.rafResizeHorizontal)
			this.iFrame.set('pointerEvents', false)
			this.resetSelection()
			document.body.style.cursor = null
		},
		activateVerticalResize (event) {
			this.iFrame.set('pointerEvents', true)
			this.deactivateSelection()
			this.initialHeight = this.$refs.panelContainer.clientHeight
			this.initialVMouseY = event.clientY
			this.rafResizeVertical = rafSchd(this.resizeVertical)
			window.addEventListener('mousemove', this.rafResizeVertical)
			window.addEventListener('mouseup', this.deactivateVertical)
		},
		resizeVertical (event) {
			this.panel.set('isDetached', true)
			document.body.style.cursor = 'n-resize'
			const draggedVertical = event.clientY - this.initialVMouseY
			const newHeightValue = this.initialHeight + draggedVertical

			this.panel.set('height', {
				value: newHeightValue < this.$refs.panelHeader.clientHeight ? this.$refs.panelHeader.clientHeight : newHeightValue,
				unit: 'px'
			})

		},
		deactivateVertical () {
			window.removeEventListener('mousemove', this.rafResizeVertical)
			document.body.style.cursor = null
			this.iFrame.set('pointerEvents', false)
			this.resetSelection()
		}

	},
	beforeUnmount () {
		this.removeEventListener('keydown', this.onKeyDown)
	}
}
</script>
<style lang="scss">
/* style panel */
.znpb-editor-panel {
	position: relative;
	display: flex;
	flex-direction: column;
	background: $surface;
	&--attached {
		height: 100%;
	}
	&:after {
		clear: both;
	}
	&--top {
		height: 100%;
	}
	&--left {
		// box-shadow: 2px 0 0 0 $border-color;
		border-right: 2px solid $border-color;
		.znpb-editor-panel__resize--horizontal {
			right: -3px;
		}
	}

	&--left + &--left, &--right + &--right {
		border-left: 1px solid $surface-variant;
	}
	&--right {
		// box-shadow: -2px 0 0 0 $border-color;
		border-left: 2px solid $border-color;
		.znpb-editor-panel__resize--horizontal {
			left: -3px;
		}
	}
	&--right + &--right {
		box-shadow: none;
	}
	&--detached {
		box-shadow: 0 0 0 2px $border-color;
		border: none;
		.znpb-editor-panel__resize--horizontal {
			right: -3px;
		}
	}
}

.znpb-panel__header {
	position: relative;
	display: flex;
	justify-content: space-between;
	align-items: center;
	flex-shrink: 0;
	background-color: $surface;
	border-bottom: 1px solid $surface-variant;
	cursor: move;

	& > .znpb-editor-icon-wrapper {
		margin-right: 15px;
		color: $icons-color;
		color: $surface-headings-color;
		transition: color .15s ease-out;
		cursor: pointer;

		.zion-icon.zion-svg-inline {
			margin: 0 auto;
		}
		&:last-child {
			margin-right: 0;
		}
		&:hover {
			color: darken($icons-color, 10%);
		}
	}

	&-icon-close {
		padding: 21.5px 20px 21.5px 15px;
	}
}
h4.znpb-panel__header-name {
	padding: 19px 0 19px 20px;
	color: $surface-active-color;
	font-size: 14px;
	font-weight: 500;
	line-height: 1.4;
	cursor: pointer;

	& > .znpb-editor-icon-wrapper {
		margin-left: 5px;
		color: $icons-color;

		&:hover {
			color: darken($icons-color, 10%);
			background: none;
		}
	}
}
.znpb-editor-panel__resize {
	position: absolute;
	z-index: 1000;
	&--horizontal {
		top: 0;
		width: 5px;
		height: 100%;
		&:hover {
			background-color: rgba($secondary, .6);
			cursor: ew-resize;
		}
	}

	&--vertical {
		right: 0;
		bottom: -3px;
		width: 100%;
		height: 5px;
		&:hover {
			background-color: rgba($secondary, .6);
			cursor: n-resize;
		}
	}
}

.znpb-panel__content_wrapper {
	display: flex;
	flex-direction: column;
	overflow: hidden;
	height: 100%;
}
</style>
