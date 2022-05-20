<template>
	<transition-group :tag="tag" name="znpb-list-animate" appear>
		<slot></slot>
	</transition-group>
</template>

<script lang="ts" setup>
interface iProps {
	tag?: string;
}

withDefaults(defineProps<iProps>(), {
	tag: 'div',
});
</script>

<style lang="scss">
.znpb-list-animate-enter-to,
.znpb-list-animate-leave-from {
	transition: all 0.3s;
}
.znpb-list-animate-enter-from,
.znpb-list-animate-leave-to {
	transform: translateX(50px);
	opacity: 0;
}
</style>
