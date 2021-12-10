<template>
	<div class="znpb-optSpacing">
		<div class="znpb-optSpacing-margin">
			<div
				v-for="position in marginPositions"
				:key="position.position"
				:class="{
					[`znpb-optSpacing-${position.position}`]: true
				}"
				class="znpb-optSpacing-value znpb-optSpacing-value--margin"
				@mouseenter="activeHover = position"
				@mouseleave="activeHover = null"
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
                    :title="linkedMargin ? 'Unlink' : 'Link'"
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
					/>
				</svg>
			</div>
		</div>
		<div class="znpb-optSpacing-padding">
			<div
				v-for="position in paddingPositions"
				:key="position.position"
				:class="{
					[`znpb-optSpacing-${position.position}`]: true
				}"
				class="znpb-optSpacing-value znpb-optSpacing-value--padding"
				@mouseenter="activeHover = position"
				@mouseleave="activeHover = null"
			>
				<input
					type="text"
					placeholder="-"
					:value="computedValues[position.position]"
					readonly
					@click="activePopup = position"
				>
			</div>

			<div class="znpb-optSpacing-labelWrapper">
				<span class="znpb-optSpacing-label">{{$translate('padding')}}</span>
				<Icon
                    :icon="linkedPadding ? 'link' : 'unlink'"
                    :title="linkedPadding ? 'Unlink' : 'Link'"
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
			<div class="znpb-optSpacing-popup__input-title">{{activePopup.title}}</div>
			<Icon
				icon="close"
				class="znpb-optSpacing-popupClose"
				@click.stop="activePopup = null"
			/>

			<InputNumberUnit
				v-model="inputValue"
				:min="0"
				:max="999"
				:units="['px', 'rem', 'pt', 'vh', '%']"
				:step="1"
				default-unit="px"
			/>
		</div>
	</div>
</template>

<script>
import { computed, ref } from 'vue'
import { translate } from '@zb/i18n'

export default {
	name: 'InputMarginPadding',
	props: {
		modelValue: {
			type: Object,
			default: {}
		}
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
				}
			},
			{
				position: 'margin-right',
				type: 'margin',
				title: translate('margin-right'),
				svg: {
					cursor: 'w-resize',
					d: 'm320 183-50-36V39l50-36v180Z'
				}
			},
			{
				position: 'margin-bottom',
				type: 'margin',
				title: translate('margin-bottom'),
				svg: {
					cursor: 's-resize',
					d: 'M50 150h220l50 36H0l50-36Z'
				}
			},
			{
				position: 'margin-left',
				type: 'margin',
				title: translate('margin-left'),
				svg: {
					cursor: 'w-resize',
					d: 'm0 3 50 36v108L0 183V3Z'
				}
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
				}
			},
			{
				position: 'padding-right',
				type: 'padding',
				title: translate('padding-right'),
				svg: {
					cursor: 'w-resize',
					d: 'm214 105-50-36V39l50-36v102Z'
				}
			},
			{
				position: 'padding-bottom',
				type: 'padding',
				title: translate('padding-bottom'),
				svg: {
					cursor: 's-resize',
					d: 'M214 108H0l50-36h114l50 36Z'
				}
			},
			{
				position: 'padding-left',
				type: 'padding',
				title: translate('padding-left'),
				svg: {
					cursor: 'w-resize',
					d: 'm0 3 50 36v30L0 105V3Z'
				}
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

		return {
			// Normal vars
			marginPositions,
			paddingPositions,

			// Refs
			activeHover,
			activePopup,
			linkedMargin,
			linkedPadding,

			// Computed
			computedValues,
			inputValue,

			// methods
			onValueUpdated,
			linkValues
		}
	}
}
</script>

<style lang="scss">
.znpb-optSpacing {
	display: grid;
	width: 320px;
	height: 186px;
	outline-style: none;
	user-select: none;

	grid-template-columns: 50px 3px 50px 1fr 50px 3px 50px;
	grid-template-rows: 36px 3px 36px 1fr 36px 3px 36px;

	&-margin {
		position: relative;
		display: grid;
		width: 320px;
		height: 186px;

		grid-area: 1 / 1 / -1 / -1;
		grid-template-columns: 50px 1fr 50px;
		grid-template-rows: 36px minmax(36px, 1fr) 36px;
		justify-items: center;
	}

	&-padding {
		position: relative;
		display: grid;
		width: 214px;
		height: 108px;

		grid-area: 3 / 3 / span 3 / span 3;
		grid-template-columns: 50px 1fr 50px;
		grid-template-rows: 36px minmax(36px, 1fr) 36px;
		justify-items: center;
	}

	&-value {
		position: relative;
		display: flex;
		justify-content: center;
		align-items: center;
		padding: 2px;
		color: #858585;
		font-size: 11px;
		font-weight: 500;
		line-height: 1;
		text-overflow: ellipsis;
		white-space: nowrap;
		cursor: pointer;
		user-select: none;

		place-self: center;

		& input {
			max-width: 40px;
			color: #858585;
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

	&-margin-top,
	&-padding-top {
		grid-area: 1 / 2 / 2 / 3;
	}

	&-margin-right,
	&-padding-right {
		grid-area: 2 / 3 / 3 / 4;
	}

	&-margin-bottom,
	&-padding-bottom {
		grid-area: 3 / 2 / 4 / 3;
	}

	&-margin-left,
	&-padding-left {
		grid-area: 2 / 1 / 3 / 2;
	}

	&-popup {
		position: relative;
		display: flex;
		flex-direction: column;
		justify-content: center;
		width: 214px;
		height: 108px;
		padding: 16px;
		background: var(--zb-surface-color);

		grid-area: 3 / 3 / span 3 / span 3;

		&Close {
			position: absolute;
			right: 10px;
			top: 10px;
			color: var(--zb-surface-icon-color);
			cursor: pointer;
			transition: color 0.1s;

			&:hover {
				color: var(--zb-surface-icon-active-color);
			}

			& svg {
				font-size: 10px;
			}
		}

		&__input-title {
			color: var(--zb-surface-text-hover-color);
			font-family: var(--zb-font-stack);
			font-size: 13px;
			font-weight: 500;
			line-height: 14px;
			margin-bottom: 10px;
		}
	}

	&-info {
		display: flex;
		justify-content: center;
		align-items: center;
		width: 108px;
		height: 30px;
		margin: 3px 0 0 3px;
		color: var(--zb-surface-text-muted-color);
		font-size: 10px;
		font-weight: bold;
		line-height: 1;
		text-transform: uppercase;

		grid-area: 4 / 4 / span 4 / span 4;
	}

	&-labelWrapper {
		position: absolute;
		top: 4px;
		left: 24px;
		display: flex;
		align-items: center;
		pointer-events: none;
	}

	&-label {
		color: #606060;
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
		grid-area: 1 / 1 / -1 / -1;

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
					fill: #2F2F34;
				}

				&:nth-child(even) {
					fill: #35353A;
				}

				&:hover {
					fill: #3e3e43;
				}
			}
		}
	}
}
</style>