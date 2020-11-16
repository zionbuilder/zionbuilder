<template>
	<div
		class="znpb-element-toolbox"
		:class="{
			'znpb-element-toolbox--dragging': isAnyDragging,
			[`znpb-element-toolbox__resize-${activeDragType}-${activeDragPosition}--dragging`]: isToolboxDragging
		}"
		:style="{
			'z-index': zIndex
		}"
		ref="rectangle"
		@mouseover.stop="zIndex = 1001"
		@mouseout.stop="zIndex = null"
	>
		<template v-if="computedStyle">
			<!-- Width/height -->
			<Tooltip
				v-for="(position, positionIndex) in positions"
				:ref="`sizeDrag--${position}`"
				tooltip-class="hg-popper--big-arrows znpb-sizing-label"
				:placement='getPopperPlacement'
				append-to="body"
				trigger="null"
				:show-arrows="false"
				:show="activeDragType === 'size' && activeDragPosition === position && newValues!= undefined"
				:key="`size-${position}`"
				:popper-ref="popperRef"
				@mousedown.left.stop="startSizeDrag($event, position)"
			>
				<template #content>
					<span>
						{{newValues !== undefined ? newValues + 'px' : ''}}
					</span>
				</template>

				<div
					class="znpb-element-toolbox__resize-width znpb-element-toolbox__resize-dimensions"
					:class="{
						[`znpb-element-toolbox__resize-width--${positionIndex}`]: true,
						[`znpb-element-toolbox__resize-dimensions--${(positionIndex === 'top' || positionIndex === 'bottom' )? 'height' : 'width'}`]:true
					}"
					@mousedown.left.stop="startSizeDrag($event, position)"
				>
					<span class="znpb-element-toolbox__resize-width-bg"></span>
				</div>

			</Tooltip>

			<!-- Paddings -->
			<template v-for="( positions, type ) in positions2">
				<div
					v-for="position in positions"
					:key="`${type}-${position}`"
					:class="{
						[`znpb-element-toolbox__resize`]: true,
						[`znpb-element-toolbox__resize-${type}`]: true,
						[`znpb-element-toolbox__resize--${position}`]: true,
						['znpb-element-toolbox__resize--dragging']: activeDragType === type && (activeDragPosition === position || reversedPosition === position)

					}"
					@mousedown.left.stop="startSpacingDrag({event: $event, type, position})"
				>
					<div
						class="znpb-element-toolbox__resize-value"
						:style="computedHelpersStyle[position]"
					>
						<span v-if="Math.abs(parseInt(computedStyle[position])) > 20">{{computedSavedValues[position]}}</span>
					</div>
				</div>
			</template>

			<!-- Add new Button -->
			<transition
				appear
				name="bounce-add-icon"
			>
				<div
					v-if="!isAnyDragging"
					class="znpb-element-toolbox__add-element-button"
					@click="toggleAddElementsPopup"
					ref="addElementsPopupButton"
				>
					<Icon
						icon="plus"
						:rounded="true"
					/>
				</div>

			</transition>

			<TopBarToolbox
				v-if="!isAnyDragging"
				:element="element"
				@set-top-bar-display="setTopBarDisplay($event)"
			/>
		</template>
	</div>
</template>

<script>
// Utils
import { ref } from 'vue'
import rafSchd from 'raf-schd'
import { mapActions } from 'vuex'
import { useWindows } from '@zb/editor'
import { useAddElementsPopup, useElementActions, useIsDragging } from '@zb/editor'
import { useResponsiveDevices } from '@zb/components'

// Components
import TopBarToolbox from './TopBarToolbox.vue'

export default {
	name: 'ElementToolbox',
	components: {
		TopBarToolbox
	},
	props: {
		element: Object,
		canHideToolbox: {
			type: Boolean,
			required: true,
			default: true
		},
		isToolboxDragging: {
			type: Boolean,
			required: true,
			default: false
		}
	},
	setup (props) {
		const showColumnTemplates = ref(false)
		const addElementsPopupButton = ref(null)
		const { activeResponsiveDeviceInfo } = useResponsiveDevices()
		const { focusedElement } = useElementActions()
		const { addEventListener, removeEventListener } = useWindows()

		const toggleAddElementsPopup = () => {
			const { showAddElementsPopup } = useAddElementsPopup()
			showAddElementsPopup(props.element, addElementsPopupButton, {
				placement: 'next'
			})
		}
		const { isDragging } = useIsDragging()

		return {
			showColumnTemplates,
			addElementsPopupButton,
			toggleAddElementsPopup,
			activeResponsiveDeviceInfo,
			focusedElement,
			isDragging,
			addEventListener,
			removeEventListener
		}
	},
	data () {
		return {
			// Active element positions
			computedStyle: null,
			zIndex: null,
			// Dragging
			startClientX: null,
			startClientY: null,
			activeDragPosition: null,
			activeDragType: null,
			initialDraggValue: null,
			reversedPosition: null,

			// Size
			initialSizeValue: null,
			positions2: {
				padding: ['paddingTop', 'paddingBottom', 'paddingLeft', 'paddingRight'],
				margin: ['marginTop', 'marginBottom', 'marginLeft', 'marginRight']
			},
			styleMap: {
				paddingTop: 'padding-top',
				paddingRight: 'padding-right',
				paddingBottom: 'padding-bottom',
				paddingLeft: 'padding-left',
				marginTop: 'margin-top',
				marginRight: 'margin-right',
				marginBottom: 'margin-bottom',
				marginLeft: 'margin-left',
				width: 'width',
				height: 'height'
			},
			positions: {
				top: 'Top',
				bottom: 'Bottom',
				right: 'Right',
				left: 'Left'
			},
			popperRefValues: {
				clientX: 0,
				clientY: 0
			},
			popperRef: {
				getBoundingClientRect: () => ({
					top: this.popperRefValues.clientY,
					right: this.popperRefValues.clientX,
					bottom: this.popperRefValues.clientY,
					left: this.popperRefValues.clientX,
					width: 0,
					height: 0
				})
			},

			// Columns add
			showColumnTemplates: false,
			newValues: null,
			settingEvenPaddingDimensions: null,
			settingEvenMarginsDimensions: null,
			addToHistory: false,
			isTopBarOpen: null
		}
	},
	computed: {
		/**
		 * Returns the saved value for each property defaulting to actual size
		 */
		computedSavedValues () {
			const savedValues = this.element.getOptionValue(`_styles.wrapper.styles.${this.activeResponsiveDeviceInfo.id}.default`, {})
			return {
				paddingTop: savedValues[this.styleMap.paddingTop] || this.computedStyle.paddingTop,
				paddingRight: savedValues[this.styleMap.paddingRight] || this.computedStyle.paddingRight,
				paddingBottom: savedValues[this.styleMap.paddingBottom] || this.computedStyle.paddingBottom,
				paddingLeft: savedValues[this.styleMap.paddingLeft] || this.computedStyle.paddingLeft,
				marginTop: savedValues[this.styleMap.marginTop] || this.computedStyle.marginTop,
				marginRight: savedValues[this.styleMap.marginRight] || this.computedStyle.marginRight,
				marginBottom: savedValues[this.styleMap.marginBottom] || this.computedStyle.marginBottom,
				marginLeft: savedValues[this.styleMap.marginLeft] || this.computedStyle.marginLeft,
				height: savedValues[this.styleMap.height] || this.computedStyle.height,
				width: savedValues[this.styleMap.width] || this.computedStyle.width
			}
		},
		computedHelpersStyle () {
			const { height, width, ...remainingProperties } = this.styleMap

			const styles = {}
			Object.keys(remainingProperties).forEach(propertyId => {
				const property = remainingProperties[propertyId]
				const direction = propertyId.indexOf('Top') !== -1 || propertyId.indexOf('Bottom') !== -1 ? 'vertical' : 'horizontal'
				const sizeProperty = direction === 'vertical' ? 'height' : 'width'
				const sizeValue = Math.abs(parseInt(this.computedStyle[propertyId]))
				styles[propertyId] = {
					[sizeProperty]: `${sizeValue}px`
				}
			})

			return styles
		},
		getPopperPlacement () {
			if (this.activeDragPosition === 'Right') {
				return 'left'
			} else if (this.activeDragPosition === 'Top') {
				return 'bottom'
			} else if (this.activeDragPosition === 'Bottom') {
				return 'top'
			} else return 'right'
		},
		isAnyDragging () {
			return this.isDragging.value || this.isToolboxDragging
		},
		dragDimension () {
			if (this.activeDragPosition === 'Top' || this.activeDragPosition === 'Bottom') {
				return 'vertical'
			}

			return 'horizontal'
		}
	},
	methods: {
		...mapActions([
			'saveState'
		]),
		startSpacingDrag ({ event, type, position }) {
			const { clientX, clientY } = event

			// Prevent user selection
			document.body.style.userSelect = 'none'

			// Prevent tooltip from closing
			this.$emit('update:canHideToolbox', false)
			this.$emit('update:isToolboxDragging', true)

			// Set info for dragging
			const startClientX = clientX
			const startClientY = clientY
			this.activeDragType = type
			this.activeDragPosition = position

			const activeDragValue = this.computedSavedValues[position]
			const match = typeof activeDragValue === 'string' && activeDragValue ? activeDragValue.match(/^([+-]?[0-9]+([.][0-9]*)?|[.][0-9]+)(\D+)$/) : null
			const initialValue = match && match[1] ? parseInt(match[1]) : 0
			const initialUnit = match ? match[3] : ''

			this.onMouseMoveDebounced = rafSchd((event) => {
				let { direction, distance } = this.getDragInfo({
					event,
					type,
					position,
					startClientX,
					startClientY
				})
				let even = false

				// Invert the distance
				if (['padding', 'margin'].includes(type) && position.indexOf('Right') !== -1) {
					distance = distance * -1
				}

				// For percentage values, increment by 0.1
				let updatedValue = initialUnit === '%' ? initialValue + (distance * 0.1) : initialValue + distance

				// Check if the
				if (event.shiftKey) {
					updatedValue = Math.round(updatedValue / 5) * 5
				}

				// Prevent negative size for paddings
				if (type === 'padding') {
					updatedValue = Math.max(updatedValue, 0)
				}

				// Finally, round the value
				updatedValue = Math.round(updatedValue * 10) / 10

				// Don't proceed if the values are the same
				if (initialValue === updatedValue) {
					return
				}

				// Check if we need to set even spacing
				if (event.ctrlKey) {
					even = true
					this.reversedPosition = this.getReversedPosition(position)
				} else {
					even = false
					this.reversedPosition = null
				}

				this.updateElementStyle({
					newValue: `${updatedValue}${initialUnit}`,
					type,
					position,
					even: event.ctrlKey,
					reversedPositgetDocumentsion: this.reversedPosition
				})

				// Refresh sizes
				this.setComputedStyle()
			})

			this.addEventListener('mousemove', this.onMouseMoveDebounced)
			this.addEventListener('mouseup', this.onMouseUp)
		},
		getReversedPosition (position) {
			const typeAndPosition = position.split(/(?=[A-Z])/)
			const positionLocation = typeAndPosition[1]
			let reversePositionLocation

			switch (positionLocation) {
				case 'Top':
					reversePositionLocation = 'Bottom'
					break
				case 'Bottom':
					reversePositionLocation = 'Top'
					break
				case 'Left':
					reversePositionLocation = 'Right'
					break
				case 'Right':
					reversePositionLocation = 'Left'
					break
			}

			return `${typeAndPosition[0]}${reversePositionLocation}`
		},
		updateElementStyle ({ newValue, type, position, even, reversedPosition }) {
			const activeDragCssProperty = this.styleMap[position]

			// Save to history
			this.addToHistory = true

			this.element.updateOptionValue(`_styles.wrapper.styles.${this.activeResponsiveDeviceInfo.id}.default.${activeDragCssProperty}`, newValue)

			// If we need to update the opposite position
			if (even) {
				const activeDragReversedCssProperty = this.styleMap[reversedPosition]
				this.element.updateOptionValue(`_styles.wrapper.styles.${this.activeResponsiveDeviceInfo.id}.default.${activeDragReversedCssProperty}`, newValue)
			}
		},
		getDragInfo ({ event, type, position, startClientX, startClientY }) {
			const direction = position.indexOf('Top') !== -1 || position.indexOf('Bottom') !== -1 ? 'vertical' : 'horizontal'
			let distance = direction === 'vertical' ? event.clientY - startClientY : event.clientX - startClientX

			return {
				direction,
				distance
			}
		},
		onMouseUp () {
			// We just need to add to history
			if (this.addToHistory) {
				this.saveState(`Updated ${this.element.name} ${this.activeDragType}`)
			}

			// Cancel the scheduler
			this.onMouseMoveDebounced.cancel()

			this.removeEventListener('mousemove', this.onMouseMoveDebounced)
			this.removeEventListener('mouseup', this.onMouseUp)

			// Reset properties
			this.onMouseMoveDebounced = null
			this.activeDragType = null
			this.activeDragPosition = null
			this.addToHistory = false

			document.body.style.userSelect = null

			this.$emit('update:canHideToolbox', true)
			this.$emit('update:isToolboxDragging', false)
		},
		setTopBarDisplay (event) {
			this.isTopBarOpen = event
		},
		getNumberFromString (string) {
			return parseInt(string.match(/\d+/)[0])
		},
		setComputedStyle () {
			this.computedStyle = window.getComputedStyle(this.$parent.$el)
		},
		removeEvents () {
			this.removeEventListener('mousemove', this.changeSizeDebounced)
			this.removeEventListener('mousemove', this.changePaddingWidth)
			this.removeEventListener('mousemove', this.changeMarginWidth)
			this.removeEventListener('mouseup', this.endDragging)
		},
		getSizeChangePropertyFromPosition (position) {
			let propertyToChange = null

			if (position === 'Top' || position === 'Bottom') {
				propertyToChange = 'min-height'
			} else {
				propertyToChange = 'width'
			}

			return propertyToChange
		},
		startSizeDrag (event, position, positionIndex) {
			const { clientX, clientY } = event

			// Prevent toolbox from closing
			this.$emit('update:canHideToolbox', false)
			this.$emit('update:isToolboxDragging', true)

			this.startClientX = clientX
			this.startClientY = clientY
			this.popperRefValues = { clientX, clientY }

			this.activeDragPosition = position
			this.activeDragType = 'size'
			const property = this.getSizeChangePropertyFromPosition(position)
			document.body.style.userSelect = 'none'
			this.initialSizeValue = parseInt(this.getSizeValue(property))

			this.changeSizeDebounced = rafSchd(this.changeSize)

			this.addEventListener('mousemove', this.changeSizeDebounced)
			this.addEventListener('mouseup', this.endDragging)
		},
		getSizeValue (type) {
			// Return min-height
			let value = this.element.getOptionValue(`_styles.wrapper.styles.${this.activeResponsiveDeviceInfo.id}.default.${type}`)
			if (value !== null) {
				return value
			}

			const alternativeType = type === 'min-height' ? 'height' : 'width'

			const sizeValue = this.computedStyle.getPropertyValue(alternativeType)

			return this.getNumberFromString(sizeValue) || 0
		},
		changeSize (event) {
			const { clientX, clientY } = event

			let newValue = null
			const property = this.getSizeChangePropertyFromPosition(this.activeDragPosition)
			const direction = this.dragDimension === 'vertical' ? 'vertical' : 'horizontal'
			this.addToHistory = true

			if (direction === 'vertical') {
				const distance = clientY - this.startClientY
				newValue = this.activeDragPosition === 'Top' ? this.initialSizeValue - distance : this.initialSizeValue + distance
			} else {
				if (this.activeDragPosition === 'Right') {
					const distance = clientX - this.startClientX
					newValue = distance + this.initialSizeValue
				} else {
					const distance = this.startClientX - clientX
					newValue = distance + this.initialSizeValue
				}
			}

			// Prevent negative values
			if (newValue < 0) {
				newValue = 0
			}

			this.newValues = newValue
			this.element.updateOptionValue(`_styles.wrapper.styles.${this.activeResponsiveDeviceInfo.id}.default.${property}`, `${newValue}px`)

			// reposition tooltip
			if (this.$refs[`sizeDrag--${this.activeDragPosition}`]) {
				const { bottom, left, top, right } = this.$refs.rectangle.getBoundingClientRect()

				if (this.activeDragPosition === 'Top') {
					this.popperRefValues.clientY = top
				} else if (this.activeDragPosition === 'Right') {
					this.popperRefValues.clientX = right
				} else if (this.activeDragPosition === 'Bottom') {
					this.popperRefValues.clientY = bottom
				} else this.popperRefValues.clientX = left

				this.$refs[`sizeDrag--${this.activeDragPosition}`].scheduleUpdate()
			}
		},
		endDragging () {
			document.body.style.userSelect = null
			this.startClientX = null
			this.startClientY = null
			this.activeDragPosition = null
			this.popperRef = null

			// Prevent tooltip from closing
			this.$emit('update:canHideToolbox', true)
			this.$emit('update:isToolboxDragging', false)

			// We just need to add to history
			if (this.addToHistory) {
				this.saveState(`Updated ${this.element.name} ${this.activeDragType}`)
			}

			this.addToHistory = false
			this.settingEvenPaddingDimensions = false
			this.removeEvents()
		}
	},
	beforeUnmount () {
		this.removeEvents()
	},
	created () {
		this.setComputedStyle()
	}
}
</script>

<style lang="scss">
.znpb-element-toolbox {
	.znpb-element-toolbox__resize-dimensions {
		z-index: 1001;
		&--width {
			width: 6px;
			height: 100%;
		}
		&--height {
			width: 100%;
			height: 6px;
		}
	}
	.znpb-even-dimensions-horizontal, .znpb-even-dimensions-vertical {
		opacity: 1;
	}
}

.znpb-element-toolbox {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	font-size: 13px;
	transition: opacity .3s;
	pointer-events: none;
	user-select: none;

	&__resize {
		position: absolute;
		z-index: 1000;
		transition: opacity .3s;
		opacity: 0;
		pointer-events: all;

		&--dragging {
			z-index: 1000;
			opacity: 1;
			visibility: visible;
		}

		&-value {
			display: flex;
			justify-content: center;
			align-items: center;
			font-family: Roboto, sans-serif;
			font-size: 10px;
			font-style: normal;
			font-weight: 700;
			text-decoration: none;
			text-decoration: none;
			text-shadow: none;
			text-transform: none;

			span {
				padding: 4px;
				line-height: 1;
				background-color: $surface;
				border-radius: 3px;
			}
		}
		&-width {
			position: absolute;
			z-index: 1000;
			pointer-events: all;

			.znpb-element__wrapper > .znpb-element-toolbox & {
				background-color: transparent;
				.znpb-element-toolbox__resize-width-bg {
					background-color: $elements-toolbox-border-color;
				}
			}
			.zb-column > .znpb-element-toolbox & {
				background-color: transparent;
				.znpb-element-toolbox__resize-width-bg {
					background-color: $column-border-color;
				}
			}
			.zb-section > .znpb-element-toolbox & {
				background-color: transparent;
				.znpb-element-toolbox__resize-width-bg {
					background-color: $section-border-color;
				}
			}
			&-bg {
				position: absolute;
				top: 0;
				right: 0;
				bottom: 0;
				left: 0;
				margin: 0 auto;
			}

			&--top {
				top: 2px;
				left: -1px;
				width: calc(100% + 2px);
				height: 6px;
				transform: translateY(-50%);
				cursor: row-resize;

				&:hover {
					width: 100%;
					padding: 3px;
					.znpb-element-toolbox__resize-width-bg {
						height: 6px;
					}
				}
				.znpb-element-toolbox__resize-width-bg {
					width: 100%;
					height: 2px;
					transition: height .3s;
				}
			}
			&--right {
				top: 0;
				right: 0;
				width: 6px;
				height: 100%;
				transform: translateX(50%);
				cursor: col-resize;

				&:hover {
					padding: 3px;
					.znpb-element-toolbox__resize-width-bg {
						width: 6px;
					}
				}
				.znpb-element-toolbox__resize-width-bg {
					width: 2px;
					height: 100%;
					transition: width .3s;
				}
			}
			&--bottom {
				bottom: -2px;
				left: -1px;
				width: calc(100% + 2px);
				min-height: 6px;
				transform: translateY(50%);
				cursor: row-resize;

				&:hover {
					padding: 3px;
					.znpb-element-toolbox__resize-width-bg {
						height: 6px;
					}
				}
				.znpb-element-toolbox__resize-width-bg {
					width: 100%;
					height: 2px;
					transition: height .3s;
				}
			}
			&--left {
				top: 0;
				left: 0;
				min-width: 6px;
				height: 100%;
				transform: translateX(-50%);
				cursor: col-resize;

				&:hover {
					padding: 3px;
					.znpb-element-toolbox__resize-width-bg {
						width: 6px;
					}
				}
				.znpb-element-toolbox__resize-width-bg {
					width: 2px;
					height: 100%;
					transition: width .3s;
				}
			}
		}

		&--paddingTop {
			top: 0;
			left: 0;
			width: 100%;
			cursor: n-resize;

			& .znpb-element-toolbox__resize-value {
				top: 0;
				left: 0;
				width: 100%;
			}

			.znpb-element-toolbox:not(.znpb-element-toolbox--dragging)
			&
			.znpb-element-toolbox__resize-value {
				min-height: 10px;
			}
		}

		&--paddingBottom {
			bottom: 0;
			left: 0;
			width: 100%;
			cursor: n-resize;

			& .znpb-element-toolbox__resize-value {
				bottom: 0;
				left: 0;
				width: 100%;
			}

			.znpb-element-toolbox:not(.znpb-element-toolbox--dragging)
			&
			.znpb-element-toolbox__resize-value {
				min-height: 10px;
			}
		}

		&--paddingRight {
			top: 0;
			right: 0;
			height: 100%;
			cursor: e-resize;

			& .znpb-element-toolbox__resize-value {
				top: 0;
				right: 0;
				height: 100%;
			}

			.znpb-element-toolbox:not(.znpb-element-toolbox--dragging)
			&
			.znpb-element-toolbox__resize-value {
				min-width: 10px;
			}
		}

		&--paddingLeft {
			top: 0;
			left: 0;
			height: 100%;
			cursor: e-resize;

			& .znpb-element-toolbox__resize-value {
				top: 0;
				left: 0;
				height: 100%;
			}
			.znpb-element-toolbox:not(.znpb-element-toolbox--dragging)
			&
			.znpb-element-toolbox__resize-value {
				min-width: 10px;
			}
		}

		&--marginTop {
			bottom: 100%;
			left: 0;
			width: 100%;
			cursor: n-resize;

			& .znpb-element-toolbox__resize-value {
				top: 0;
				left: 0;
				width: 100%;
			}

			.znpb-element-toolbox:not(.znpb-element-toolbox--dragging)
			&
			.znpb-element-toolbox__resize-value {
				min-height: 10px;
			}
		}

		&--marginBottom {
			top: 100%;
			left: 0;
			width: 100%;
			cursor: n-resize;

			& .znpb-element-toolbox__resize-value {
				top: 0;
				left: 0;
				width: 100%;
			}

			.znpb-element-toolbox:not(.znpb-element-toolbox--dragging)
			&
			.znpb-element-toolbox__resize-value {
				min-height: 10px;
			}
		}

		&--marginRight {
			top: 0;
			left: 100%;
			height: 100%;
			cursor: e-resize;

			& .znpb-element-toolbox__resize-value {
				top: 0;
				right: 0;
				height: 100%;
			}

			.znpb-element-toolbox:not(.znpb-element-toolbox--dragging)
			&
			.znpb-element-toolbox__resize-value {
				min-width: 10px;
			}
		}

		&--marginLeft {
			top: 0;
			right: 100%;
			height: 100%;
			cursor: e-resize;

			& .znpb-element-toolbox__resize-value {
				top: 0;
				left: 0;
				height: 100%;
			}
			.znpb-element-toolbox:not(.znpb-element-toolbox--dragging)
			&
			.znpb-element-toolbox__resize-value {
				min-width: 10px;
			}
		}

		&-padding {
			& .znpb-element-toolbox__resize-value {
				display: flex;
				justify-content: center;
				align-items: center;
				color: #06bee1;
				text-decoration: none;
				text-shadow: none;
				text-transform: none;
				background-color: rgba(6, 190, 225, .35);
			}

			.znpb-element-toolbox:not(.znpb-element-toolbox--dragging) &:hover, &-Top--dragging &--top, &-Bottom--dragging &--bottom, &-Right--dragging &--right, &-Left--dragging &--left {
				z-index: 1000;
				opacity: 1;
				visibility: visible;
			}
		}

		&-margin {
			& .znpb-element-toolbox__resize-value {
				display: flex;
				justify-content: center;
				align-items: center;
				color: #f9952d;
				background-color: rgba(249, 149, 45, .35);
			}

			.znpb-element-toolbox:not(.znpb-element-toolbox--dragging) &:hover, &-Top--dragging &--top, &-Bottom--dragging &--bottom, &-Right--dragging &--right, &-Left--dragging &--left {
				z-index: 1;
				opacity: 1;
				visibility: visible;
			}
		}
	}

	&__add-element-button {
		position: absolute;
		top: 100%;
		right: 0;
		bottom: 0;
		left: 50%;
		z-index: 1001;
		width: 34px;
		height: 34px;
		color: $surface;
		font-size: 14px;
		line-height: 1 !important;
		transform: translate(-50%, -50%);
		transition: all .2s;
		cursor: pointer;
		pointer-events: auto;

		& svg {
			position: relative;
			pointer-events: none;
		}

		&:before {
			@extend %iconbg;

			.znpb-element__wrapper > .znpb-element-toolbox & {
				background-color: $elements-toolbox-color;
			}

			.zb-column > .znpb-element-toolbox & {
				background-color: $column-color;
			}

			.zb-section > .znpb-element-toolbox & {
				background-color: $section-color;
			}
		}
		.znpb-editor-icon-wrapper {
			position: relative;
			width: 34px;
			height: 34px;
		}
		&--section {
			position: absolute;
			top: 100%;
			right: 0;
			bottom: 0;
			left: 50%;
			width: 34px;
			height: 34px;
			transform: translate(-50%, -50%);
			animation: AddCol ease-in-out .2s;
			transition: all .2s;
		}
		&:hover {
			&:before {
				transform: scale(1.1);
			}
		}
	}
}

.znpb-sizing-label {
	padding: 4px;
	color: $surface-active-color;
	font-size: 11px;
	font-weight: 700;
	line-height: 1;
}

.bounce-add-icon-enter-from {
	transform: translate(-50%, -50%) scale(.9);
}
.bounce-add-icon-enter-to {
	transform: translate(-50%, -50%) scale(1);
}
.bounce-add-icon-leave-from {
	transform: translate(-50%, -50%) scale(.5);
}
.bounce-add-icon-leave-to {
	transform: scale(0);
}
.bounce-add-icon-enter-to, .bounce-add-icon-leave-from {
	transition: all .2s;
}
</style>
