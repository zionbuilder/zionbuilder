<!-- Unused Component -->
<template>
	<div class="znpb-input-media-wrapper">
		<BaseInput
			v-model="inputValue"
			class="znpb-form__input-text"
			placeholder="Type your text here"
			@click="onButtonClick"
		/>

		<input
			ref="fileInput"
			type="file"
			style="display: none"
			:accept="type"
			name="file"
			@change="uploadFiles(($event.target as HTMLInputElement).name, ($event.target as HTMLInputElement).files)"
		/>

		<Button type="line" @click="onButtonClick">
			<Loader v-if="loading" :size="14" />
			<span v-else>{{ selectButtonText }}</span>
		</Button>
	</div>
</template>
<script lang="ts">
export default {
	name: 'InputFile',
};
</script>

<script lang="ts" setup>
import { ref, Ref, computed } from 'vue';
import BaseInput from '../BaseInput/BaseInput.vue';
import { uploadFile } from '../../api';

const props = withDefaults(
	defineProps<{
		modelValue?: string;
		type?: string;
		selectButtonText?: string;
	}>(),
	{
		type: 'image',
		selectButtonText: 'select',
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: string): void;
}>();

const fileInput: Ref<HTMLInputElement | null> = ref(null);
const loading = ref(false);

const inputValue = computed({
	get() {
		return props.modelValue || '';
	},
	set(newValue: string) {
		emit('update:modelValue', newValue);
	},
});

function onButtonClick() {
	if (fileInput.value) {
		fileInput.value.click();
	}
}

async function uploadFiles(fieldName: string, fileList: FileList | null) {
	const formData = new FormData();

	if (!fileList || !fileList.length) return;

	// append the files to FormData
	Array.from(fileList).forEach(file => {
		formData.append(fieldName, file, file.name);
	});

	loading.value = true;

	try {
		// send it to axios
		const response = await uploadFile(formData);
		const responseData = response.data;
		inputValue.value = responseData.file_url;
	} catch (err) {
		console.error(err);
	}

	loading.value = false;
}
</script>
<style lang="scss">
.znpb-input-media-wrapper {
	display: flex;

	.zion-input {
		margin-right: 7px;
	}
}
</style>
