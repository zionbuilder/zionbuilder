<template>
	<div class="znpb-admin-content-wrapper">
		<div class="znpb-admin-content znpb-admin-content--left">
			<span class="znpb-admin-side-menu-trigger js-side-menu-trigger" @click="responsiveOpen = !responsiveOpen">
				<span>
					<span></span>
				</span>
			</span>
			<SideMenu
				:class="{ 'znpb-admin-side-menu--open': responsiveOpen }"
				:menu-items="childMenus"
				:base-path="basePath"
			></SideMenu>
		</div>
		<router-view></router-view>
	</div>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const responsiveOpen = ref(false);

const basePathConfig = computed(() => {
	const currentRoute = useRoute();
	const router = useRouter();
	const routes = router.getRoutes();

	if (currentRoute.matched.length > 0) {
		const path = currentRoute.matched[0].path;
		return routes.find(route => route.path === path) || false;
	}

	return false;
});

const basePath = computed(() => {
	return basePathConfig.value ? basePathConfig.value.path : false;
});

const childMenus = computed(() => {
	if (basePathConfig.value !== false && basePathConfig.value.children) {
		return basePathConfig.value.children;
	}

	return [];
});
</script>
