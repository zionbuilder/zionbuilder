<template>
	<div class="zb-el-counter">
		<slot name="start" />

		<div
			v-if="options.before"
			class="zb-el-counter__before"
			:class="api.getStyleClasses('before_text_styles')"
		>
			{{options.before}}
		</div>

		<div class="zb-el-counter__number">{{options.start}}</div>

		<div
			v-if="options.after"
			class="zb-el-counter__after"
			:class="api.getStyleClasses('after_text_styles')"
		>
			{{options.after}}
		</div>

		<slot name="end" />
	</div>
</template>

<script>
export default {
	name: 'counter',
	props: ['options', 'data', 'api'],
	mounted () {
		this.$nextTick(() => {
			this.runScript()
		})
	},
	methods: {
		runScript () {
			const script = window.ZionBuilderFrontend.getScript('counter')

			if (script) {
				script.run(this.$el)
			}
		}
	},
	created () {
		this.$watch((vm) => {
			return [
				vm.options.start,
				vm.options.end,
				vm.options.duration,
				vm.options.before,
				vm.options.after,
				Date.now()
			]
		}, () => {
			this.runScript()
		})
	}
}
</script>
