<template>
	<Icon v-if="icon" :icon="icon" :class="classes" @mousedown="toggleFormatter" />

	<span v-else class="zion-inline-editor-button" :class="classes" @mousedown="toggleFormatter">{{ buttontext }}</span>
</template>

<script>
import { ref, computed, inject, onMounted, onBeforeUnmount } from 'vue';

export default {
	props: ['formatter', 'icon', 'buttontext', 'formatterValue'],
	setup(props) {
		const editor = inject('ZionInlineEditor');
		const isActive = ref(false);

		const classes = computed(() => {
			const classes = [];

			// Check if the button is active
			if (isActive.value) {
				classes.push('zion-inline-editor-button--active');
			}

			return classes.join(' ');
		});

		function checkIsActive() {
			isActive.value = editor.editor.formatter.match(...getFormatterArguments());
		}

		function getFormatterArguments() {
			const formatterArguments = [props.formatter];

			if (props.formatterValue) {
				formatterArguments.push({
					value: props.formatterValue,
				});
			}

			return formatterArguments;
		}

		function toggleFormatter(event) {
			event.preventDefault();

			// Remove Style if this is already active
			editor.editor.formatter.toggle(...getFormatterArguments());
		}

		function onNodeChange() {
			checkIsActive();
		}

		onMounted(() => {
			checkIsActive();
			editor.editor.on('SelectionChange', onNodeChange);
		});

		onBeforeUnmount(() => {
			editor.editor.off('SelectionChange', onNodeChange);
		});

		return {
			isActive,
			classes,
			toggleFormatter,
		};
	},
};
</script>
