<template>
	<div
		class="znpb-empty-list__container"
		:class="{'znpb-empty-list__container--no-margin' : noMargin }"
	>
		<div class="znpb-empty-list__border-top-bottom"></div>
		<div class="znpb-empty-list__border-left-right"></div>
		<div class="znpb-empty-list__content">
			<slot></slot>
		</div>
	</div>
</template>

<script>
export default {
	name: 'EmptyList',
	props: {
		noMargin: {
			type: Boolean,
			required: false,
			default: false
		}
	},
	data () {
		return {}
	}
}
</script>

<style lang="scss">

.znpb-empty-list {
	&__container {
		position: relative;
		display: flex;
		justify-content: center;
		align-items: center;
		margin-bottom: 30px;
		color: $font-color;
		font-weight: 500;
		text-align: center;
		cursor: pointer;

		&--no-margin {
			margin-bottom: 0;
		}
	}

	&__content {
		position: relative;
		padding: 30px;
	}

	&__border-top-bottom, &__border-left-right {
		position: absolute;
		width: 100%;
		height: 100%;
	}

	&__border-top-bottom {
		&:after {
			content: "";
			position: absolute;
			top: 0;
			right: 0;
			left: 0;
			width: 100%;
			height: 2px;
			background-image: linear-gradient(to right, $border-color 77%, rgba(255, 255, 255, 0) 0%);
			background-repeat: repeat-x;
			background-position: top;
			background-size: 12px 2px;
		}

		&:before {
			content: "";
			position: absolute;
			right: 0;
			bottom: 0;
			left: 0;
			width: 100%;
			height: 2px;
			background-image: linear-gradient(to right, $border-color 77%, rgba(255, 255, 255, 0) 0%);
			background-repeat: repeat-x;
			background-position: bottom;
			background-size: 12px 2px;
		}
	}

	&__border-left-right {
		&:after {
			content: "";
			position: absolute;
			top: 0;
			right: 2px;
			bottom: 0;
			left: 0;
			width: 2px;
			height: 100%;
			background-image: linear-gradient(to top, $border-color 77%, rgba(255, 255, 255, 0) 0%);
			background-repeat: repeat-y;
			background-position: bottom;
			background-size: 2px 12px;
		}

		&:before {
			content: "";
			position: absolute;
			top: 0;
			right: 0;
			bottom: 0;
			width: 2px;
			height: 100%;
			background-image: linear-gradient(to top, $border-color 77%, rgba(255, 255, 255, 0) 0%);
			background-repeat: repeat-y;
			background-position: bottom;
			background-size: 2px 12px;
		}
	}
}
</style>
