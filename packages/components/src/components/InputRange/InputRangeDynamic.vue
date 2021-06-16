<template>
	<div
		class="znpb-input-range znpb-input-range--has-multiple-units"
		:class="{['znpb-input-range--disabled']: disabled}"
	>

		<BaseInput
			ref="rangebase"
			type="range"
			:min="activeOption.min"
			:max="activeOption.max"
			v-model="rangeModel"
			@keydown="onRangeKeydown"
			@keyup="onRangeKeyUp"
			:step="step"
			:disabled="disabled"
		>
			<template v-slot:suffix>
				<div
					class="znpb-input-range__trackwidth"
					:style="trackWidth"
				></div>
			</template>
		</BaseInput>
		<label class="znpb-input-range__label">
			<InputNumberUnit
				class="znpb-input-range-number"
				v-model="computedValue"
				:min="activeOption.min"
				:max="activeOption.max"
				:units="getUnits"
				:step="step"
				:shift_step="shiftStep"
				ref="InputNumberUnit"
				@is-custom-unit="onCustomUnit"
				@unit-update="onUnitUpdate"
			>
			</InputNumberUnit>
		</label>
	</div>
</template>

<script>
/**
 * this type of element supports
 *   model - Number
 */
import BaseInput from '../BaseInput/BaseInput.vue'
import { InputNumberUnit } from '../InputNumber'
import { units as stringUnits } from '../../composables/units'
import rafSchd from 'raf-schd'

export default {
	name: 'InputRangeDynamic',
	inheritAttrs: false,
	components: {
		BaseInput,
		InputNumberUnit
	},
	props: {
		/**
		 * Value/v-bind model
		 */
		modelValue: {
			type: String,
			required: false,
			default () {
				return null
			}
		},
		/**
		 * Array of Objects, each object containt unit, min, max, step, shiftstep
		 */
		options: {
			type: Array,
			required: true
		},
		default_step: {
			type: Number,
			default: 1,
			required: false
		},
		default_shift_step: {
			type: Number,
			default: 1,
			required: false
		},
		min: Number,
		max: Number
	},
	data () {
		return {
			step: 1,
			unit: '',
			customUnit: false
		}
	},
	created () {
		this.rafUpdateValue = rafSchd(this.updateValue)
	},
	computed: {
		activeOption () {
			return this.getActiveOption(this.valueUnit)
		},
		valueUnit: {
			get () {
				const match = typeof this.modelValue === 'string' ? this.modelValue.match(/^([+-]?[0-9]+([.][0-9]*)?|[.][0-9]+)(\D+)$/) : null
				const value = match && match[1] ? match[1] : null
				const unit = match ? match[3] : null

				return {
					value,
					unit
				}
			},
			set (newValue) {
				if (newValue.value && newValue.unit) {
					if (parseInt(newValue.value) > this.activeOption.max) {
						this.computedValue = `${this.activeOption.max}${newValue.unit}`
					} else if (parseInt(newValue.value) < this.activeOption.min) {
						this.computedValue = `${this.activeOption.min}${newValue.unit}`
					}
				}
			}
		},
		computedValue: {
			get () {
				return this.modelValue
			},
			set (newValue) {
				this.rafUpdateValue(newValue)
			}
		},
		rangeModel: {
			get () {
				if (this.disabled) {
					return 0
				} else {
					return this.valueUnit.value || this.min || 0
				}
			},
			set (newValue) {
				/**
				* Emit input value when range updates
				*/
				if (this.getUnit) {
					this.computedValue = `${newValue}${this.getUnit}`
				}
			}
		},
		getUnit () {
			return this.activeOption.unit ? this.activeOption.unit : this.valueUnit.unit ? this.valueUnit.unit : this.unit ? this.unit : null
		},
		getUnits () {
			/**
			* Array of units to send to InputNumberUnits
			*/
			let units = []
			this.options.forEach(function (option, index) {
				units.push(option.unit)
			})
			return units
		},
		baseStep () {
			return this.activeOption.step || this.default_step
		},
		shiftStep () {
			return this.activeOption.shift_step || this.default_shift_step
		},
		trackWidth () {
			// 14 is the thumb size
			let thumbSize = 14 * this.width / 100
			return {
				width: `calc(${this.width}% - ${thumbSize}px)`
			}
		},
		width () {
			let minmax = this.activeOption.max - this.activeOption.min
			return Math.round(((this.activeOption.value - this.activeOption.min) * 100) / minmax)
		},
		disabled () {
			const transformOriginUnits = ['left', 'right', 'top', 'bottom', 'center']
			return transformOriginUnits.includes(this.unit) || this.customUnit
		}
	},
	methods: {
		updateValue (newValue) {
			this.$emit('update:modelValue', newValue)

		},
		onUnitUpdate (event) {
			this.unit = event
		},
		onCustomUnit (event) {
			this.customUnit = event
		},
		getActiveOption (valueUnit) {
			let activeOption = null
			this.options.forEach((option, index) => {
				if (this.valueUnit && option.unit === this.valueUnit.unit) {
					activeOption = option
				}
			})
			return activeOption || this.options[0]
		},
		onRangeKeydown (event) {
			if (event.shiftKey) {
				this.step = this.shiftStep
			}
		},
		onRangeKeyUp (event) {
			if (event.key === 'Shift') {
				this.step = this.baseStep
			}
		}
	}
}
</script>
<style lang="scss">
.znpb-input-range {
	&--disabled {
		& > .zion-input {
			opacity: 0.5;
		}
	}
	&__label {
		.znpb-input-number--has-units
			.znpb-input-number__units-multiple
			.znpb-input-number__active-unit {
			background: $surface;
		}
	}
	&.znpb-input-range--has-multiple-units {
		input[type="number"] {
			padding: 12px 10px 12px 0;
			font-family: var(--font-stack);
		}
	}
}

.znpb-input-range--has-multiple-units {
	.znpb-input-range__label {
		.znpb-input-number {
			width: 85px;
			border-radius: 3px;
		}
		.znpb-input-number .zion-input__suffix {
			margin-right: 0;
			background: transparent;
		}
	}
}
.znpb-input-range-number {
	.znpb-input-number__units-multiple {
		margin-left: -7px;
	}
}
</style>
