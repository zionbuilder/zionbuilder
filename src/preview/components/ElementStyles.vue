<template>
	<component :is="'style'">
		{{ styles }}
	</component>
</template>

<script lang="ts" setup>
defineProps<{
	styles: string;
}>();
</script>
