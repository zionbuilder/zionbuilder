<template>
	<div
		@input="onInput"
		:contenteditable="enabled"
		spellcheck="false"
	>
		{{editedText}}
	</div>
</template>

<script>
import { ref, watch } from 'vue'

export default {
	name: "InlineEdit",
	props: {
		modelValue: {
			type: String,
			required: false
		},
		enabled: {
			type: Boolean,
			required: false,
			default: true
		}
	},
	setup (props, { emit }) {
		const editedText = ref(props.modelValue)
		const newText = ref(props.modelValue)

		watch(() => props.modelValue, (newValue) => {
			if (newValue !== newText.value) {
				editedText.value = newValue
			}
		})

		function onInput (event) {
			newText.value = event.target.innerText
			emit('update:modelValue', event.target.innerText)
		}

		return {
			editedText,
			onInput
		}
	}
}
</script>