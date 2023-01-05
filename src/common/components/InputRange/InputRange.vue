<template>
	<div class="znpb-input-range">
		<BaseInput
			v-model="optionValue"
			type="range"
			:min="min"
			:max="max"
			:step="localStep"
			@keydown="onKeydown"
			@keyup="onKeyUp"
		>
			<template #suffix>
				<div class="znpb-input-range__trackwidth" :style="trackWidth"></div>
			</template>
		</BaseInput>
		<label class="znpb-input-range__label">
			<InputNumber
				v-model="optionValue"
				class="znpb-input-range-number"
				:min="min"
				:max="max"
				:step="step"
				:shift_step="shift_step"
				@keydown="onKeydown"
				@keyup="onKeyUp"
			>
				<!-- @slot Content for units -->
				<slot />
			</InputNumber>
		</label>
	</div>
</template>

<script lang="ts">
export default {
	name: 'InputRange',
	inheritAttrs: false,
};
</script>

<script lang="ts" setup>
import { computed, ref } from 'vue';
import BaseInput from '../BaseInput/BaseInput.vue';
import { InputNumber } from '../InputNumber';

const props = withDefaults(
	defineProps<{
		modelValue?: number;
		shift_step?: number;
		min?: number;
		max?: number;
		step?: number;
	}>(),
	{
		modelValue: 0,
		shift_step: 10,
		min: 0,
		max: 100,
		step: 1,
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: number): void;
}>();

const localStep = ref(props.step);

const optionValue = computed({
	get() {
		return props.modelValue ?? props.min;
	},
	set(newValue: number) {
		/**
		 * Emit input value when v-model
		 */
		emit('update:modelValue', +newValue);
	},
});

const trackWidth = computed(() => {
	// 14 is the thumb size
	const thumbSize = (14 * width.value) / 100;

	return {
		width: `calc(${width.value}% - ${thumbSize}px)`,
	};
});

const width = computed(() => {
	const minmax = props.max - props.min;
	return Math.round(((props.modelValue - props.min) * 100) / minmax);
});

function onKeydown(event: KeyboardEvent) {
	if (event.shiftKey) {
		localStep.value = props.shift_step;
	}
}

function onKeyUp(event: KeyboardEvent) {
	if (event.key === 'Shift') {
		localStep.value = props.step;
	}
}
</script>
<style lang="scss">
.znpb-input-range {
	display: flex;
	align-items: center;
	width: 100%;

	input[type='range']:focus {
		padding: 0;
		background: transparent;
	}

	& > .zion-input {
		align-items: center;
		border: none;
	}
	.zion-input,
	input[type='range'] {
		position: relative;
		flex: 2;
		width: 100%;
		padding: 0;
		background: var(--zb-input-bg-color);
	}
	& > label > .znpb-input-number > .zion-input > input[type='number'] {
		height: auto;
		padding: 12px 10px 12px 10px;
		font-family: var(--zb-font-stack);
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
		background-color: var(--zb-secondary-color);
	}
	&__label {
		flex: 1;
		margin-left: 7px;
		color: var(--zb-surface-icon-color);
		border-radius: 3px;
		.znpb-input-number .zion-input__suffix {
			font-size: 11px;
		}
	}

	// hide the initial slider
	input[type='range'] {
		border: none;

		-webkit-appearance: none;
	}
	input[type='range']::-webkit-slider-thumb {
		-webkit-appearance: none;
	}
	input[type='range']:focus {
		outline: none;
	}

	// style the slider
	/* Special styling for WebKit/Blink */
	input[type='range']::-webkit-slider-thumb {
		z-index: 1;
		width: 18px;
		height: 18px;
		margin: -8px 0 0;
		background-color: var(--zb-secondary-color);
		border-radius: 50%;
		cursor: pointer;
		&:active {
			transform: scale(1.1);
		}
	}

	/* for Firefox */
	input[type='range']::-moz-range-thumb {
		width: 18px;
		height: 18px;
		background: var(--zb-secondary-color);
		border-radius: 50%;
		transform: translate(0px, 0px);
		transition: all 0.2s ease;
		cursor: pointer;
	}

	/* All for IE */
	input[type='range']::-ms-thumb {
		width: 18px;
		height: 18px;
		background-color: var(--zb-surface-color);
		border: 2px solid var(--zb-secondary-color);
		border-radius: 50%;
		transform: translate(-1px, 0px);
		cursor: pointer;
	}
	input[type='range']:visited:-ms-thumb {
		transform: translate(0px, 0px);
	}
	input[type='range']::-webkit-slider-runnable-track {
		width: 100%;
		height: 2px;
		margin: 0;
		background: var(--zb-surface-lightest-color);
		cursor: pointer;
	}

	input[type='range']:focus::-webkit-slider-runnable-track {
		margin: 0;
		background: var(--zb-surface-lightest-color);
	}

	input[type='range']::-moz-range-track {
		width: 100%;
		height: 2px;
		margin: 0;
		background: var(--zb-surface-lightest-color);
		cursor: pointer;
	}

	input[type='range']::-ms-track {
		width: 100%;
		height: 2px;
		margin: 0;
		color: var(--zb-surface-text-active-color);
		background: var(--zb-surface-text-active-color);
		border-color: var(--zb-surface-text-active-color);
		cursor: pointer;
	}
	input[type='range']::-ms-fill-lower {
		border: 2px solid var(--zb-secondary-color);
	}
	input[type='range']::-ms-fill-upper {
		border: 2px solid var(--zb-secondary-color);
	}
}

// style for wordpress backend
.znpb-input-range-number {
	input.znpb-input-number__input {
		padding: 0;
		background-color: transparent;
	}
	.znpb-input-number__units {
		margin-left: -10px;
	}
}
</style>
