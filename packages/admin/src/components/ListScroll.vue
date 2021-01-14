<template>
	<div
		class="znpb-scroll-list-wrapper"
		:class="{'znpb-scroll-list-wrapper--loading': loading}"
	>
		<div
			@wheel="onScroll"
			class="znpb-fancy-scrollbar znpb-scroll-list-container"
			ref="listScrollRef"
		>
			<slot></slot>
		</div>
		<transition name="fadeFromBottom">
			<Loader v-if="loading" />
		</transition>
	</div>
</template>

<script>
import { ref } from 'vue'

export default {
	name: 'ListScroll',
	props: {
		loading: {
			type: Boolean,
			required: false,
			default: true
		}
	},
	setup (props, { emit }) {
		const listScrollRef = ref(null)

		function onScroll (event, delta) {
			if (listScrollRef.value.scrollHeight - listScrollRef.value.scrollTop === listScrollRef.value.clientHeight) {
				emit('scroll-end')
			}
		}

		return {
			listScrollRef,
			onScroll
		}
	}
}
</script>

<style lang="scss">
// Transitions
.fadeFromBottom-enter-to, .fadeFromBottom-leave-from {
	transition: opacity .5s;
}
.fadeFromBottom-enter-from, .fadeFromBottom-leave-to {
	opacity: 0;
}

.znpb-scroll-list-wrapper {
	position: relative;
}

.znpb-scroll-list-container {
	overflow: auto;
}
</style>
