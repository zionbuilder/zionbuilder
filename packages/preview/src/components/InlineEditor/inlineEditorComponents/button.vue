<template>
	<Icon
		v-if="icon"
		:icon="icon"
		@mousedown="toggleFormatter"
		:class="classes"
	/>

	<span
		v-else
		class="zion-inline-editor-button"
		:class="classes"
		@mousedown="toggleFormatter"
	>{{buttontext}}</span>
</template>

<script>
import { ref, computed, inject, onMounted, onBeforeUnmount } from 'vue'

export default {
	props: ['formatter', 'icon', 'buttontext', 'formatterValue'],
	setup (props) {
		const editor = inject('ZionInlineEditor')
		const isActive = ref(false)

		const classes = computed(() => {
			let classes = []

			// Check if the button is active
			if (isActive.value) {
				classes.push('zion-inline-editor-button--active')
			}

			return classes.join(' ')
		})

		function checkIsActive () {
			isActive.value = editor.value.formatter.match(...getFormatterArguments())
		}

		function getFormatterArguments () {
			const formatterArguments = [props.formatter]

			if (props.formatterValue) {
				formatterArguments.push({
					value: props.formatterValue
				})
			}

			return formatterArguments
		}

		function toggleFormatter (event) {
			event.preventDefault()

			// Remove Style if this is already active
			editor.value.formatter.toggle(...getFormatterArguments())
		}

		function onNodeChange () {
			checkIsActive()
		}

		onMounted(() => {
			checkIsActive()
			editor.value.on('SelectionChange', onNodeChange)
		})

		onBeforeUnmount(() => {
			editor.value.off('SelectionChange', onNodeChange)
		})

		return {
			isActive,
			classes,
			toggleFormatter
		}
	}
}
</script>