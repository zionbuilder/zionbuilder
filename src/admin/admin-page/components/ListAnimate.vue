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
.znpb-list-animate-enter-active, .znpb-list-animate-leave-active {
	transition: all .3s;
}
.znpb-list-animate-enter, .znpb-list-animate-leave-to {
	transform: translateX(50px);
	opacity: 0;
}

</style>
