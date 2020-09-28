<template>
	<div class="znpb-system-list-plugins">
		<h2 class="znpb-system-subtitle">{{categoryData.category_name}}</h2>
		<div class="znpb-system-plugins-wrapper">
			<div v-for="(value,i) in categoryData.values" :key="i" class="znpb-system-plugins">
				<h3 class="znpb-system-plugins__item">{{value.name}}</h3>
				<h4 class="znpb-system-plugins__item"> {{value.version}} </h4>
				<h5 class="znpb-system-plugins__item"> {{value.author}} </h5>

			</div>
		</div>
	</div>
</template>

<script>

export default {
	name: 'SystemPlugins',
	props: {
		categoryData: {
			type: Object,
			required: true
		}
	}
}
</script>
<style lang="scss" scoped>
.znpb-system-plugins {
	display: grid;
	padding: 20px;
	box-shadow: 0 5px 10px 0 rgba(164, 164, 164, .1);
	border: 1px solid $surface-variant;
	border-radius: 3px;

	grid-gap: 10px;
	grid-template-columns: 1fr 1fr;
	&-wrapper {
		display: grid;
		padding-bottom: 40px;

		grid-gap: 20px;
		grid-template-columns: 1fr 1fr;
	}
	&__item {
		margin: 0;
		font-weight: 500;
		line-height: 20px;
	}
	h3 {
		font-size: 15px;
		font-weight: 500;
	}
	h4 {
		align-self: center;
		color: $font-color;
		font-weight: 500;

		justify-self: end;
	}
	h5 {
		color: $font-color;
		font-size: 13px;
		font-weight: 400;
	}
}
</style>
