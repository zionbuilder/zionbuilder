<template>
	<ul :class="{ 'znpb-progressBars--resetAnimation': resetAnimation }">
		<slot name="start" />

		<li
			v-for="(item, index) in bars"
			:key="index"
			class="zb-el-progressBars__singleBar"
			:class="[`zb-el-progressBars__bar--${index}`]"
			v-bind="api.getAttributesForTag('single-bar', {}, index)"
		>
			<h5
				v-if="item.title"
				class="zb-el-progressBars__barTitle"
				:class="api.getStyleClasses('title_styles')"
				v-bind="api.getAttributesForTag('title_styles')"
			>
				{{ item.title }}
			</h5>

			<span class="zb-el-progressBars__barTrack">
				<span
					class="zb-el-progressBars__barProgress"
					:data-width="item.fill_percentage !== undefined ? item.fill_percentage : 50"
				>
				</span>
			</span>
		</li>

		<slot name="end" />
	</ul>
</template>

<script>
export default {
	name: 'ProgressBars',
	props: ['options', 'element', 'api'],
	data() {
		return {
			resetAnimation: false,
		};
	},
	computed: {
		bars() {
			return this.options.bars || [];
		},
		barsWidth() {
			const barsWidth = (this.options.bars || []).map(item => {
				return item.fill_percentage;
			});

			return barsWidth.join('');
		},
	},
	watch: {
		barsWidth() {
			this.doResetAnimation();
		},
		'options.transition_delay'() {
			this.doResetAnimation();
		},
	},
	mounted() {
		window.requestAnimationFrame(() => {
			this.runScript();
		});
	},
	methods: {
		doResetAnimation() {
			this.resetAnimation = true;
			this.runScript().then(() => {
				this.resetAnimation = false;
			});
		},
		runScript() {
			return new Promise((resolve, reject) => {
				window.requestAnimationFrame(() => {
					new window.zbScripts.progressBars(this.$el);
					resolve();
				});
			});
		},
	},
};
</script>

<style lang="scss">
.znpb-progressBars--resetAnimation .zb-el-progressBars__barProgress {
	width: 0 !important;
	transition: none !important;
}
</style>
