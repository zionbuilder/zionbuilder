<template>
	<Icon
		v-if="icon"
		:icon="icon"
		@mousedown="setTextStyle"
		:class="classes"
	/>

	<span
		v-else
		class="zion-inline-editor-button"
		:class="classes"
		@mousedown="setTextStyle"
	>{{buttontext}}</span>
</template>

<script>
import { ref, computed, inject } from 'vue'

export default {
	props: ['formatter', 'icon', 'buttontext'],
	setup (props) {
		const editor = inject('ZionInlineEditor')
		// console.log(editor.value.formatter);
		const isActive = ref(editor.value.formatter.match(props.formatter))
		const classes = computed(() => {
			let classes = []

			// Check if the button is active
			if (isActive.value) {
				classes.push('zion-inline-editor-button--active')
			}

			return classes.join(' ')
		})

		function setTextStyle (event) {
			event.preventDefault()

			// Remove Style if this is already active
			editor.value.formatter.toggle(props.formatter)
		}

		editor.value.formatter.formatChanged(props.formatter, function (state, args) {
			isActive.value = state
		})

		return {
			isActive,
			classes,
			setTextStyle
		}
	}
}
</script>

<style lang="scss" scoped>
</style>
