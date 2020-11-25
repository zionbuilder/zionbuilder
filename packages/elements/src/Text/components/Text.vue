<template>
	<div>
		<slot name="start" />
		<InlineEditor v-model="content" />
		<slot name="end" />
	</div>
</template>

<script>
import { computed } from 'vue'
export default {
	name: 'ZionText',
	props: ['options', 'element', 'api'],
	setup (props) {
		const content = computed({
			get () {
				return props.options.content
			},
			set (newValue) {
				props.element.updateOptionValue('content', newValue)
			}
		})

		return {
			content
		}
	}
}
</script>
