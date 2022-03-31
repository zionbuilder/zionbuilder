<template>
	<div>
		<InputCustomSelector v-model="textAlignModel" :options="textAlignOptions" :columns="4"> </InputCustomSelector>
	</div>
</template>

<script lang="ts">
export default {
	name: 'InputTextAlign',
};
</script>

<script lang="ts" setup>
import { computed } from 'vue';
import { InputCustomSelector } from '../InputCustomSelector';
import { translate } from '@zb/i18n';

const props = defineProps<{
	modelValue?: string;
}>();

const emit = defineEmits<{
	(e: 'update:modelValue', value: string): void;
}>();

const textAlignOptions = [
	{
		icon: 'align--left',
		id: 'left',
		name: translate('align_left'),
	},
	{
		icon: 'align--center',
		id: 'center',
		name: translate('align_center'),
	},
	{
		icon: 'align--right',
		id: 'right',
		name: translate('align_right'),
	},
	{
		icon: 'align--justify',
		id: 'justify',
		name: translate('justify'),
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
