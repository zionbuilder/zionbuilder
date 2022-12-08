<template>
	<div
		class="znpb-input-range znpb-input-range--has-multiple-units"
		:class="{ ['znpb-input-range--disabled']: disabled }"
	>
		<BaseInput
			ref="rangebase"
			v-model="rangeModel"
			type="range"
			:min="activeOption.min"
			:max="activeOption.max"
			:step="step"
			:disabled="disabled"
			@keydown="onRangeKeydown"
			@keyup="onRangeKeyUp"
		>
			<template #suffix>
				<div class="znpb-input-range__trackwidth" :style="trackWidth"></div>
			</template>
		</BaseInput>
		<label class="znpb-input-range__label">
			<InputNumberUnit
				ref="inputNumberUnit"
				v-model="computedValue"
				class="znpb-input-range-number"
				:min="activeOption.min"
				:max="activeOption.max"
				:units="getUnits"
				:step="step"
				:shift_step="shiftStep"
				@is-custom-unit="onCustomUnit"
				@unit-update="onUnitUpdate"
			>
			</InputNumberUnit>
		</label>
	</div>
</template>

<script lang="ts">
export default {
	name: 'InputRangeDynamic',
	inheritAttrs: false,
};
</script>

<script lang="ts" setup>
/**
 * @todo Merge with InputRange
 */
import { ref, computed, CSSProperties } from 'vue';
import BaseInput from '../BaseInput/BaseInput.vue';
import InputNumberUnit from '../InputNumber/InputNumberUnit.vue';
import rafSchd from 'raf-schd';

const props = withDefaults(
	defineProps<{
		modelValue?: string | null;
		options: {
			unit: string;
			value: number;
			min: number;
			max: number;
			step: number;
			shiftStep: number;
		}[];
		default_step?: number;
		default_shift_step?: number;
		min?: number;
		max?: number;
	}>(),
	{
		modelValue: null,
		default_step: 1,
		default_shift_step: 1,
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: string | null): void;
}>();

const rangebase = ref<InstanceType<typeof BaseInput> | null>(null);
const inputNumberUnit = ref<InstanceType<typeof InputNumberUnit> | null>(null);

const step = ref(1);
const unit = ref('');
const customUnit = ref(false);

const rafUpdateValue = rafSchd(updateValue);

const activeOption = computed(() => {
	let activeOption = null;
	props.options.forEach(option => {
		if (valueUnit.value && option.unit === valueUnit.value.unit) {
			activeOption = option;
		}
	});
	return activeOption || props.options[0];
});

const valueUnit = computed({
	get() {
		const match =
			typeof props.modelValue === 'string'
				? props.modelValue.match(/^([+-]?[0-9]+([.][0-9]*)?|[.][0-9]+)(\D+)$/)
				: null;
		const value = match && match[1] ? +match[1] : null;
		const unit = match ? match[3] : null;

		return {
			value,
			unit,
		};
	},
	set(newValue: { value: number | null; unit: string | null }) {
		if (newValue.value && newValue.unit) {
			if (Number(newValue.value) > activeOption.value.max) {
				computedValue.value = `${activeOption.value.max}${newValue.unit}`;
			} else if (Number(newValue.value) < activeOption.value.min) {
				computedValue.value = `${activeOption.value.min}${newValue.unit}`;
			}
		}
	},
});

const computedValue = computed({
	get() {
		return props.modelValue;
	},
	set(newValue: string | null) {
		rafUpdateValue(newValue);
	},
});

const rangeModel = computed({
	get() {
		return disabled.value ? 0 : valueUnit.value.value || props.min || 0;
	},
	set(newValue: number) {
		/**
		 * Emit input value when range updates
		 */
		if (getUnit.value) {
			computedValue.value = `${newValue}${getUnit.value}`;
		}
	},
});

const getUnit = computed(() => activeOption.value.unit ?? valueUnit.value.unit ?? unit.value ?? null);

const getUnits = computed(() => props.options.map(option => option.unit));

const baseStep = computed(() => activeOption.value.step || props.default_step);

const shiftStep = computed(() => activeOption.value.shiftStep || props.default_shift_step);

const trackWidth = computed<CSSProperties>(() => {
	// 14 is the thumb size
	const thumbSize = (14 * width.value) / 100;
	return {
		width: `calc(${width.value}% - ${thumbSize}px)`,
	};
});

const width = computed(() => {
	const minmax = activeOption.value.max - activeOption.value.min;
	return Math.round(((activeOption.value.value - activeOption.value.min) * 100) / minmax);
});

const disabled = computed(() => {
	const transformOriginUnits = ['left', 'right', 'top', 'bottom', 'center'];
	return transformOriginUnits.includes(unit.value) || customUnit.value;
});

function updateValue(newValue: string | null) {
	emit('update:modelValue', newValue);
}

function onUnitUpdate(event: string) {
	unit.value = event;
}

function onCustomUnit(event: boolean) {
	customUnit.value = event;
}

function onRangeKeydown(event: KeyboardEvent) {
	if (event.shiftKey) {
		step.value = shiftStep.value;
	}
}

function onRangeKeyUp(event: KeyboardEvent) {
	if (event.key === 'Shift') {
		step.value = baseStep.value;
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
		.znpb-input-number--has-units .znpb-input-number__units-multiple .znpb-input-number__active-unit {
			background: var(--zb-surface-color);
		}
	}
	&.znpb-input-range--has-multiple-units {
		input[type='number'] {
			padding: 12px 10px 12px 0;
			font-family: var(--zb-font-stack);
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
