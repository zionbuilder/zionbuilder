<template>
	<div>
		<slot name="start" />
		<InlineEditor v-model="content" />
		<slot name="end" />
	</div>
</template>

<script>
import { isEqual } from 'lodash-es'
import { computed, watch } from 'vue'
export default {
	name: 'ZionText',
	props: ['options', 'element', 'api'],
	setup (props) {

		// watch content in case it is changed from inline editor
		watch(
			() => [
				props.options.content
			].toString()
			, (newValue, oldValue) => {
				if (isEqual(newValue, oldValue)) {
					return
				}

				props.element.updateOptionValue('content', newValue)

			})

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
