<template>
	<div class="znpb-input-number">
		<BaseInput
			v-model="model"
			type="number"
			class="znpb-input-number__input"
			:min="min"
			:max="max"
			:step="shiftKey ? shiftStep : step"
			@keydown="onKeyDown"
			@mousedown="actNumberDrag"
			@touchstart.prevent.passive="actNumberDrag"
			@mouseup="deactivatedragNumber"
		>
			<!-- @slot Content that represents units -->
			<template #suffix>
				<slot></slot>
				{{ suffix }}
			</template>
		</BaseInput>
	</div>
</template>

<script lang="ts">
export default {
	name: 'InputNumber',
	inheritAttrs: false,
};
</script>

<script lang="ts" setup>
import { computed, ref, onBeforeUnmount } from 'vue';
import BaseInput from '../BaseInput/BaseInput.vue';

const props = withDefaults(
	defineProps<{
		modelValue: number;
		min?: number;
		max?: number;
		step?: number;
		shiftStep?: number;
		suffix?: string;
	}>(),
	{
		step: 1,
		shiftStep: 5,
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: number): void;
	(e: 'linked-value'): void;
}>();

let shiftKey = ref(false);
let initialPosition = 0;
let lastPosition = 0;
let dragTreshold = 3;
let canChangeValue = false;

const model = computed({
	get() {
		return props.modelValue;
	},
	set(newValue: number) {
		// Check if minimum value is meet
		if (props.min !== undefined && newValue < props.min) {
			newValue = props.min;
		}

		if (props.max !== undefined && newValue > props.max) {
			newValue = props.max;
		}

		if (newValue !== props.modelValue) {
			/**
			 * Emits new value number
			 */
			emit('update:modelValue', Number(newValue));
		}
	},
});

function reset() {
	initialPosition = 0;
	lastPosition = 0;
	canChangeValue = false;
}

function actNumberDrag(event: MouseEvent | TouchEvent) {
	if (event instanceof MouseEvent) {
		initialPosition = event.clientY;
	}

	document.body.style.userSelect = 'none';

	window.addEventListener('mousemove', dragNumber);
	window.addEventListener('mouseup', deactivatedragNumber);
	window.addEventListener('keyup', onKeyUp);
}

function onKeyDown(event: KeyboardEvent) {
	if (event.altKey) {
		emit('linked-value');
	}

	shiftKey.value = event.shiftKey;
}

function onKeyUp(event: KeyboardEvent) {
	emit('linked-value');
}

function deactivatedragNumber() {
	document.body.style.userSelect = '';
	document.body.style.pointerEvents = '';

	window.removeEventListener('mousemove', dragNumber);
	window.removeEventListener('mouseup', deactivatedragNumber);
	window.removeEventListener('keyup', onKeyUp);

	function preventClicks(e: Event) {
		e.stopPropagation();
	}

	// Prevent closing colorpicker when clicked outside
	window.addEventListener('click', preventClicks, true);
	setTimeout(() => {
		window.removeEventListener('click', preventClicks, true);
	}, 100);

	reset();
}

function dragNumber(event: MouseEvent) {
	const distance = initialPosition - event.clientY;
	const directionUp = event.pageY < lastPosition;
	const initialValue = model.value ?? props.min;

	if (Math.abs(distance) > dragTreshold) {
		canChangeValue = true;
	}

	if (canChangeValue && distance % 2 === 0) {
		document.body.style.pointerEvents = 'none';

		const increment = event.shiftKey ? props.shiftStep : props.step;
		model.value = directionUp ? +(initialValue + increment).toFixed(12) : +(initialValue - increment).toFixed(12);

		event.preventDefault();
	}

	lastPosition = event.clientY;
}

onBeforeUnmount(() => {
	deactivatedragNumber();
});
</script>
<style lang="scss">
.znpb-input-number__input {
	input[type='number'] {
		-moz-appearance: textfield;

		&::-webkit-inner-spin-button,
		&::-webkit-outer-spin-button {
			-webkit-appearance: none;
		}
		&:hover {
			cursor: ns-resize;
		}
	}
}

.znpb-input-number {
	.zion-input__suffix {
		margin-right: 7px;
		color: var(--zb-surface-text-color);
	}
}
</style>
