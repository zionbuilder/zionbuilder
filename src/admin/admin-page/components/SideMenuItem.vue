<template>
	<router-link
		class="znpb-admin-side-menu__item"
		:class="{'znpb-admin__side-menu-item--active': isActive}"
		:to="`${basePath}/${menuItem.path}`"
	>
		<slot></slot>
		<SideMenu
			v-if="menuItem.children && isActive"
			:menu-items="menuItem.children"
			:animate="true"
			:base-path="`${basePath}/${menuItem.path}`"
		></SideMenu>
	</router-link>
</template>

<script>
export default {
	name: 'SideMenuItem',
	props: {
		menuItem: {
			type: Object,
			required: true
		},
		basePath: {
			type: String,
			required: false
		}
	},
	computed: {
		isActive () {
			const routerPath = this.$route.path
			return routerPath.indexOf(this.menuItem.path) !== -1
		}
	}
}
</script>

<style lang="scss">
.znpb-admin__wrapper {
	a.znpb-admin-side-menu__item {
		color: $font-color;
	}
}

.znpb-admin-side-menu {
	& & {
		padding-top: 15px;
	}

	&__item {
		position: relative;
		display: block;
		display: flex;
		flex-wrap: wrap;
		align-items: center;
		padding: 24px 30px;
		margin: 0;
		font-size: 13px;
		font-weight: 500;
		cursor: pointer;

		.znpb-label--warning {
			position: absolute;
			right: 35px;
		}
	}

	& & &__item {
		&::before {
			content: "";
			display: inline-block;
			width: 10px;
			height: 2px;
			margin-right: 5px;
			background: #ccc;
		}

		&:last-child {
			padding-bottom: 0;
		}

		.znpb-label--pro {
			right: 0;
		}
	}
}

.znpb-admin-content > .znpb-admin-side-menu > .znpb-admin-side-menu__item {
	font-size: 14px;
	border-bottom: 1px solid $surface-variant;

	&::after {
		content: "";
		position: absolute;
		top: 28px;
		right: 20px;
		width: 5px;
		height: 5px;
		margin-left: auto;
		border: 2px solid $font-color;
		border-top: 0;
		border-left: 0;
		transform: rotate(-45deg);
	}

	&.znpb-admin__side-menu-item--active::after, &:hover::after {
		border-color: $surface-active-color;
	}
}

a.znpb-admin-side-menu__item {
	&.router-link-active, &--active, &:hover {
		color: $surface-active-color;
		background: $surface;
	}

	& & {
		padding: 15px 0;
		color: $font-color;
		font-weight: 500;

		&.router-link-active, &:hover {
			color: $surface-active-color;
		}
	}
}
</style>
