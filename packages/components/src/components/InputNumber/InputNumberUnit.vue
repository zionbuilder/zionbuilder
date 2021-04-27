<template>
	<div
		class="znpb-input-number-unit"
		:title="$attrs.title"
	>
		<BaseInput
			v-model="stringValueModel"
			class="znpb-input-number--has-units"
			size="narrow"
			@mousedown="actNumberDrag"
			@touchstart.prevent.passive="actNumberDrag"
			@mouseup="deactivatedragNumber"
			:placeholder="placeholder"
			ref="numberUnitInput"
			@keydown="onKeyDown"
		>
			<template v-slot:suffix>
				<Icon
					class="znpb-input-number__dots"
					@click="expanded = !expanded"
					icon="three-dots"
				/>
			</template>

		</BaseInput>

		<div
			class="znpb-input-number__units"
			:class="{'znpb-input-number__units-multiple': multipleUnits}"
		>

			<Tooltip
				:trigger="null"
				:show="expanded"
				placement="bottom-end"
				append-to="element"
				:show-arrows="false"
				strategy="fixed"
				tooltip-class="znpb-input-number__units-tooltip-wrapper"
			>
				<template v-slot:content>
					<div class="znpb-number-unit-list hg-popper-list">
						<div
							class="znpb-number-unit-list__option hg-popper-list__item"
							@click.stop="onSelectUnit(null)"
							:class="{[`znpb-number-unit-list__option--selected`]: isCustomUnit}"
							v-if="allow_custom"
						>
							custom
						</div>
						<div
							v-for="(availableUnit, i) in units"
							v-bind:key="i"
							@click.stop="onSelectUnit(availableUnit)"
							class="znpb-number-unit-list__option hg-popper-list__item"
							:class="{[`znpb-number-unit-list__option--selected`]: (valueUnit.unit === availableUnit || unit === availableUnit) && (isValidUnit || units.includes(stringValueModel) || unit === availableUnit)}"
						>
							{{availableUnit}}
						</div>
					</div>
				</template>
			</Tooltip>
		</div>
	</div>

</template>

<script>
/**
 * this type of element supports
 * required properties received
 * 		options - object
 * 			options.unit - array or string *
 *
 * Example of props:
 * options:{
			min: 12,
		max: 50,
		step: 1,
		units: ['px','%','rem']
	}
 */
import { Tooltip } from '@zionbuilder/tooltip'
import { units as stringUnits } from '../../composables/units'
import BaseInput from '../BaseInput/BaseInput.vue'
import rafSchd from 'raf-schd'
import { Icon } from '../Icon'

export default {
	name: 'InputNumberUnit',
	components: {
		Tooltip,
		BaseInput,
		Icon
	},
	props: {
		/**
		 * Input value
		 */
		modelValue: {
			type: String,
			required: false,
			default () {
				return null
			}
		},
		/**
		 * Min value allowed
		 */
		min: {
			type: Number,
			required: false
		},
		/**
		 * Max value allowed
		 */
		max: {
			type: Number,
			required: false
		},
		/**
		 * Step
		 */
		step: {
			type: Number,
			required: false,
			default: 1
		},
		/**
		 * Units for the number
		 */
		units: {
			type: Array || String,
			required: true
		},
		allow_custom: {
			type: Boolean,
			required: false,
			default: true
		},
		shift_step: {
			type: Number,
			required: false,
			default: 5
		}
	},
	data () {
		return {
			expanded: false,
			mouseDownPositionTop: 0,
			draggingPositionTop: 0,
			dragTreshold: 0,
			direction: null,
			shiftDrag: false,
			toTop: false,
			directionReset: 0,
			unit: this.units[0],
			cachedValue: null,
			draggingCached: null,
			dragging: false
		}
	},
	computed: {
		isCustomUnit () {
			return !this.isValidUnit && !this.units.includes(this.stringValueModel) && !this.units.includes(this.unit)
		},
		increment () {
			if (this.shiftDrag) {
				return this.toTop ? this.shift_step : -this.shift_step
			} else {
				return this.toTop ? this.step : -this.step
			}
		},
		placeholder () {
			if ((this.unit && this.isValidUnit) || this.units.includes(this.unit)) {
				return this.unit
			}
			return 'Insert value'
		},
		integerValue: {
			get () {
				return this.valueUnit.value !== null ? parseInt(this.valueUnit.value) : null
			},
			set (newValue) {
				this.valueUnit.value = `${newValue}`
			}
		},
		cursorPosition () {
			return this.modelValue && this.unit ? this.modelValue.length - this.unit.length : null
		},
		stringValueModel: {
			get () {
				return this.modelValue ? this.modelValue : null
			},
			set (newValue) {
				if (this.units.includes(newValue) || stringUnits.includes(newValue)) {
					this.unit = newValue
				}
				if (!this.modelValue && this.unit && this.unit !== 'custom' && this.units.includes(this.unit) && newValue && newValue.match(/^[0-9]*$/) !== null && !stringUnits.includes(this.unit)) {
					this.$emit('update:modelValue', `${newValue}${this.unit}`)
					this.$nextTick(() => {
						this.$refs.numberUnitInput.$refs.input.setSelectionRange(this.cursorPosition, this.cursorPosition)
					})
				} else {
					this.$emit('update:modelValue', newValue)
				}
			}
		},
		valueUnit: {
			get () {
				const match = typeof this.modelValue === 'string' && this.modelValue ? this.modelValue.match(/^([+-]?[0-9]+([.][0-9]*)?|[.][0-9]+)(\D+)$/) : null
				const value = match && match[1] ? match[1] : null
				const unit = match ? match[3] : null
				return {
					value,
					unit
				}
			},
			set (newValue) {
				if (newValue.value && newValue.unit) {
					this.stringValueModel = `${newValue.value}${newValue.unit}`
				}
			}
		},
		isValidUnit () {
			return this.units.includes(this.valueUnit.unit) || stringUnits.includes(this.unit)
		},
		multipleUnits () {
			return this.units.length
		},
		noValueUnit () {
			return stringUnits.includes(this.valueUnit.unit)
		}
	},
	watch: {
		expanded: function (newValue, oldValue) {
			if (newValue) {
				document.addEventListener('click', this.closePanel)
			} else {
				document.removeEventListener('click', this.closePanel)
			}
		},
		valueUnit (newValue) {
			if (newValue.value) {
				this.cachedValue = parseInt(newValue.value)
			}
			if (newValue.unit) {
				this.unit = newValue.unit
			}
		},
		isCustomUnit (newValue) {
			this.$emit('is-custom-unit', newValue)
		},
		unit (newValue) {
			this.$emit('unit-update', newValue)
		}
	},
	methods: {
		onSelectUnit (unit) {
			this.unit = unit
			if (unit) {
				if (this.cachedValue && this.units.includes(unit) && !stringUnits.includes(unit)) {
					this.stringValueModel = `${this.cachedValue}${unit}`
				}
				if (this.integerValue) {
					this.stringValueModel = `${this.integerValue}${unit}`
				}

				if (!this.integerValue && !stringUnits.includes(unit) && !this.cachedValue) {
					this.valueUnit.unit = unit
					this.stringValueModel = null
				}
				if (stringUnits.includes(unit)) {
					this.stringValueModel = unit
				}
			} else {
				if (unit !== 'custom') {
					unit = 'custom'
					this.stringValueModel = null
				}
			}

			this.expanded = false
			this.$emit('update:unit', unit)
			this.$refs.numberUnitInput.$refs.input.focus()
		},
		/**
		* Close panel if clicked outside of selector
		*/
		closePanel (event) {
			if (!this.$el.contains(event.target)) {
				this.expanded = false
			}
		},
		actNumberDrag (event) {
			this.dragging = true
			this.draggingCached = parseInt(this.valueUnit.value) || 0
			this.mouseDownPositionTop = event.clientY
			if (!stringUnits.includes(this.unit) && this.unit !== 'custom' && (this.valueUnit.unit || this.unit)) {
				this.dragNumberThrottle = rafSchd(this.dragNumber)
				document.body.style.userSelect = 'none'
				window.addEventListener('mousemove', this.dragNumberThrottle)
				window.addEventListener('mouseup', this.deactivatedragNumber)
			}
		},
		roundedByStep (step) {
			return step * Math.round(this.integerValue / step)
		},
		onKeyDown (event) {
			if (event.altKey) {
				event.preventDefault()
				this.$emit('linked-value')
			}
			if ((this.valueUnit.unit || this.unit) && !stringUnits.includes(this.unit)) {
				this.shiftDrag = event.shiftKey

				// Check if up/down arrow was presses
				if (event.keyCode === 38 || event.keyCode === 40) {
					this.toTop = event.keyCode === 38
					this.setDraggingValue()
					event.preventDefault()
				}
			}
		},
		deactivatedragNumber (event) {
			this.dragging = false
			document.body.style.userSelect = null
			document.body.style.pointerEvents = null
			window.removeEventListener('mousemove', this.dragNumberThrottle)
		},
		removeEvents () {
			this.deactivatedragNumber()
			this.dragNumberThrottle = null
			window.removeEventListener('mouseup', this.deactivatedragNumber)
		},
		dragNumber (event) {
			const pageY = event.pageY
			this.shiftDrag = event.shiftKey
			this.draggingPositionTop = event.clientY

			if (Math.abs(this.mouseDownPositionTop - this.draggingPositionTop) > this.dragTreshold) {
				if (pageY < this.directionReset) {
					this.toTop = true
				} else {
					this.toTop = false
				}

				document.body.style.pointerEvents = 'none'

				if (pageY !== this.directionReset) {
					this.setDraggingValue()
				}
			}

			this.directionReset = event.pageY
		},
		setDraggingValue () {
			let newValue
			if (this.dragging) {
				const dragged = this.mouseDownPositionTop - this.draggingPositionTop
				newValue = this.draggingCached + dragged

				if (this.shiftDrag) {
					newValue = newValue % this.shift_step ? Math.ceil(newValue / this.shift_step) * this.shift_step : newValue
				}
			} else {
				newValue = this.integerValue + this.increment
				if (this.shiftDrag) {
					newValue = newValue % this.shift_step ? Math.ceil(newValue / this.shift_step) * this.shift_step : newValue
					if (this.toTop && this.integerValue % this.shift_step !== 0) {
						newValue -= this.shift_step
					}
				}
			}
			// Check that value is in between min and max
			const value = newValue <= this.min ? this.min : newValue >= this.max ? this.max : newValue
			this.stringValueModel = `${value}${this.valueUnit.unit || this.unit}`
		},
		beforeUnmount () {
			this.removeEvents()
		},
		unmounted () {
			window.removeEventListener('mousemove', this.dragNumberThrottle)
		}
	}
}
</script>
<style lang="scss" >
.znpb-input-number__units-tooltip-wrapper {
	padding: 0;
}

.znpb-input-number-unit {
	position: relative;
	.znpb-editor-icon-wrapper.znpb-input-number__dots {
		position: absolute;
		top: 0;
		right: 0;
		display: flex;
		justify-content: center;
		align-items: center;
		flex-grow: 1;
		height: 100%;
		padding-right: 3px;
		font-size: 10px;
		cursor: pointer;
	}
	.zion-input {
		padding-right: 10px;
	}
	input {
		text-align: left;
	}

	&__units {
		position: relative;

		&-tooltip-wrapper {
			padding: 0;
		}
	}

	&--has-units {
		.znpb-input-number__units-multiple {
			color: $surface-headings-color;
			font-size: 11px;
			font-weight: 500;
			line-height: 1;
			cursor: pointer;
		}
	}
}
.znpb-number-unit-list {
	margin: 0;
	list-style-type: none;
	&__option {
		font-weight: 500;
		cursor: pointer;

		&:last-child {
			border-bottom: 0;
		}
		&:hover, &--selected {
			color: $surface-active-color;
		}
	}
}
.znpb-input-number__units--auto {
	padding: 7px;
	background-color: $surface-variant;
}
</style>
