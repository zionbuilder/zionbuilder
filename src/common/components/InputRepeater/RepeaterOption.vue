<template>
	<HorizontalAccordion :title="title" :combine-breadcrumbs="true" :show-back-button="true">
		<template #actions>
			<Icon
				v-if="clonable"
				v-znpb-tooltip="i18n.__('Clone', 'zionbuilder')"
				class="znpb-option-repeater-selector__clone-icon"
				icon="copy"
				@click.stop="cloneOption"
			></Icon>
			<Icon
				v-if="deletable"
				v-znpb-tooltip="i18n.__('Delete', 'zionbuilder')"
				class="znpb-option-repeater-selector__delete-icon"
				icon="delete"
				@click.stop="deleteOption(propertyIndex)"
			></Icon>
		</template>

		<OptionsForm
			:schema="schema"
			:modelValue="selectedOptionModel"
			class="znpb-option-repeater-form"
			@update:modelValue="onItemChange($event, propertyIndex)"
		/>
	</HorizontalAccordion>
</template>

<script lang="ts">
export default {
	name: 'RepeaterOption',
};
</script>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { computed } from 'vue';

const props = withDefaults(
	defineProps<{
		modelValue?: Record<string, any>;
		schema: Record<string, any>;
		propertyIndex?: number;
		item_title?: string;
		default_item_title: string;
		deletable?: boolean;
		clonable?: boolean;
	}>(),
	{
		modelValue: () => {
			return {};
		},
		propertyIndex: 0,
		deletable: true,
		clonable: true,
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: Record<string, any>): void;
	(e: 'clone-option', value: Record<string, any>): void;
	(e: 'delete-option', value: number): void;
}>();

const selectedOptionModel = computed({
	get() {
		return props.modelValue;
	},
	set(newValue: Record<string, any>) {
		emit('update:modelValue', newValue);
	},
});

const title = computed(() => {
	if (props.item_title && selectedOptionModel.value && selectedOptionModel.value[props.item_title]) {
		return selectedOptionModel.value[props.item_title];
	}

	return props.default_item_title.replace('%s', props.propertyIndex + 1);
});

function cloneOption() {
	const clone = JSON.parse(JSON.stringify(props.modelValue));
	emit('clone-option', clone);
}

function deleteOption(propertyIndex: number) {
	emit('delete-option', propertyIndex);
}

function onItemChange(newValues: Record<string, any>, index: number) {
	emit('update:modelValue', { newValues, index });
}

// const folded = ref(true);

// function toggleOptions() {
// 	folded.value = !folded.value;
// }

// function expand() {
// 	folded.value = false;
// }

// function collapse() {
// 	folded.value = true;
// }
</script>

<style lang="scss">
.znpb-options-form-wrapper.znpb-option-repeater-form {
	padding-top: 0;
}
</style>
