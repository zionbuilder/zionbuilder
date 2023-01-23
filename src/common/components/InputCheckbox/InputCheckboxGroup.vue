<template>
	<div class="znpb-checkbox-list" :class="wrapperClasses">
		<!-- @slot content for checkbox if no checkbox -->
		<slot></slot>
		<template v-if="!hasSlots">
			<InputCheckbox
				v-for="(option, i) in options"
				:key="i"
				v-model="model"
				:option-value="option.id"
				:label="option.name"
				:disabled="disabled"
				:placeholder="placeholder"
				:title="option.icon ? option.name : false"
				:class="{
					[`znpb-checkbox-list--isPlaceholder`]: model.length === 0 && placeholder && placeholder.includes(option.id),
				}"
			>
				<Icon v-if="option.icon" :icon="option.icon"></Icon>
			</InputCheckbox>
		</template>
	</div>
</template>
<script lang="ts">
export default {
	name: 'InputCheckboxGroup',
};
</script>

<script lang="ts" setup>
import { Comment, computed, useSlots, provide } from 'vue';
import { Icon } from '../Icon';
import InputCheckbox from './InputCheckbox.vue';

const props = withDefaults(
	defineProps<{
		modelValue?: string[];

		/** min & max checked values **/
		min?: number;
		max?: number;

		/** Allows to unselect the last value in case the limit is exceeded */
		allowUnselect?: boolean;
		direction?: 'vertical' | 'horizontal';
		options?: { id: string; name: string; icon: string }[];
		disabled?: boolean;
		displayStyle?: string;
		placeholder?: string[];
	}>(),
	{
		modelValue: () => {
			return [];
		},
		direction: 'vertical',
		placeholder: () => {
			return [];
		},
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: string[]): void;
}>();

const slots = useSlots();

const model = computed({
	get() {
		return props.modelValue ? props.modelValue : [];
	},
	set(newValue: string[]) {
		emit('update:modelValue', newValue);
	},
});

const wrapperClasses = computed(() => {
	return {
		[`znpb-checkbox-list--${props.direction}`]: props.direction,
		[`znpb-checkbox-list-style--${props.displayStyle}`]: props.displayStyle,
	};
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

// Provide the API for the InputCheckbox component
provide('checkboxGroup', {
	model,
	props,
});
</script>

<style lang="scss">
.znpb-checkbox-list {
	overflow: hidden;
	border-radius: 3px;

	&--vertical .znpb-checkbox-wrapper {
		margin-bottom: 16px;

		&:last-child {
			margin-bottom: 0;
		}
	}

	&--horizontal {
		display: flex;
		justify-content: space-between;
	}

	// Styles
	&-style--buttons {
		.znpb-checkbox-wrapper {
			background-color: var(--zb-surface-lighter-color);

			.znpb-checkmark-option {
				padding: 10px;
			}
		}

		& .znpb-form__input-checkbox,
		& .znpb-checkmark {
			display: none;
		}

		& .znpb-checkmark-option {
			display: flex;
			justify-content: center;
			align-items: center;
			padding: 10px;
			border-radius: 2px;
			margin: 3px;
		}

		.znpb-checkbox-wrapper:hover .znpb-checkmark-option,
		.znpb-checkbox-list--isPlaceholder .znpb-checkmark-option {
			background-color: var(--zb-surface-lightest-color);
			color: var(--zb-surface-text-active-color);
		}

		& input:checked ~ .znpb-checkmark-option {
			color: var(--zb-secondary-text-color);
			background-color: var(--zb-secondary-color);
		}
	}
}
</style>
