<template>
	<ul ref="root" :class="{ 'znpb-progressBars--resetAnimation': resetAnimation }">
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

<script lang="ts" setup>
import { ref, computed, onMounted, watch } from 'vue';

const props = defineProps<{
	options: {
		bars: {
			title: string;
			fill_percentage: number;
		}[];
		transition_delay: number;
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();

const root = ref(null);

const bars = computed(() => {
	return props.options.bars ? props.options.bars : [];
});

const barsWidth = computed(() => {
	const barsWidth = (props.options.bars || []).map(item => {
		return item.fill_percentage;
	});

	return barsWidth.join('');
});

const resetAnimation = ref(false);

watch(barsWidth, () => {
	doResetAnimation();
});

watch(
	() => props.options.transition_delay,
	() => {
		doResetAnimation();
	},
);

onMounted(() => {
	window.requestAnimationFrame(() => {
		runScript();
	});
});

function doResetAnimation() {
	resetAnimation.value = true;
	runScript().then(() => {
		resetAnimation.value = false;
	});
}

function runScript() {
	return new Promise(resolve => {
		window.requestAnimationFrame(() => {
			if (root.value) {
				new window.zbScripts.progressBars(root.value);
			}
			resolve(true);
		});
	});
}
</script>

<style lang="scss">
.znpb-progressBars--resetAnimation .zb-el-progressBars__barProgress {
	width: 0 !important;
	transition: none !important;
}
</style>
