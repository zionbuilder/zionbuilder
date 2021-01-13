<template>
	<div class="znpb-admin-content znpb-admin-content--center" functional>
		<div class="znpb-admin-content__center">
			<slot></slot>
		</div>
		<div class="znpb-admin-content__right">
			<Icon icon="infobig" class="znpb-admin-right-info" />
			<slot name="right"></slot>
		</div>
	</div>
</template>

<script>
export default {
	name: "PageTemplate"
};
</script>

<style lang="scss">
.znpb-admin-content-wrapper {
	display: flex;
	flex-shrink: 0;
	background: $surface;
}
.znpb-admin-content {
	&--center {
		display: flex;
		flex-grow: 1;
	}
	&--left {
		position: relative;
		display: flex;
		flex-basis: 240px;
		flex-shrink: 0;
		padding: 0 0 40px 0;
		border-right: 1px solid $surface-variant;

		@media (max-width: 1399px) {
			width: 40px;
			flex-basis: 40px;
			align-items: flex-start;
			justify-content: center;

			> .znpb-admin-side-menu {
				display: none;
				position: absolute;
				top: 0;
				width: 200px;
				background: #fff;
				left: 39px;
				z-index: 10;
				box-shadow: 0px 4px 40px -20px #00000063;
				border-radius: 3px;
				border: 1px solid #f1f1f1;
			}
		}
	}

	&__right {
		display: flex;
		flex-basis: 300px;
		flex-grow: 0;
		flex-shrink: 0;
		padding: 30px 30px 30px 15px;
		border-left: 1px solid $surface-variant;

		@media (max-width: 1399px) {
			flex-basis: 200px;
		}

		@media (max-width: 991px) {
			display: none;
		}

		p.znpb-admin-info-p {
			margin-top: 0;
			color: $font-color;
			font-family: $font-stack;
			font-size: 13px;
		}
		.znpb-admin-right-info {
			flex-shrink: 0;
			width: 20px;
			height: 20px;
			margin-top: 5px;
			margin-right: 10px;
			border: 2px solid darken($border-color, 10%);
			border-radius: 50%;
			.zion-icon.zion-svg-inline {
				margin: 0 auto;
				font-size: 8px;
			}
		}
	}

	&__center {
		position: relative;
		flex-grow: 1;
		min-height: 500px;
		padding: 30px 30px 30px;
		background: $surface;
	}
}

</style>
