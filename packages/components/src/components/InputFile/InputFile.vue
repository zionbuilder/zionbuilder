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
			name="file"
			@change="uploadFiles($event.target.name, $event.target.files)"
		>

		<Button
			@click="onButtonClick"
			type="line"
		>
			<Loader
				v-if="loading"
				:size="14"
			/>
			<span v-else>{{selectButtonText}}</span>
		</Button>
	</div>
</template>
<script>
import { ref, computed } from 'vue'

import BaseInput from '../BaseInput/BaseInput.vue'
import { uploadFile } from '@zb/rest'
import { useNotifications } from '@zionbuilder/composables'

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
		const loading = ref(false)

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

		async function uploadFiles (fieldName, fileList) {
			const formData = new FormData()

			if (!fileList.length) return

			// append the files to FormData
			Array.from(fileList).forEach(file => {
				formData.append(fieldName, file, file.name)
			})

			loading.value = true

			try {
				// send it to axios
				const response = await uploadFile(formData)
				const responseData = response.data
				inputValue.value = responseData.file_url
			} catch (err) {
				console.error(err)
			}

			loading.value = false
		}

		return {
			onButtonClick,
			fileInput,
			uploadFiles,
			inputValue,
			loading
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
