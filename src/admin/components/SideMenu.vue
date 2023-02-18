<template>
	<ListAnimation class="znpb-admin-side-menu" tag="ul">
		<SideMenuItem v-for="(menuItem, key) in menuItems" :key="key" :menu-item="menuItem" :base-path="`${basePath}`">
			{{ menuItem.meta.title }}

			<!-- Item label -->
			<Label v-if="menuItem.meta.label" v-bind="menuItem.meta.label" class="znpb-label--pro" />
		</SideMenuItem>
	</ListAnimation>
</template>

<script lang="ts" setup>
import { RouteRecordNormalized } from 'vue-router';
import SideMenuItem from './SideMenuItem.vue';

withDefaults(
	defineProps<{
		menuItems: RouteRecordNormalized[];
		basePath?: string;
	}>(),
	{
		basePath: '',
	},
);
</script>

<style lang="scss">
.znpb-admin-side-menu {
	overflow: hidden;
	width: 100%;
	margin: 0;
}
</style>
