<template>
	<div class="znpb-optSpacing">
		<div
			class="znpb-optSpacing-margin"
			:class="
				{
					'znpb-optSpacing--linked': linkedMargin,
					'znpb-optSpacing--hover': activeHover && activeHover.position.indexOf('margin') !== -1,
					[`znpb-optSpacing--hover-${activeHover ? activeHover.position : ''}`]: activeHover,
				}
			"
		>
			<div
				v-for="position in marginPositions"
				:key="position.position"
				:class="{
					[`znpb-optSpacing-${position.position}`]: true
				}"
				class="znpb-optSpacing-value znpb-optSpacing-value--margin"
				@mouseenter="activeHover = position"
				@mouseleave="activeHover = null"
				@mousedown="startDragging($event, position)"
				@click="activePopup = position"
			>
				<input
					type="text"
					placeholder="-"
					:value="computedValues[position.position]"
					readonly
				>
			</div>
			<div class="znpb-optSpacing-labelWrapper">
				<span class="znpb-optSpacing-label">{{$translate('margin')}}</span>
				<Icon
					:icon="linkedMargin ? 'link' : 'unlink'"
					:title="linkedMargin ? $translate('unlink') : $translate('link')"
					:size="12"
					class="znpb-optSpacing-link"
					:class="{
						'znpb-optSpacing-link--linked': linkedMargin
					}"
					@click="linkValues('margin')"
				></Icon>
			</div>
			<div class="znpb-optSpacing-svg">
				<svg
					xmlns="http://www.w3.org/2000/svg"
					fill="none"
					viewBox="0 0 320 186"
				>
					<path
						v-for="position in marginPositions"
						:key="position.position"
						:cursor="position.svg.cursor"
						:fill="position.svg.fill"
						:d="position.svg.d"
						@mouseenter="activeHover = position"
						@mouseleave="activeHover = null"
						:class="{
							[`znpb-optSpacing--path-${position.position}`]: true
						}"
						@mousedown="startDragging($event, position)"
					/>
				</svg>
			</div>
		</div>
		<div
			class="znpb-optSpacing-padding"
			:class="
				{
					'znpb-optSpacing--linked': linkedPadding,
					'znpb-optSpacing--hover': activeHover && activeHover.position.indexOf('padding') !== -1,
					[`znpb-optSpacing--hover-${activeHover ? activeHover.position : ''}`]: activeHover,
				}
			"
		>
			<div
				v-for="position in paddingPositions"
				:key="position.position"
				:class="{
					[`znpb-optSpacing-${position.position}`]: true
				}"
				class="znpb-optSpacing-value znpb-optSpacing-value--padding"
				@mouseenter="activeHover = position"
				@mouseleave="activeHover = null"
				@mousedown="startDragging($event, position)"
				@click="activePopup = position"
			>
				<input
					type="text"
					placeholder="-"
					:value="computedValues[position.position]"
					readonly
				>
			</div>

			<div class="znpb-optSpacing-labelWrapper">
				<span class="znpb-optSpacing-label">{{$translate('padding')}}</span>
				<Icon
					:icon="linkedPadding ? 'link' : 'unlink'"
					:title="linkedPadding ? $translate('unlink') : $translate('link')"
					:size="12"
					class="znpb-optSpacing-link"
					:class="{
						'znpb-optSpacing-link--linked': linkedPadding
					}"
					@click="linkValues('padding')"
				></Icon>
			</div>
			<div class="znpb-optSpacing-svg">
				<svg
					xmlns="http://www.w3.org/2000/svg"
					fill="none"
					viewBox="0 0 214 108"
				>
					<path
						v-for="position in paddingPositions"
						:key="position.position"
						:cursor="position.svg.cursor"
						:fill="position.svg.fill"
						:d="position.svg.d"
						@mouseenter="activeHover = position"
						@mouseleave="activeHover = null"
						@mousedown="startDragging($event, position)"
						:class="{
							[`znpb-optSpacing--path-${position.position}`]: true
						}"
					/>
				</svg>
			</div>
		</div>
		<span
			class="znpb-optSpacing-info"
			v-if="activeHover"
		>{{activeHover.title}}</span>

		<div
			class="znpb-optSpacing-popup"
			v-if="activePopup"
		>
			<div
				class="znpb-optSpacing-popupInner"
				v-click-outside="() => activePopup = false"
			>
				<div class="znpb-optSpacing-popup__input-title">
					{{activePopup.title}}

					<ChangesBullet
						v-if="hasChanges"
						:content="$translate('discard_changes')"
						@remove-styles="onDiscardChanges"
					/>
				</div>
				<Icon
					icon="close"
					class="znpb-optSpacing-popupClose"
					@click.stop="activePopup = null"
				/>

				<InputNumberUnit
					v-model="inputValue"
					:units="['px', 'rem', 'pt', 'vh', '%']"
					:step="1"
					default-unit="px"
					ref="popupInput"
				/>
			</div>
		</div>
	</div>
</template>

<script>
import { computed, ref, watch, nextTick } from 'vue'
import { translate } from '@zb/i18n'
import rafSchd from 'raf-schd'
import clickOutside from '@zionbuilder/click-outside-directive'


export default {
	name: 'InputSpacing',
	props: {
		modelValue: {
			type: Object,
			default: {}
		}
	},
	directives: {
		clickOutside
	},
	setup (props, { emit }) {
		const marginPositions = [
			{
				position: 'margin-top',
				type: 'margin',
				title: translate('margin-top'),
				svg: {
					cursor: 's-resize',
					d: 'M0 0h320l-50 36H50L0 0Z'
				},
				dragDirection: 'vertical'
			},
			{
				position: 'margin-right',
				type: 'margin',
				title: translate('margin-right'),
				svg: {
					cursor: 'w-resize',
					d: 'm320 183-50-36V39l50-36v180Z'
				},
				dragDirection: 'horizontal'
			},
			{
				position: 'margin-bottom',
				type: 'margin',
				title: translate('margin-bottom'),
				svg: {
					cursor: 's-resize',
					d: 'M50 150h220l50 36H0l50-36Z'
				},
				dragDirection: 'vertical'
			},
			{
				position: 'margin-left',
				type: 'margin',
				title: translate('margin-left'),
				svg: {
					cursor: 'w-resize',
					d: 'm0 3 50 36v108L0 183V3Z'
				},
				dragDirection: 'horizontal'
			},
		]
		const paddingPositions = [
			{
				position: 'padding-top',
				type: 'padding',
				title: translate('padding-top'),
				svg: {
					cursor: 's-resize',
					d: 'M0 0h214l-50 36H50L0 0Z'
				},
				dragDirection: 'vertical'
			},
			{
				position: 'padding-right',
				type: 'padding',
				title: translate('padding-right'),
				svg: {
					cursor: 'w-resize',
					d: 'm214 105-50-36V39l50-36v102Z'
				},
				dragDirection: 'horizontal'
			},
			{
				position: 'padding-bottom',
				type: 'padding',
				title: translate('padding-bottom'),
				svg: {
					cursor: 's-resize',
					d: 'M214 108H0l50-36h114l50 36Z'
				},
				dragDirection: 'vertical'
			},
			{
				position: 'padding-left',
				type: 'padding',
				title: translate('padding-left'),
				svg: {
					cursor: 'w-resize',
					d: 'm0 3 50 36v30L0 105V3Z'
				},
				dragDirection: 'horizontal'
			}
		]
		const allowedValues = [
			...marginPositions,
			...paddingPositions
		].map(position => position.position)

		/**
		 * Refs
		 */
		const activeHover = ref(null)
		const activePopup = ref(null)
		const lastChanged = ref(null)
		const popupInput = ref(null)

		const hasChanges = computed(() => {
			if (activePopup.value) {
				const { position } = activePopup.value

				return typeof props.modelValue[position] !== 'undefined'
			}

			return false
		})

		function onDiscardChanges () {
			if (activePopup.value) {
				const { position } = activePopup.value
				const clonedModelValue = { ...props.modelValue }
				delete clonedModelValue[position]

				emit('update:modelValue', clonedModelValue)
			}
		}

		const computedValues = computed({
			get () {
				const values = {}

				Object.keys(props.modelValue).forEach(optionId => {
					if (allowedValues.includes(optionId)) {
						values[optionId] = props.modelValue[optionId]
					}
				})

				return values
			}, set (newValues) {
				emit('update:modelValue', newValues)
			}
		})

		const inputValue = computed({
			get () {
				return computedValues.value[activePopup.value.position]
			},
			set (newValue) {
				onValueUpdated(activePopup.value.position, activePopup.value.type, newValue)
			}
		})

		function onValueUpdated (sizePosition, type, newValue) {
			const isLinked = type === 'margin' ? linkedMargin : linkedPadding

			// Keep a track of the last changed position so we can use it for linking values
			lastChanged.value = {
				position: sizePosition,
				type: type,
			}

			if (isLinked.value) {
				const valuesToUpdate = type === 'margin' ? marginPositions : paddingPositions
				const updatedValues = {}

				valuesToUpdate.forEach(position => updatedValues[position.position] = newValue)

				computedValues.value = {
					...props.modelValue,
					...updatedValues
				}
			} else {
				computedValues.value = {
					...props.modelValue,
					[sizePosition]: newValue
				}
			}
		}

		/**
		 * Values linking
		 * - If a value was manually changed, change all the values to this value
		 * - If no value was changed, try to find a modified value and apply it to all
		 */
		const linkedMargin = ref(isLinked('margin'))
		const linkedPadding = ref(isLinked('padding'))
		function linkValues (type) {
			const valueToChange = type === 'margin' ? linkedMargin : linkedPadding
			valueToChange.value = !valueToChange.value

			// Set the same value for all
			if (valueToChange.value) {
				if (lastChanged.value && lastChanged.value.type === type) {
					onValueUpdated(valueToChange.value, type, computedValues.value[lastChanged.value.position])
				} else {
					// Find a position that has a saved value
					const valuesToCheck = type === 'margin' ? marginPositions : paddingPositions
					const savedValueConfig = valuesToCheck.find(positionConfig => computedValues.value[positionConfig.position] !== 'undefined')

					if (savedValueConfig) {
						onValueUpdated(savedValueConfig.position, type, computedValues.value[savedValueConfig.position])
					}
				}
			}
		}

		function isLinked (type) {
			const valuesToCheck = type === 'margin' ? marginPositions : paddingPositions

			return valuesToCheck.every(position => {
				return computedValues.value[position.position] && computedValues.value[position.position] === computedValues.value[`${type}-top`]
			})
		}

		/**
		 * Dragging
		 */
		let startMousePosition = null
		let dragDirection = null
		let draggingConfig = null
		let initialValue = null
		const dragTreshold = 0

		const validUnits = [
			{
				type: 'px',
				isModifiable: true
			},
			{
				type: '%',
				isModifiable: true
			},
			{
				type: 'vw',
				isModifiable: true
			},
			{
				type: 'vh',
				isModifiable: true
			},
			{
				type: 'rem',
				isModifiable: true
			},
			{
				type: 'em',
				isModifiable: true
			},
			{
				type: 'pt',
				isModifiable: true
			},
			{
				type: 'auto',
				isModifiable: false
			},
			{
				type: 'initial',
				isModifiable: false
			},
			{
				type: 'unset',
				isModifiable: false
			},
		]

		// refs
		const isDragging = ref(false)
		const draggingType = ref(null)

		function startDragging (event, positionConfig) {
			const { clientY, clientX } = event

			// Save the initial values
			startMousePosition = {
				clientY,
				clientX
			}

			// Set the drag direction
			dragDirection = positionConfig.dragDirection

			const { position, type } = positionConfig

			// prevent selection
			document.body.style.userSelect = 'none'

			initialValue = getSplitValue(position)
			const { unit = null } = initialValue
			const validUnit = validUnits.find(singleUnit => singleUnit.type === unit)

			if (validUnit && validUnit.isModifiable) {
				const linkType = type === 'margin' ? linkedMargin : linkedPadding

				// Set initial value
				draggingConfig = {
					positionConfig,
					position,
					type,
					initialValue,

					// Link status
					activeLinkStatus: linkType.value,
					activeLinkComputedValue: linkType
				}

				window.addEventListener('mousemove', rafDragValue)
				window.addEventListener('mouseup', rafDeactivateDragging)
				window.addEventListener('keydown', onKeyDown)
				window.addEventListener('keyup', onKeyUp)
			}
		}

		function getSplitValue (position) {
			const savedValue = computedValues.value[position] ? computedValues.value[position] : '0px'
			const splitValue = savedValue.match(/^([+-]?(?:\d+|\d*\.\d+))([a-z]*|%)$/)

			if (!splitValue) {
				return false
			}

			return {
				value: parseInt(splitValue[1]),
				unit: splitValue[2],
			}
		}

		function onKeyDown (event) {
			if (isDragging.value) {
				const { activeLinkStatus, activeLinkComputedValue } = draggingConfig

				if (!activeLinkStatus && event.which === 17) {
					activeLinkComputedValue.value = true
				} else {
					activeLinkComputedValue.value = activeLinkStatus
				}

			}
		}

		function onKeyUp (event) {
			if (isDragging.value) {
				const { activeLinkComputedValue } = draggingConfig

				if (event.which === 17) {
					activeLinkComputedValue.value = false
				}
			}
		}

		function deactivateDragging () {
			// Save the link status
			const { type, activeLinkComputedValue } = draggingConfig

			// activeLinkComputedValue.value = isLinked(type)

			document.body.style.userSelect = null
			document.body.style.pointerEvents = null

			// cancel dragging raf
			rafDragValue.cancel()

			// Remove events
			window.removeEventListener('mousemove', rafDragValue)
			window.removeEventListener('mouseup', rafDeactivateDragging)


			isDragging.value = false
			startMousePosition = null
			initialValue = null
			draggingConfig = false
		}

		function dragValue (event) {
			const { clientX, clientY } = event
			document.body.style.pointerEvents = 'none'

			const movedAmmount = dragDirection === 'vertical' ? Math.ceil(startMousePosition.clientY - clientY) : Math.ceil(startMousePosition.clientX - clientX) * -1

			if (Math.abs(movedAmmount) > dragTreshold) {
				const { positionConfig } = draggingConfig
				isDragging.value = true
				activeHover.value = positionConfig


				setDraggingValue(movedAmmount - dragTreshold, event)
			}
		}

		function setDraggingValue (newValue, event) {
			const { position, type, initialValue = {} } = draggingConfig
			const { value, unit } = initialValue

			// const updatedValue = event.shiftKey ? initialValue + increment).toFixed(12) : newValue + value
			let updatedValue = newValue + value

			// Check if the
			if (event.shiftKey) {
				updatedValue = Math.round(updatedValue / 5) * 5
			}


			const valueToUpdate = `${updatedValue}${unit}`

			// Update the value
			onValueUpdated(position, type, valueToUpdate)
		}

		const rafDragValue = rafSchd(dragValue)
		const rafDeactivateDragging = rafSchd(deactivateDragging)

		// highlight input when opening
		watch(activePopup, (newValue) => {
			if (newValue) {
				// Allow closing the popup by pressing esc key
				document.addEventListener('keydown', closeOnEscape)

				nextTick(() => {
					popupInput.value.$refs.numberUnitInput.$refs.input.focus()
				})
			} else {
				document.removeEventListener('keydown', closeOnEscape)
			}
		})

		function closeOnEscape (event) {
			if (event.which === 27) {
				activePopup.value = false

				event.stopPropagation()
			}
		}

		return {
			// Normal vars
			marginPositions,
			paddingPositions,

			// Refs
			activeHover,
			activePopup,
			linkedMargin,
			linkedPadding,
			popupInput,

			// Computed
			computedValues,
			inputValue,
			isDragging,
			draggingType,
			hasChanges,

			// methods
			onValueUpdated,
			linkValues,
			startDragging,
			onDiscardChanges
		}
	}
}
</script>

<style lang="scss">
.znpb-optSpacing {
	position: relative;
	width: 320px;
	height: 186px;
	margin: 0 auto;
	outline-style: none;
	user-select: none;

	&-margin {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;

		justify-items: center;
	}

	&-padding {
		position: absolute;
		top: 39px;
		left: 53px;
		width: 214px;
		height: 108px;

		justify-items: center;
	}

	&-value {
		position: absolute;
		z-index: 1;
		padding: 2px;
		color: var(--zb-surface-text-color);
		font-size: 11px;
		font-weight: 500;
		line-height: 1;
		text-overflow: ellipsis;
		white-space: nowrap;
		cursor: pointer;
		user-select: none;

		& input {
			max-width: 40px;
			color: var(--zb-surface-text-color);
			font-size: 11px;
			font-weight: bold;
			text-align: center;
			background: transparent;
			border: 0;
			cursor: pointer;

			&:focus {
				outline: none;
			}
		}
	}

	&-margin-top, &-padding-top, &-margin-bottom, &-padding-bottom {
		left: 50%;
		transform: translateX(-50%);
	}

	&-margin-top, &-padding-top {
		top: 8px;
	}

	&-margin-bottom, &-padding-bottom {
		bottom: 8px;
	}

	&-margin-left, &-padding-left, &-margin-right, &-padding-right {
		top: 50%;
		transform: translateY(-50%);
	}

	&-margin-left, &-padding-left {
		left: 5px;
	}

	&-margin-right, &-padding-right {
		right: 5px;
	}

	&-popup {
		position: absolute;
		top: 0;
		left: 0;
		z-index: 1;
		display: flex;
		justify-content: center;
		align-items: center;
		width: 100%;
		height: 100%;

		&::before {
			content: "";
			position: absolute;
			top: -40px;
			right: -54px;
			bottom: -40px;
			left: -54px;
			z-index: -1;
			background: var(--zb-surface-color);
			opacity: .5;
		}

		&Inner {
			position: relative;
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
			    flex-direction: column;
			justify-content: center;
			width: 214px;
			height: 108px;
			padding: 16px;
			background: var(--zb-surface-color);
			box-shadow: var(--zb-dropdown-shadow);
			border: 1px solid var(--zb-dropdown-border-color);
			border-radius: 4px;

			-webkit-box-direction: normal;
			-webkit-box-orient: vertical;
			-webkit-box-pack: center;
			-ms-flex-direction: column;
			-ms-flex-pack: center;
		}

		&Close {
			position: absolute;
			top: 10px;
			right: 10px;
			color: var(--zb-surface-icon-color);
			transition: color .1s;
			cursor: pointer;

			&:hover {
				color: var(--zb-surface-icon-active-color);
			}

			& svg {
				font-size: 10px;
			}
		}

		&__input-title {
			display: flex;
			align-items: center;
			margin-bottom: 10px;
			color: var(--zb-surface-text-hover-color);
			font-family: var(--zb-font-stack);
			font-size: 13px;
			font-weight: 500;
			line-height: 14px;
		}
	}

	&-info {
		position: absolute;
		top: 50%;
		left: 50%;
		z-index: 1;
		display: flex;
		justify-content: center;
		align-items: center;
		width: 108px;
		height: 30px;
		margin: -15px 0 0 -54px;
		color: #858585;
		font-size: 10px;
		font-weight: bold;
		line-height: 1;
		text-transform: uppercase;
	}

	&-labelWrapper {
		position: absolute;
		top: 4px;
		left: 24px;
		z-index: 1;
		display: -webkit-box;
		display: -ms-flexbox;
		display: flex;
		align-items: center;
		pointer-events: none;

		-webkit-box-align: center;
		-ms-flex-align: center;
	}

	&-label {
		color: #686868;
		font-size: 9px;
		font-weight: bold;
		line-height: 1;
		text-transform: uppercase;
		pointer-events: none;
	}

	&-link {
		margin-left: 4px;
		color: #858585;
		font-size: 12px;
		cursor: pointer;
		pointer-events: all;

		&--linked {
			color: var(--zb-secondary-color);
		}

		& svg {
			display: block;
			width: 1em;
			height: 1em;

			fill: currentColor;
		}
	}

	&-svg {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;

		& svg {
			display: block;
			width: 100%;
			height: 100%;

			& path {
				transition: fill .1s;

				&:nth-child(odd) {
					fill: #ebebeb;
				}

				&:nth-child(even) {
					fill: #f1f1f1;
				}

				&:hover {
					fill: #e6e6e6;
				}
			}
		}

		@at-root .znpb-theme-dark .znpb-optSpacing-svg svg {
			& path {
				&:nth-child(odd) {
					fill: #2f2f34;
				}

				&:nth-child(even) {
					fill: #35353a;
				}
			}
		}

		@at-root .znpb-theme-dark {
			// Margin
			.znpb-optSpacing--hover-margin-top
			.znpb-optSpacing-svg
			svg
			.znpb-optSpacing--path-margin-top, .znpb-optSpacing--hover-margin-left
			.znpb-optSpacing-svg
			svg
			.znpb-optSpacing--path-margin-left, .znpb-optSpacing--hover-margin-bottom
			.znpb-optSpacing-svg
			svg
			.znpb-optSpacing--path-margin-bottom, .znpb-optSpacing--hover-margin-right
			.znpb-optSpacing-svg
			svg
			.znpb-optSpacing--path-margin-right, // Paddings
			.znpb-optSpacing--hover-padding-top
			.znpb-optSpacing-svg
			svg
			.znpb-optSpacing--path-padding-top, .znpb-optSpacing--hover-padding-left
			.znpb-optSpacing-svg
			svg
			.znpb-optSpacing--path-padding-left, .znpb-optSpacing--hover-padding-bottom
			.znpb-optSpacing-svg
			svg
			.znpb-optSpacing--path-padding-bottom, .znpb-optSpacing--hover-padding-right
			.znpb-optSpacing-svg
			svg
			.znpb-optSpacing--path-padding-right, // Hovered states
			.znpb-optSpacing--linked.znpb-optSpacing--hover
			.znpb-optSpacing-svg
			svg
			path {
				fill: #3a3a3e;
			}
		}
	}
}
</style>