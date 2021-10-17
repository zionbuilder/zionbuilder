<template>
	<div class="znpb-input-number">
		<BaseInput
			v-model="model"
			type="number"
			class="znpb-input-number__input"
			:min="min"
			:max="max"
			:step="shiftKey ? shift_step : step"
			@keydown="onKeyDown"
			@mousedown="actNumberDrag"
			@touchstart.prevent.passive="actNumberDrag"
			@mouseup="deactivatedragNumber"
		>
			<!-- @slot Content that represents units -->
			<template v-slot:suffix>
				<slot></slot>
				{{suffix}}
			</template>

		</BaseInput>
	</div>
</template>

<script>
import { computed, ref, onBeforeUnmount } from 'vue'
import BaseInput from '../BaseInput/BaseInput.vue'

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
		},
		suffix: {
			required: false
		},
	},
	setup (props, { emit }) {
		let shiftKey = ref(false)
		let initialPosition = 0
		let lastPosition = 0
		let dragTreshold = 3
		let canChangeValue = false

		const model = computed({
			get () {
				return props.modelValue
			},
			set (newValue) {
				// Check if minimum value is meet
				if (props.min !== null && newValue < props.min) {
					newValue = props.min
				}

				if (props.max !== null && (newValue > props.max)) {
					newValue = props.max
				}

				if (newValue !== props.model) {
					/**
					 * Emits new value number
					 */
					emit('update:modelValue', parseFloat(newValue))
				}
			}
		})

		function reset () {
			initialPosition = 0
			lastPosition = 0
			canChangeValue = false
		}

		function actNumberDrag (event) {
			initialPosition = event.clientY

			document.body.style.userSelect = 'none'

			window.addEventListener('mousemove', dragNumber)
			window.addEventListener('mouseup', deactivatedragNumber)
			window.addEventListener('keyup', onKeyUp)
		}

		function onKeyDown (event) {
			if (event.altKey) {
				emit('linked-value')
			}

			shiftKey.value = event.shiftKey
		}

		function onKeyUp (event) {
			emit('linked-value', false)
		}

		function deactivatedragNumber (event) {
			document.body.style.userSelect = null
			document.body.style.pointerEvents = null

			window.removeEventListener('mousemove', dragNumber)
			window.removeEventListener('mouseup', deactivatedragNumber)
			window.removeEventListener('keyup', onKeyUp)


			function preventClicks (e) {
				e.stopPropagation()
			}

			// Prevent closing colorpicker when clicked outside
			window.addEventListener('click', preventClicks, true)
			setTimeout(() => {
				window.removeEventListener('click', preventClicks, true)
			}, 100);

			reset()
		}

		function dragNumber (event) {
			const distance = initialPosition - event.clientY
			const directionUp = event.pageY < lastPosition
			const initialValue = typeof model.value !== 'undefined' ? model.value : props.min

			if (Math.abs(distance) > dragTreshold) {
				canChangeValue = true
			}

			if (canChangeValue && distance % 2 === 0) {
				document.body.style.pointerEvents = 'none'

				let increment = event.shiftKey ? props.shift_step : props.step
				model.value = directionUp ? +(initialValue + increment).toFixed(12) : +(initialValue - increment).toFixed(12)

				event.preventDefault()
			}

			lastPosition = event.clientY
		}

		onBeforeUnmount(() => {
			deactivatedragNumber()
		})

		return {
			shiftKey,
			model,
			actNumberDrag,
			onKeyDown,
			actNumberDrag,
			deactivatedragNumber
		}
	},

	data () {
		return {
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
			shiftKey: false
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
		color: var(--zb-surface-text-color);
	}
}
</style>
