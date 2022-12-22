<template>
	<li class="zb-el-tabs-nav-title" :class="{ 'zb-el-tabs-nav--active': active }">{{ title }}</li>
</template>

<script lang="ts" setup>
defineProps<{
	title: string;
	active: boolean;
}>();
</script>
