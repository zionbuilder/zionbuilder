<template>
	<div class="znpb-checkbox-switch-wrapper">
		<label
			class="znpb-checkbox-switch-wrapper__label"
			:class="{ [`znpb-checkbox-switch--${model ? 'checked' : 'unchecked'}`]: true }"
			:content="model ? __('Yes', 'zionbuilder') : __('No', 'zionbuilder')"
		>
			<input
				v-model="model"
				type="checkbox"
				:disabled="disabled"
				class="znpb-checkbox-switch-wrapper__checkbox"
				:modelValue="optionValue"
			/>

			<span class="znpb-checkbox-switch-wrapper__button"></span>
		</label>
	</div>
</template>

<script lang="ts" setup>
import { ref, computed, nextTick, inject } from 'vue';
import { __ } from '@wordpress/i18n';

const props = withDefaults(
	defineProps<{
		label?: string;
		showLabel?: boolean;
		modelValue?: string | Array<string> | boolean;
		optionValue?: string | boolean;
		disabled?: boolean;
		checked?: boolean;
		rounded?: boolean;
	}>(),
	{
		label: '',
		showLabel: true,
		disabled: false,
		checked: false,
		rounded: false,
		modelValue: '',
		optionValue: '',
	},
);

const emit = defineEmits(['update:modelValue', 'change']);

// Injections
const checkboxGroup = inject('checkboxGroup', null);

// Refs
const isLimitExceeded = ref(false);

// Computed data
const model = computed({
	get() {
		return props.modelValue !== undefined ? props.modelValue : false;
	},
	set(newValue) {
		if (checkboxGroup) {
			isLimitExceeded.value = false;
			const allowUnselect = checkboxGroup.props.allowUnselect;

			isLimitExceeded.value = false;
			// Check if minimum limit is meet
			if (checkboxGroup.props.min !== undefined && newValue.length < checkboxGroup.props.min) {
				isLimitExceeded.value = true;
			}

			// Check if maximum limit is meet
			if (checkboxGroup.props.max !== undefined && newValue.length > checkboxGroup.props.max) {
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
			/**
			 * when input model changed, it emits new value
			 */
			emit('update:modelValue', newValue);
		}
	},
});

if (props.checked) {
	setInitialValue();
}

function setInitialValue() {
	model.value = props.modelValue || true;
}

function onChange(event) {
	const checked = event.target.checked;
	if (isLimitExceeded.value) {
		nextTick(() => {
			event.target.checked = !checked;
		});

		return;
	}

	/**
	 * when input changed, it emits new value
	 */
	emit('change', !!event.target.checked);
}
</script>
<style lang="scss">
.znpb-checkbox-switch-wrapper {
	position: relative;
	display: flex;
	cursor: pointer;

	input[type='checkbox'].znpb-form__input-checkbox {
		width: 0;
		height: 0;
		margin: 0;
		border: none;

		&:focus {
			box-shadow: none;
		}
	}

	&:hover .znpb-checkmark:after,
	input:checked ~ .znpb-checkmark:after {
		display: block !important;
	}

	&__checkbox {
		display: none !important;
	}
	&__label {
		position: relative;
		display: block;
		box-sizing: content-box;
		width: 74px;
		height: 40px;
		background: var(--zb-input-bg-color);
		border: 2px solid var(--zb-input-border-color);
		border-radius: 3px;
		cursor: pointer;

		&:before,
		&:after {
			position: absolute;
			display: flex;
			justify-content: center;
			align-items: center;
			width: 50%;
			height: 100%;
			color: var(--zb-input-text-color);
			font-family: var(--zb-font-stack);
			font-size: 13px;
			font-weight: 500;
			text-align: center;
		}

		&:before {
			content: attr(content);
		}
		&:after {
			content: '' attr(content) '';
			right: 0;
			color: var(--zb-surface-text-active-color);
		}
	}

	&__button {
		position: absolute;
		top: 2px;
		left: 2px;
		z-index: 1;
		width: calc(50% - 2.5px);
		height: calc(100% - 4px);
		background: #006dd2;
		border-radius: 2px;
		transition: transform 0.15s, background-color 0.1s;
	}
}

.znpb-checkbox-switch--checked {
	&::before {
		content: '';
	}
}
.znpb-checkbox-switch--unchecked {
	&::after {
		content: '';
	}
	.znpb-checkbox-switch-wrapper__button {
		background: var(--zb-surface-lightest-color);
		transform: translateX(33px);
	}
}
</style>
