<template>
	<div class="znpb-scroll-list-wrapper" :class="{ 'znpb-scroll-list-wrapper--loading': loading }">
		<div ref="listScrollRef" class="znpb-fancy-scrollbar znpb-scroll-list-container" @wheel.passive="onScroll">
			<slot></slot>
		</div>
		<transition name="fadeFromBottom">
			<Loader v-if="loading" />
		</transition>
	</div>
</template>

<script lang="ts">
export default {
	name: 'ListScroll',
};
</script>

<script lang="ts" setup>
import { ref, Ref } from 'vue';
withDefaults(
	defineProps<{
		loading?: boolean;
	}>(),
	{
		loading: true,
	},
);

const emit = defineEmits(['scroll-end']);

const listScrollRef: Ref<HTMLDivElement | null> = ref(null);

function onScroll(event: MouseEvent) {
	if (
		listScrollRef.value!.scrollHeight - Math.round(listScrollRef.value!.scrollTop) ===
		listScrollRef.value!.clientHeight
	) {
		emit('scroll-end');
	}
}
</script>

<style lang="scss">
.znpb-scroll-list-wrapper {
	position: relative;
	display: flex;
	flex-direction: column;
	min-height: 0;
}
.znpb-scroll-list-container {
	overflow: auto;
}
</style>
