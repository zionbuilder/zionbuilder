<template>
	<Sortable
		v-model="sortableItems"
		class="znpb-option-repeater"
		handle=".znpb-horizontal-accordion > .znpb-horizontal-accordion__header"
	>
		<RepeaterOption
			v-for="(item, index) in sortableItems"
			:key="index"
			ref="repeaterItem"
			:schema="child_options"
			:modelValue="item"
			:property-index="index"
			:item_title="item_title"
			:default_item_title="default_item_title"
			:deletable="!addable ? false : deletable"
			:clonable="checkClonable"
			@clone-option="cloneOption($event, index)"
			@delete-option="deleteOption"
			@update:modelValue="onItemChange($event)"
		>
		</RepeaterOption>

		<template #end>
			<Button v-if="showButton" class="znpb-option-repeater__add-button" type="line" @click="addProperty">
				{{ add_button_text }}
			</Button>
		</template>
	</Sortable>
</template>

<script lang="ts">
export default {
	name: 'Repeater',
};
</script>

<script lang="ts" setup>
import { computed } from 'vue';
import RepeaterOption from './RepeaterOption.vue';

const props = withDefaults(
	defineProps<{
		modelValue?: Record<string, any>[];
		addable?: boolean;
		deletable?: boolean;
		clonable?: boolean;
		maxItems?: number;
		add_button_text?: string;
		child_options: Record<string, any>;
		item_title?: string;
		default_item_title: string;
		add_template?: Record<string, any>;
	}>(),
	{
		addable: true,
		deletable: true,
		clonable: true,
		add_button_text: () => {
			const { translate } = window.zb.i18n;
			return translate('generic_add_new') as unknown as string;
		},
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: Record<string, any>[]): void;
}>();

const sortableItems = computed({
	get() {
		return props.modelValue || [];
	},
	set(newValue: Record<string, any>[]) {
		emit('update:modelValue', newValue);
	},
});

const showButton = computed(() => {
	return props.maxItems ? props.addable && sortableItems.value.length < props.maxItems : props.addable;
});

const checkClonable = computed(() => {
	return !props.addable ? false : !props.maxItems ? props.clonable : sortableItems.value.length < props.maxItems;
});

function onItemChange(payload: { index: number; newValues: Record<string, any> }) {
	const { index, newValues } = payload;
	let copiedValues = [...sortableItems.value];
	let clonedNewValue = newValues;

	// Check to see if we need to delete the data
	if (newValues === null) {
		clonedNewValue = [];
	}
	copiedValues[index] = clonedNewValue;

	emit('update:modelValue', copiedValues);
}
function addProperty() {
	const clone = [...sortableItems.value];
	const newItem = props.add_template ?? {};
	clone.push(newItem);

	emit('update:modelValue', clone);

	// this.$nextTick(() => {
	// 	console.log(this.$refs.repeaterItem);
	// 	this.$refs.repeaterItem.expand()
	// })
}
function cloneOption(event: Record<string, any>, index: number) {
	if (
		(props.maxItems && props.addable && sortableItems.value.length < props.maxItems) ||
		props.maxItems === undefined
	) {
		const repeaterClone = [...sortableItems.value];
		repeaterClone.splice(index, 0, event);

		emit('update:modelValue', repeaterClone);
	}
}

function deleteOption(optionIndex: number) {
	let copiedValues = [...sortableItems.value];
	copiedValues.splice(optionIndex, 1);
	emit('update:modelValue', copiedValues);
}
</script>

<style lang="scss">
.znpb-option-repeater__add-button {
	width: 100%;
	margin-top: 5px;
	text-align: center;
}
</style>
