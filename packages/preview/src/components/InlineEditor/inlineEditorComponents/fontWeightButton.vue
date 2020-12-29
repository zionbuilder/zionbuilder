<template>
	<span
		class="zion-inline-editor-button"
		:class="classes"
		@mousedown="setTextStyle"
	>{{modelValue}}</span>
</template>

<script>
import { ref, computed, inject, onBeforeMount, onBeforeUnmount } from 'vue'

export default {
	props: ['modelValue'],
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

		// Apply button style
		function setTextStyle () {
			// Remove Style if this is already active
			editor.value.formatter.toggle('fontweight', { value: props.modelValue })
		}

		// Check if the selection has a specific style applied
		function hasFormat () {
			isActive.value = editor.value.formatter.match('fontweight', { value: props.modelValue })
		}

		onBeforeMount(() => {
			editor.value.on('SelectionChange', hasFormat)
		})

		onBeforeUnmount(() => {
			editor.value.off('SelectionChange', hasFormat)
		})

		return {
			isActive,
			classes,
			setTextStyle
		}
	}
}
</script>

<style lang="scss" >
.zion-inline-editor-button {
	padding: 6px;
	font-size: 13px;
	font-weight: 500;
	line-height: 25px;

	&:hover {
		color: darken($font-color, 10%);
	}

	@at-root {
		.znpb-editor-icon-wrapper.ite-weight ~ .zion-inline-editor-dropdown
		.zion-inline-editor-button--active {
			color: $surface-active-color;
		}
	}
}
</style>
