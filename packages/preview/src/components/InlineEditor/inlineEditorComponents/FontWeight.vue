<template>
	<PopOver
		icon="ite-weight"
		:is-active="isActive"
	>
		<InlineEditorButton
			v-for="fontWeight in fontWeights"
			:key="fontWeight"
			formatter="fontweight"
			:formatter-value="fontWeight"
			:buttontext="fontWeight"
		/>
	</PopOver>

</template>

<script>
import { ref, computed, inject, onBeforeMount, onBeforeUnmount } from 'vue'

// Components
import PopOver from './popOver.vue'
import InlineEditorButton from './button.vue'

export default {
	props: ['modelValue'],
	components: {
		PopOver,
		InlineEditorButton
	},
	setup (props) {
		const editor = inject('ZionInlineEditor')
		const isActive = ref(false)
		const fontWeights = [100, 200, 300, 400, 500, 600, 700, 800, 900]

		const classes = computed(() => {
			let classes = []

			// Check if the button is active
			if (isActive.value) {
				classes.push('zion-inline-editor-button--active')
			}

			return classes.join(' ')
		})

		function checkIfActive () {
			isActive.value = fontWeights.some((fontWeight) => {
				return editor.value.formatter.match('fontweight', { value: fontWeight })
			})
		}

		onBeforeMount(() => {
			checkIfActive()
			editor.value.on('NodeChange', checkIfActive)
		})

		onBeforeUnmount(() => {
			editor.value.off('NodeChange', checkIfActive)
		})

		return {
			isActive,
			classes,
			fontWeights
		}
	}
}
</script>

<style lang="scss">
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
