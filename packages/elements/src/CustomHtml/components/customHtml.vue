<template>
	<div>
		<slot name="start" />

		<div v-html="content"></div>
		<div
			v-if="phpError.length > 0"
			class="znpb-notice znpb-notice--error"
			v-html="phpError"
		></div>

		<slot name="end" />
	</div>
</template>

<script>
import { onBeforeUnmount, onMounted, ref, computed } from 'vue'

export default {
	name: 'custom_html',
	props: ['options', 'element', 'api'],
	setup (props) {
		const phpMarkup = ref('')
		const phpError = ref('')
		const content = computed(() => {
			return props.options.content + phpMarkup.value
		})

		function onApplyPHPCode () {
			window.zb.editor.serverRequest.request({
				type: 'parse_php',
				config: props.options.php
			}, (response) => {
				if (response && response.error) {
					phpError.value = response.message
					phpMarkup.value = ''
				} else {
					phpMarkup.value = response
					phpError.value = ''
				}
			}, function (message) {
				// eslint-disable-next-line
				console.log('server Request fail', message)
			})

		}

		props.element.on('apply_php_code', onApplyPHPCode)

		onMounted(onApplyPHPCode)
		onBeforeUnmount(() => props.element.off('apply_php_code', onApplyPHPCode))

		return {
			content,
			phpError
		}
	}
}
</script>
