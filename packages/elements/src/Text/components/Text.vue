<template>
	<div>
		<slot name="start" />
		{{options}}
		<InlineEditor v-model="content" />
		<slot name="end" />
	</div>
</template>

<script>
import { computed } from 'vue'
export default {
	name: 'zion_text',
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
