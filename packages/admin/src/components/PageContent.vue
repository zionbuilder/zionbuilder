<template>
	<div class="znpb-admin-content-wrapper">
		<div class="znpb-admin-content znpb-admin-content--left">
			<span
				class="znpb-admin-side-menu-trigger js-side-menu-trigger"
				@click="responsiveOpen=!responsiveOpen"
			>
				<span>
					<span></span>
				</span>
			</span>
			<SideMenu
				:class="{'znpb-admin-side-menu--open': responsiveOpen}"
				:menu-items="childMenus"
				:base-path="basePath"
			></SideMenu>
		</div>
		<router-view></router-view>
	</div>
</template>

<script>
export default {
	name: 'PageContent',
	data () {
		return {
			responsiveOpen: false
		}
	},
	computed: {
		basePathConfig () {
			if (this.$route.matched.length > 0) {
				const path = this.$route.matched[0].path
				return this.$router.options.routes.find((route) => route.path === path)
			}

			return false
		},
		basePath () {
			return (this.basePathConfig) ? this.basePathConfig.path : false
		},
		childMenus () {
			if (this.basePathConfig) {
				if (this.basePathConfig.children.length > 0) {
					return this.basePathConfig.children
				}
			}

			return []
		}
	}

}
</script>
