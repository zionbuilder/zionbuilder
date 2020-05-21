<template>
	<div
		class="znpb-scroll-list-wrapper"
		:class="{'znpb-scroll-list-wrapper--loading': loading}"
	>
		<div
			@scroll="onScroll"
			class="znpb-fancy-scrollbar znpb-scroll-list-container"
		>
			<slot></slot>
		</div>
		<transition name="fadeFromBottom">
			<Loader v-if="loading" />
		</transition>
	</div>
</template>

<script>
export default {
	name: 'ListScroll',
	props: {
		loading: {
			type: Boolean,
			required: false,
			default: true
		}
	},
	data () {
		return {}
	},
	methods: {
		onScroll (event) {
			if (event.target.scrollHeight - event.target.scrollTop === event.target.clientHeight) {
				this.$emit('scroll-end')
			}
		}
	}
}
</script>

<style lang="scss">
// Transitions
.fadeFromBottom-enter-active, .fadeFromBottom-leave-active {
	transition: opacity .5s;
}
.fadeFromBottom-enter, .fadeFromBottom-leave-to {
	opacity: 0;
}

.znpb-scroll-list-wrapper {
	position: relative;

	&--loading {
		padding-bottom: 75px;
	}
}

.znpb-scroll-list-container {
	overflow: auto;
}
</style>
