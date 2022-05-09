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
				:title="option.icon ? option.name : false"
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
import { Comment, computed, useSlots } from 'vue';
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
	}>(),
	{
		modelValue: () => {
			return [];
		},
		direction: 'vertical',
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
	}

	// Styles
	&-style--buttons {
		.znpb-checkbox-wrapper {
			padding: 0 2px;

			.znpb-checkmark-option {
				padding: 13px;
				margin-left: 0;
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
			background-color: var(--zb-surface-lighter-color);
			border-radius: 2px;

			&:hover {
				background-color: var(--zb-surface-lightest-color);
			}
		}

		& input:checked ~ .znpb-checkmark-option {
			color: var(--zb-secondary-text-color);
			background-color: var(--zb-secondary-color);
		}
	}
}
</style>
