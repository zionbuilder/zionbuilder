<template>
	<div class="znpb-admin-content znpb-admin-content--center" functional>
		<div class="znpb-admin-content__center">
			<slot></slot>
		</div>
		<div class="znpb-admin-content__right">
			<BaseIcon icon="infobig" class="znpb-admin-right-info"/>
			<slot name="right"></slot>
		</div>
	</div>
</template>
<script>
import { BaseIcon } from '@zionbuilder/components'

export default {
	name: 'PageTemplate',
	components: {
		BaseIcon
	}
}
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
		display: flex;
		flex-basis: 240px;
		flex-shrink: 0;
		padding: 0 0 40px 0;
		border-right: 1px solid $surface-variant;
	}

	&__right {
		display: flex;
		flex-basis: 300px;
		flex-grow: 0;
		flex-shrink: 0;
		padding: 30px 30px 30px 15px;
		border-left: 1px solid $surface-variant;

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
