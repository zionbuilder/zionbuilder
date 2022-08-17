<template>
	<label class="znpb-checkbox-wrapper" :aria-disabled="disabled">
		<input
			v-model="model"
			type="checkbox"
			aria-hidden="true"
			:disabled="disabled"
			:value="optionValue"
			class="znpb-form__input-checkbox"
			@change="onChange"
		/>
		<span class="znpb-checkmark" :class="{ 'znpb-checkmark--rounded': rounded }"></span>
		<span v-if="hasSlots || label" class="znpb-checkmark-option">
			<!-- @slot content for checkbox or label -->
			<slot></slot>
			<template v-if="showLabel && label">
				{{ label }}
			</template>
		</span>
	</label>
</template>

<script lang="ts">
export default {
	name: 'InputCheckbox',
};
</script>

<script lang="ts" setup>
import { ref, Comment, computed, nextTick, useSlots, getCurrentInstance } from 'vue';
import InputCheckboxGroup from './InputCheckboxGroup.vue';

const props = withDefaults(
	defineProps<{
		label?: string;
		showLabel?: boolean;
		modelValue?: boolean | string[];
		optionValue?: string | boolean;
		disabled?: boolean;
		checked?: boolean;
		rounded?: boolean;
		placeholder?: boolean | string[];
	}>(),
	{
		modelValue: true,
		showLabel: true,
		placeholder: () => {
			return [];
		},
	},
);

const isLimitExceeded = ref(false);

const emit = defineEmits<{
	(e: 'update:modelValue', value: boolean | string[]): void;
	(e: 'change', value: boolean): void;
}>();

const slots = useSlots();

// @Todo Move this logic to CheckboxGroup
const model = computed({
	get() {
		return props.modelValue;
	},
	set(newValue: boolean | string[]) {
		isLimitExceeded.value = false;
		const allowUnselect = parentGroup.value?.allowUnselect;

		if (Array.isArray(newValue)) {
			isLimitExceeded.value = false;
			// Check if minimum limit is meet
			if (parentGroup.value?.min !== undefined && newValue.length < parentGroup.value?.min) {
				isLimitExceeded.value = true;
			}

			// Check if maximum limit is meet
			if (parentGroup.value?.max && newValue.length > parentGroup.value?.max) {
				isLimitExceeded.value = true;
			}

			if (isLimitExceeded.value === false) {
				emit('update:modelValue', newValue);
			} else if (allowUnselect && isLimitExceeded.value === true) {
				const clonedValues = [...newValue];
				clonedValues.shift();
				// Allows to change the option check state on nextThick
				isLimitExceeded.value = false;
				emit('update:modelValue', clonedValues);
			}
		} else {
			emit('update:modelValue', newValue);
		}
	},
});

const instance = getCurrentInstance();

const parentGroup = computed<InstanceType<typeof InputCheckboxGroup> | null>(() => {
	const isInGroup = instance?.parent?.type.name === 'InputCheckboxGroup';
	return isInGroup ? instance?.parent?.ctx : null;
});

const hasSlots = computed(() => {
	if (!slots.default) {
		return false;
	}

	const defaultSlot = slots.default();
	const normalNodes = [];
	if (Array.isArray(defaultSlot)) {
		defaultSlot.forEach(vNode => {
			if (vNode.type !== Comment) {
				normalNodes.push(vNode);
			}
		});
	}

	return normalNodes.length > 0;
});

function onChange(event: Event) {
	const checkbox = event.target as HTMLInputElement;
	if (isLimitExceeded.value) {
		nextTick(() => {
			checkbox.checked = !checkbox.checked;
		});

		return;
	}

	emit('change', !!checkbox.checked);
}
</script>
<style lang="scss">
.znpb-checkbox-wrapper {
	position: relative;
	display: flex;
	cursor: pointer;

	input[type='checkbox'].znpb-form__input-checkbox {
		position: absolute;
		width: 0;
		height: 0;
		margin: 0;
		border: none;

		&:focus {
			box-shadow: none;
		}

		&:before {
			content: '';
		}
	}

	input:checked ~ .znpb-checkmark:after {
		display: block;
	}
}

input:checked ~ .znpb-checkmark {
	background-color: var(--zb-secondary-color);
	border-color: var(--zb-secondary-color);
}
input:checked ~ .znpb-checkmark:after {
	display: block;
}
input[type='checkbox']:disabled ~ .znpb-checkmark {
	background-color: var(--zb-surface-lighter-color);
}
.znpb-checkmark:after {
	top: 4px;
	left: 7px;
	width: 4px;
	height: 8px;
	border: solid var(--zb-primary-text-color);
	border-width: 0 2px 2px 0;
	transform: rotate(45deg);
}

.znpb-checkmark {
	position: relative;
	top: 0;
	left: 0;
	flex-shrink: 0;
	width: 24px;
	height: 24px;
	background-color: var(--zb-surface-color);
	border: 2px solid var(--zb-surface-border-color);
	border-radius: 3px;
	transition: all 0.2s;

	&--rounded {
		border-radius: 50%;
	}

	&-option {
		white-space: nowrap;
	}

	&:after {
		content: '';
		position: absolute;
		display: none;
	}
}
.znpb-checkbox-wrapper {
	align-items: center;
	margin-bottom: 10px;

	.znpb-checkmark-option {
		width: 100%;
		margin-left: 10px;
		font-weight: 500;
		text-align: left;
	}
}
</style>
