<template>
	<div>
		<InputCustomSelector v-model="textAlignModel" :placeholder="placeholder" :options="textAlignOptions" :columns="4" />
	</div>
</template>

<script lang="ts">
export default {
	name: 'InputTextAlign',
};
</script>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { computed } from 'vue';
import { InputCustomSelector } from '../InputCustomSelector';

const props = defineProps<{
	modelValue?: string;
	placeholder?: string;
}>();

const emit = defineEmits<{
	(e: 'update:modelValue', value: string): void;
}>();

const textAlignOptions = [
	{
		icon: 'align--left',
		id: 'left',
		name: i18n.__('Align left', 'zionbuilder'),
	},
	{
		icon: 'align--center',
		id: 'center',
		name: i18n.__('Align center', 'zionbuilder'),
	},
	{
		icon: 'align--right',
		id: 'right',
		name: i18n.__('Align right', 'zionbuilder'),
	},
	{
		icon: 'align--justify',
		id: 'justify',
		name: i18n.__('Justify', 'zionbuilder'),
	},
];

const textAlignModel = computed({
	get() {
		return props.modelValue || '';
	},
	set(newValue: string) {
		emit('update:modelValue', newValue);
	},
});
</script>
