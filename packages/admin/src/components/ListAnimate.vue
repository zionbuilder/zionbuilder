<template>
	<transition-group
		:tag="tag"
		name="znpb-list-animate"
		appear
	>
		<slot></slot>
	</transition-group>
</template>

<script>
export default {
	name: 'ListAnimate',
	props: {
		tag: {
			type: String,
			required: false,
			default: 'div'
		}
	},
	data () {
		return {}
	}
}
</script>

<style lang="scss">
.znpb-list-animate-enter-to, .znpb-list-animate-leave-from {
	transition: all .3s;
}
.znpb-list-animate-enter-from, .znpb-list-animate-leave-to {
	transform: translateX(50px);
	opacity: 0;
}
</style>
