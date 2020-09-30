<template>
	<div class="znpb-input-number">
		<BaseInput
			v-model="model"
			type="number"
			class="znpb-input-number__input"
			:min="min"
			:max="max"
			:step="shiftKey ? shift_step : step"
			v-bind="$attrs"
			@keydown="onKeyDown"
			@mousedown="actNumberDrag"
			@touchstart.prevent="actNumberDrag"
			@mouseup="deactivatedragNumber"
		>
			<!-- @slot Content that represents units -->
			<template v-slot:suffix>
				<slot></slot>
			</template>

		</BaseInput>
	</div>
</template>

<script>
import BaseInput from '../forms/elements/input/BaseInput.vue'

export default {
	name: 'InputNumber',
	inheritAttrs: false,
	components: {
		BaseInput
	},
	props: {
		/**
		 * Min value allowed
		 */
		min: {
			type: Number,
			required: false,
			default: null
		},
		/**
		 * Max value allowed
		 */
		max: {
			type: Number,
			required: false,
			default: null
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
		 * Step
		 */
		shift_step: {
			type: Number,
			required: false,
			default: 5
		},
		/**
		 * Input value
		 */
		modelValue: {
			type: Number,
			required: false
		},
		/**
		 * If arrows
		 */
		optionConfig: {
			type: Object,
			required: false
		}
	},
	data () {
		return {
			localValue: this.modelValue,
			mouseDownPosition: {
				left: 0,
				top: 0
			},
			draggingPosition: {
				left: 0,
				top: 0
			},
			dragTreshold: 3,
			directionSet: false,
			direction: null,
			shiftDrag: false,
			toTop: false,
			directionReset: 0,
			shiftDragHook: false,
			shiftKey: false
		}
	},

	computed: {
		model: {
			get () {
				return this.modelValue !== undefined ? this.modelValue : null
			},
			set (newValue) {
				// Check if minimum value is meet

				if (this.min !== null && newValue < this.min) {
					newValue = this.min
				}

				if (this.max !== null && (newValue > this.max)) {
					newValue = this.max
				}
				if (newValue !== this.model) {
					/**
					 * Emits new value number
					 */
					this.$emit('update:modelValue', Number(newValue))
				}
			}
		}
	},
	methods: {
		actNumberDrag (event) {
			this.mouseDownPosition.left = event.clientX
			this.mouseDownPosition.top = event.clientY
			this.directionSet = false
			this.shiftDrag = false
			this.shiftDragHook = false
			document.body.style.userSelect = 'none'
			window.addEventListener('mousemove', this.dragNumber)
			window.addEventListener('mouseup', this.deactivatedragNumber)
			window.addEventListener('keyup', this.onKeyUp)
		},
		onKeyDown (event) {
			if (event.altKey) {
				this.$emit('linked-value')
			}
			this.shiftKey = event.shiftKey
		},
		onKeyUp (event) {
			this.$emit('linked-value', false)
		},
		deactivatedragNumber (event) {
			document.body.style.userSelect = null
			document.body.style.pointerEvents = null
			window.removeEventListener('mousemove', this.dragNumber)
			window.removeEventListener('mouseup', this.deactivatedragNumber)
			window.removeEventListener('keyup', this.onKeyUp)
		},
		dragNumber (event) {
			this.draggingPosition.left = event.clientX
			this.draggingPosition.top = event.clientY
			if (Math.abs(this.mouseDownPosition.top - this.draggingPosition.top) > this.dragTreshold) {
				this.directionSet = true
				document.body.style.pointerEvents = 'none'
			}
			if (event.pageY < this.directionReset) {
				this.toTop = true
			} else {
				this.toTop = false
			}
			this.directionReset = event.pageY
			this.shiftDrag = event.shiftKey
			this.setDraggingValue(event)
		},
		setDraggingValue (event) {
			let draggingValue = this.modelValue
			if (this.directionSet) {
				let dragged = this.mouseDownPosition.top - this.draggingPosition.top
				const increment = this.toTop ? this.step : -this.step
				let shiftIncrement = this.toTop ? this.shift_step : -this.shift_step
				draggingValue = dragged % 2 === 0 ? (this.modelValue || 0) + increment : (this.modelValue || 0)

				if (!this.shiftDrag) {
					this.model = draggingValue
					this.shiftDragHook = false
				} else if (this.shiftDrag) {
					if (!this.shiftDragHook) {
						const divisibleValue = this.shift_step * Math.round(this.localValue / this.shift_step)
						this.model = divisibleValue

						this.shiftDragHook = event.shiftKey
					} else {
						this.model = dragged % this.shift_step === 0 ? (this.modelValue || 0) + shiftIncrement : (this.modelValue || 0)
					}
				}
				if (this.min && draggingValue <= this.min) {
					draggingValue = this.min
				}
				if (this.max && draggingValue >= this.max) {
					draggingValue = this.max
				}

				this.localValue = draggingValue
			}
		},
		beforeUnmount () {
			this.deactivatedragNumber()
		},
		unmounted () {
			window.removeEventListener('mousemove', this.dragNumber)
		}
	}
}
</script>
<style lang="scss" >
input[type="number"] {
	-moz-appearance: textfield;

	&::-webkit-inner-spin-button, &::-webkit-outer-spin-button {
		-webkit-appearance: none;
	}
	&:hover {
		cursor: ns-resize;
	}
}
.znpb-input-number {
	.zion-input__suffix {
		margin-right: 7px;
		color: $font-color;
	}
}
</style>
