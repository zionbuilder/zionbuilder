<template>
	<div
		class="zb-el-counter"
		ref="root"
	>
		<slot name="start" />

		<div
			v-if="options.before"
			class="zb-el-counter__before"
			v-bind="api.getAttributesForTag('before_text_styles')"
		>
			{{options.before}}
		</div>

		<div class="zb-el-counter__number">{{options.start}}</div>

		<div
			v-if="options.after"
			class="zb-el-counter__after"
			v-bind="api.getAttributesForTag('after_text_styles')"
		>
			{{options.after}}
		</div>

		<slot name="end" />
	</div>
</template>

<script>
import { ref, watch, nextTick, onMounted, computed } from 'vue'

export default {
	name: 'counter',
	props: ['options', 'element', 'api'],
	setup (props) {
		const root = ref(null)

		onMounted(() => {
			runScript()
		})

		watch(
			() => [
				props.options.start,
				props.options.end,
				props.options.duration
			].toString()
		, (newValue, oldValue) => {
			runScript()
		})

		function runScript () {
			const script = window.ZionBuilderFrontend.getScript('counter')

			if (script) {
				script.run(root.value)
			}
		}

		return {
			root
		}
	}
}
</script>
