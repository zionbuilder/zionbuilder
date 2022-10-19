<template>
	<div class="znpb-gradient-actions">
		<OptionsForm v-model="valueModel" :schema="schema" class="znpb-gradient-color-form" />
		<div v-if="showDelete" class="znpb-gradient-actions__delete">
			<Icon icon="close" class="znpb-gradient-actions-delete" @click.stop="$emit('delete-color', config)" />
		</div>
	</div>
</template>

<script lang="ts">
export default {
	name: 'GradientColorConfig',
};
</script>

<script lang="ts" setup>
import { computed } from 'vue';
import { cloneDeep } from 'lodash-es';
import Icon from '../Icon/Icon.vue';
import type { Color } from './GradientBar.vue';

const props = withDefaults(
	defineProps<{
		config: Color;
		showDelete?: boolean;
	}>(),
	{
		showDelete: true,
	},
);

const emit = defineEmits<{
	(e: 'delete-color', value: Color): void;
	(e: 'update:modelValue', value: Color): void;
}>();

const schema = {
	color: {
		type: 'colorpicker',
		id: 'color',
		width: '50',
	},
	position: {
		type: 'number',
		id: 'position',
		content: '%',
		width: '50',
		min: 0,
		max: 100,
	},
};

const valueModel = computed({
	get() {
		const value = cloneDeep(props.config);
		if (Array.isArray(value.__dynamic_content__)) {
			value.__dynamic_content__ = {};
		}
		return value;
	},
	set(newValue: Color) {
		emit('update:modelValue', newValue);
	},
});
</script>

<style lang="scss">
.znpb-gradient-actions {
	position: relative;
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 10px;

	&:last-child {
		margin-bottom: 0;
	}

	.znpb-gradient-color-form {
		flex-wrap: nowrap;
		justify-content: space-between;
		padding: 0;
		.znpb-input-type--colorpicker {
			margin-right: 5px;

			&:last-child {
				margin-right: 0;
			}
		}
	}

	.znpb-form-colorpicker-trigger-wrapper {
		padding-left: 8px;
		margin-bottom: 0;
	}
	.znpb-form-colorpicker__text {
		max-width: 80px;
	}
	.znpb-form-gradient {
		display: flex;
		align-items: center;
		margin: 0;

		&:first-child {
			flex-basis: 50%;
			flex-grow: 1;
			flex-shrink: 0;
			margin-right: 10px;
		}

		.znpb-forms-input-content {
			width: 100%;
		}
	}
	& > .znpb-input-wrapper > .znpb-form__input-title {
		padding: 15px 15px 15px 0;
		margin-bottom: 0;
	}
	&__delete {
		display: flex;
		justify-content: flex-end;
		align-items: center;
		padding: 11px;
		margin-left: 5px;
		font-size: 14px;
		background: transparent;
		border: 2px solid var(--zb-surface-border-color);
		border-radius: 3px;
		transition: opacity 0.15s ease;
		cursor: pointer;

		&:hover {
			opacity: 0.7;
		}
	}

	.znpb-form-gradient + .znpb-gradient-actions__delete {
		margin-left: 10px;
	}
}
</style>
