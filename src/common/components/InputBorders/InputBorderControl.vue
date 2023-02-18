<template>
	<OptionsForm v-model="computedValue" :schema="schema" class="znpb-border-control-group" />
</template>

<script lang="ts">
export default {
	name: 'InputBorderControl',
};
</script>

<script lang="ts" setup>
import { computed } from 'vue';

export type BorderValue = { color?: string; style?: string; width?: string };

const props = withDefaults(
	defineProps<{
		modelValue?: BorderValue;
		title?: string;
		placeholder?: BorderValue;
	}>(),
	{
		placeholder: () => {
			return {};
		},
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: BorderValue): void;
}>();

const schema = computed(() => {
	return {
		color: {
			id: 'color',
			type: 'colorpicker',
			css_class: 'znpb-border-control-group-item',
			title: 'Color',
			width: 100,
			placeholder: props.placeholder ? props.placeholder['color'] : null,
		},
		width: {
			id: 'width',
			type: 'number_unit',
			title: 'Width',
			min: 0,
			max: 999,
			step: 1,
			css_class: 'znpb-border-control-group-item',
			width: 50,
			placeholder: props.placeholder ? props.placeholder['width'] : null,
		},
		style: {
			id: 'style',
			type: 'select',
			title: 'Style',
			options: ['solid', 'dashed', 'dotted', 'double', 'groove', 'ridge', 'inset', 'outset'].map(style => {
				return { name: style, id: style };
			}),
			css_class: 'znpb-border-control-group-item',
			width: 50,
			placeholder: props.placeholder ? props.placeholder['style'] : 'solid',
		},
	};
});

const computedValue = computed({
	get() {
		return props.modelValue || {};
	},
	set(newValue: BorderValue) {
		emit('update:modelValue', newValue);
	},
});
</script>

<style lang="scss">
.znpb-input-wrapper.znpb-border-control-group-item {
	padding-bottom: 0;
}

.znpb-input-wrapper.znpb-border-control-group-item.znpb-input-type--colorpicker {
	margin: 0 0 20px 0;
}

.znpb-border-control-group-item .znpb-global-color-select-innerWrapper {
	justify-content: flex-start;
}

.znpb-border-control-group-item .znpb-global-color-select-tooltip {
	flex: 1;
	padding-right: 10px;
}

.znpb-border-control-group-item .znpb-global-color-select-innerWrapper > span:last-child {
	margin-left: auto;
}

.znpb-border-control-group-item .znpb-global-color-select__id {
	font-weight: 500;
	width: auto;
}
</style>
