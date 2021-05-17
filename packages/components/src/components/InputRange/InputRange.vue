<template>
	<div class="znpb-input-range">

		<BaseInput
			ref="rangebase"
			type="range"
			:min="min"
			:max="max"
			v-model="optionValue"
			@keydown="onKeydown"
			@keyup="onKeyUp"
			:step="localStep"
		>
			<template v-slot:suffix>
				<div
					class="znpb-input-range__trackwidth"
					:style="trackWidth"
				></div>
			</template>
		</BaseInput>
		<label class="znpb-input-range__label">
			<InputNumber
				class="znpb-input-range-number"
				v-model="optionValue"
				@keydown="onKeydown"
				@keyup="onKeyUp"
				:min="min"
				:max="max"
				:step="localStep"
				:shift_step="localStep"
			>
				<!-- @slot Content for units -->
				<slot />
			</InputNumber>

		</label>
	</div>
</template>

<script>
import BaseInput from '../BaseInput/BaseInput.vue'
import { InputNumber } from '../InputNumber'
/**
 * this type of element supports
 *   model - Number
 */
export default {
	name: 'InputRange',
	inheritAttrs: false,
	components: {
		BaseInput,
		InputNumber
	},
	props: {
		/**
		 * Value/v-bind model
		 */
		modelValue: {
			type: Number,
			required: false
		},
		/**
		 * Step when pressing shift key
		 */
		shiftStep: {
			type: Number,
			required: false,
			default: 10
		},
		/**
		 * Min value
		 */
		min: {
			type: Number,
			required: false,
			default: 0
		},
		/**
		 * Max value
		 */
		max: {
			type: Number,
			required: false,
			default: 100
		},
		/**
		 * Step
		 */
		step: {
			type: Number,
			required: false,
			default: 1
		}
	},
	data () {
		return {
			localStep: this.step
		}
	},
	computed: {

		baseStep () {
			return this.step
		},
		optionValue: {
			get () {
				return typeof this.modelValue !== 'undefined' ? this.modelValue : this.min || 0
			},
			set (newValue) {
				/**
				 * Emit input value when v-model
				 */
				this.$emit('update:modelValue', Number(newValue))
			}
		},
		trackWidth () {
			// 14 is the thumb size
			let thumbSize = 14 * this.width / 100

			return {
				width: `calc(${this.width}% - ${thumbSize}px)`
			}
		},
		width () {
			let minmax = this.max - this.min
			return Math.round(((this.modelValue - this.min) * 100) / minmax)
		}
	},
	methods: {
		onKeydown (event) {
			if (event.shiftKey) {
				this.localStep = this.shiftStep
			}
		},
		onKeyUp (event) {
			if (event.key === 'Shift') {
				this.localStep = this.baseStep
			}
		}
	}
}
</script>
<style lang="scss" >
.znpb-input-range {
	display: flex;
	align-items: center;
	width: 100%;

	input[type="range"]:focus {
		padding: 0;
		background: transparent;
	}

	& > .zion-input {
		align-items: center;
		border: none;
	}
	.zion-input, input[type="range"] {
		position: relative;
		flex: 2;
		width: 100%;
		padding: 0;
		background: transparent;
	}
	& > label > .znpb-input-number > .zion-input > input[type="number"] {
		height: auto;
		padding: 12px 10px 12px 10px;
		font-family: $font-stack;
	}

	input.znpb-input-number__input {
		width: 100%;
		height: 100%;
		min-height: 20px;
		padding: 0;
		margin: 0;
		cursor: pointer;
	}

	&__trackwidth {
		position: absolute;
		left: 0;
		width: 0;
		height: 2px;
		background-color: $secondary;
	}
	&__label {
		flex: 1;
		margin-left: 7px;
		color: $surface-headings-color;
		border-radius: 3px;
		.znpb-input-number .zion-input__suffix {
			font-size: 11px;
		}
	}

	// hide the initial slider
	input[type="range"] {
		border: none;

		-webkit-appearance: none;
	}
	input[type="range"]::-webkit-slider-thumb {
		-webkit-appearance: none;
	}
	input[type="range"]:focus {
		outline: none;
	}

	// style the slider
	/* Special styling for WebKit/Blink */
	input[type="range"]::-webkit-slider-thumb {
		z-index: 1;
		width: 18px;
		height: 18px;
		margin: -8px 0 0;
		background-color: $secondary;
		border-radius: 50%;
		cursor: pointer;
		&:active {
			transform: scale(1.1);
		}
	}

	/* for Firefox */
	input[type="range"]::-moz-range-thumb {
		width: 18px;
		height: 18px;
		background: $secondary;
		border-radius: 50%;
		transform: translate(0px, 0px);
		transition: all .2s ease;
		cursor: pointer;
	}

	/* All for IE */
	input[type="range"]::-ms-thumb {
		width: 18px;
		height: 18px;
		background-color: $surface;
		border: 2px solid $secondary;
		border-radius: 50%;
		transform: translate(-1px, 0px);
		cursor: pointer;
	}
	input[type="range"]:visited:-ms-thumb {
		transform: translate(0px, 0px);
	}
	input[type="range"]::-webkit-slider-runnable-track {
		width: 100%;
		height: 2px;
		margin: 0;
		background: $border-color;
		cursor: pointer;
	}

	input[type="range"]:focus::-webkit-slider-runnable-track {
		margin: 0;
		background: $border-color;
	}

	input[type="range"]::-moz-range-track {
		width: 100%;
		height: 2px;
		margin: 0;
		background: $border-color;
		cursor: pointer;
	}

	input[type="range"]::-ms-track {
		width: 100%;
		height: 2px;
		margin: 0;
		color: $surface-active-color;
		background: $surface-active-color;
		border-color: $surface-active-color;
		cursor: pointer;
	}
	input[type="range"]::-ms-fill-lower {
		border: 2px solid $secondary;
	}
	input[type="range"]::-ms-fill-upper {
		border: 2px solid $secondary;
	}
}

// style for wordpress backend
.znpb-input-range-number {
	input.znpb-input-number__input {
		padding: 0;
		padding: 0;
		background-color: transparent;
	}
	.znpb-input-number__units {
		margin-left: -10px;
	}
}
</style>
