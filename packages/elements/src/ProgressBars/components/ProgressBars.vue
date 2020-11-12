<template>
	<ul :class="{ 'znpb-progressBars--resetAnimation': resetAnimation}">
		<slot name="start" />

		<RenderTagGroup
			tag-id="single-bar"
			:repeater-value="bars"
		>
			<template v-slot:item="{ item, index }">
				<li
					class="zb-el-progressBars__singleBar"
					:class="[`zb-el-progressBars__bar--${index}`]"
				>
					<h5
						v-if="item.title"
						class="zb-el-progressBars__barTitle"
						:class="api.getStyleClasses('title_styles')"
					>
						{{item.title}}
					</h5>
					<span class="zb-el-progressBars__barTrack">

						<span
							class="zb-el-progressBars__barProgress"
							:data-width="item.fill_percentage !== undefined ? item.fill_percentage : 50"
						>
						</span>

					</span>

				</li>
			</template>
		</RenderTagGroup>

		<slot name="end" />
	</ul>
</template>

<script>
export default {
	name: 'progress_bars',
	props: ['options', 'data', 'api'],
	watch: {
		barsWidth () {
			this.doResetAnimation()
		},
		'options.transition_delay' () {
			this.doResetAnimation()
		}
	},
	data () {
		return {
			resetAnimation: false
		}
	},
	computed: {
		bars () {
			return this.options.bars || []
		},
		barsWidth () {
			const barsWidth = (this.options.bars || []).map(item => {
				return item.fill_percentage
			})

			return barsWidth.join('')
		}
	},
	mounted () {
		window.requestAnimationFrame(() => {
			this.runScript()
		})
	},
	methods: {
		doResetAnimation () {
			this.resetAnimation = true
			this.runScript().then(() => {
				this.resetAnimation = false
			})
		},
		runScript () {
			return new Promise((resolve, reject) => {
				const script = window.ZionBuilderFrontend.getScript('progressBars')

				if (script) {
					window.requestAnimationFrame(() => {
						script.run(this.$el)
						resolve()
					})
				}
			})
		}
	}
}
</script>

<style lang="scss">
.znpb-progressBars--resetAnimation .zb-el-progressBars__barProgress {
	width: 0 !important;
	transition: none !important;
}
</style>
