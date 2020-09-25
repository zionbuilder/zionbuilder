<template>
	<span
		class="znpb-label"
		:class="{ [`znpb-label--${type}`]: type }"
	>
		{{text}}
	</span>
</template>

<script>
export default {
	name: 'Label',
	props: ['text', 'type']
}
</script>

<style lang="scss">
@import "~@zionbuilder/css-variables";

.znpb-label {
	display: inline-block;
	padding: 5px 8px 5px;
	margin: 0 20px;
	color: #fff;
	font-size: 9px;
	font-weight: 700;
	letter-spacing: .5px;
	background: $primary-color;
	border-radius: 2px;

	&--error {
		background: $error;
	}

	&--warning {
		color: #fff;
		background: $warning;
	}
	&--pro {
		margin-right: 0;
		margin-left: auto;
		color: #fff;
		text-transform: uppercase;
		background: $warning;
	}
}
</style>
