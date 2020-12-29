<template>
	<InlineEditorPopOver
		icon="ite-alignment"
		:is-active="isActive"
	>
		<!-- Align left -->
		<InlineEditorButton
			@click="alignButtonIcon = 'align--left'"
			formatter="alignleft"
			icon="align--left"
		/>

		<!-- Align center -->
		<InlineEditorButton
			@click="alignButtonIcon = 'align--center'"
			formatter="aligncenter"
			icon="align--center"
		/>

		<!-- Align right -->
		<InlineEditorButton
			@click="alignButtonIcon = 'align--right'"
			formatter="alignright"
			icon="align--right"
		/>

		<!-- Align justify -->
		<InlineEditorButton
			@click="alignButtonIcon = 'align--justify'"
			formatter="alignjustify"
			icon="align--justify"
		/>
	</InlineEditorPopOver>
</template>

<script>
import { ref, inject, onMounted, onBeforeUnmount } from 'vue'

// Components
import InlineEditorButton from './button.vue'
import InlineEditorPopOver from './popOver.vue'

export default {
	name: 'TextAlign',
	components: {
		InlineEditorButton,
		InlineEditorPopOver
	},
	setup (props) {
		const editor = inject('ZionInlineEditor')
		const isActive = ref(false);

		function checkIfActive () {
			isActive.value = editor.value.formatter.matchAll([
				'alignleft',
				'aligncenter',
				'alignright',
				'alignjustify',
			]).length > 0
		}


		onMounted(() => {
			checkIfActive()
			editor.value.on('SelectionChange', checkIfActive)
		})

		onBeforeUnmount(() => {
			editor.value.off('SelectionChange', checkIfActive)
		})

		return {
			isActive
		}
	}
}
</script>

<style>
</style>