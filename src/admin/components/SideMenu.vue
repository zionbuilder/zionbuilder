<template>
	<ListAnimation class="znpb-admin-side-menu" tag="ul">
		<SideMenuItem v-for="(menuItem, key) in menuItems" :key="key" :menu-item="menuItem" :base-path="`${basePath}`">
			{{ menuItem.title }}

			<!-- Item label -->
			<Label v-if="menuItem.label" v-bind="menuItem.label" class="znpb-label--pro" />
		</SideMenuItem>
	</ListAnimation>
</template>

<script>
import SideMenuItem from './SideMenuItem.vue';

export default {
	name: 'SideMenu',
	components: {
		SideMenuItem,
	},
	props: {
		menuItems: {
			type: Array,
			required: true,
		},
		basePath: {
			type: String,
			required: false,
		},
	},
	data() {
		return {
			activeMenuItem: this.menuItems.length > 0 ? this.menuItems[0] : false,
		};
	},
	methods: {
		isActive(path) {
			return this.$router.currentRoute.path.indexOf(path) !== -1;
		},
	},
};
</script>

<style lang="scss">
.znpb-admin-side-menu {
	overflow: hidden;
	width: 100%;
	margin: 0;
}
</style>
