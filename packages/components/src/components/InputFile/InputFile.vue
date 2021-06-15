<template>
	<div class="znpb-input-media-wrapper">
		<BaseInput
			v-model="inputValue"
			@click="onButtonClick"
			class="znpb-form__input-text"
			placeholder="Type your text here"
		/>

		<input
			type="file"
			ref="fileInput"
			style="display:none"
			:accept="type"
			name="font"
			@change="uploadFiles($event.target.name, $event.target.files)"
		>

		<Button
			@click="onButtonClick"
			type="line"
		>{{selectButtonText}}
		</Button>
	</div>
</template>
<script>
import { ref, computed } from 'vue'
import BaseInput from '../BaseInput/BaseInput.vue'

export default {
	name: 'InputFile',
	props: {
		/**
		 * Value for input
		 */
		modelValue: {
			type: String,
			required: false
		},
		/**
		 * Type of media
		 */
		type: {
			required: false,
			type: String,
			default: 'image'
		},
		/**
		 * Text on button
		 */
		selectButtonText: {
			required: false,
			type: String,
			default: 'Select'
		},
	},
	components: {
		BaseInput
	},
	setup (props, { emit }) {
		const fileInput = ref(null)

		const inputValue = computed({
			get () {
				return props.modelValue
			},
			set (newValue) {
				emit('update:modelValue', newValue)
			}
		})

		function onButtonClick () {
			if (fileInput.value) {
				fileInput.value.click()
			}
		}

		function uploadFiles (fieldName, fileList) {
			const formData = new FormData()
			console.log({ fieldName, fileList });
			if (!fileList.length) return

			// append the files to FormData
			Array.from(fileList).forEach(file => {
				// console.log(fieldName);
				// console.log(file);
				// console.log(file.name);
				formData.append(fieldName, file, file.name)
			})

			// send it to axios
			saveFile(formData)
		}

		function saveFile (fileData) {
			console.log(fileData);
		}

		return {
			onButtonClick,
			fileInput,
			uploadFiles,
			inputValue
		}
	}
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
