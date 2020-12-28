<template functional>
	<div class="znpb-loader-wrapper">
		<div
			class="znpb-loader"
			:style="{
				height: `${size}px`,
				width: `${size}px`
			}"
		>
		</div>
	</div>
</template>

<script>
export default {
	name: 'Loader',
	props: {
		size: {
			type: Number,
			default: 24,
			required: false
		}
	}
}
</script>

<style lang="scss">
.znpb-loader-wrapper {
	display: flex;
	justify-content: center;
	align-items: center;
	width: 100%;
}
.znpb-loader {
	position: relative;
	left: -2px;

	&:before, &:after {
		@extend %loading;
		box-sizing: border-box;
		border: 2px solid transparent;
	}

	&:after {
		border-right-color: lighten($surface-headings-color, 10%);
		border-bottom-color: lighten($surface-headings-color, 10%);
		animation: Rotate .6s linear infinite;
	}
}
</style>
