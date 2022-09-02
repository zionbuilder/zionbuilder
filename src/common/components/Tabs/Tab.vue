<template>
	<slot name="title" />
	<slot />
</template>

<script lang="ts">
export default {
	name: 'Tab',
};
</script>

<script lang="ts" setup>
defineProps<{
	name: string;
	icon?: string;
	id?: string;
	active?: boolean;
}>();
</script>
