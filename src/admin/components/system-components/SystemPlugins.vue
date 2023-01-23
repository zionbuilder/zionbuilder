<template>
	<div class="znpb-system-list-plugins">
		<h2 class="znpb-system-subtitle">{{ categoryData.category_name }}</h2>
		<div class="znpb-system-plugins-wrapper">
			<div v-for="(value, i) in categoryData.values" :key="i" class="znpb-system-plugins">
				<h3 class="znpb-system-plugins__item">{{ value.name }}</h3>
				<h4 class="znpb-system-plugins__item">{{ value.version }}</h4>
				<h5 class="znpb-system-plugins__item">{{ value.author }}</h5>
			</div>
		</div>
	</div>
</template>

<script lang="ts" setup>
defineProps<{
	categoryData: {
		category_name: string;
		values: {
			name: string;
			version: string;
			author: string;
		}[];
	};
}>();
</script>
<style lang="scss">
.znpb-system-plugins {
	display: flex;
	flex-wrap: wrap;
	padding: 20px;
	box-shadow: 0 5px 10px 0 var(--zb-surface-shadow);
	border: 1px solid var(--zb-surface-border-color);
	border-radius: 3px;

	@media (max-width: 767px) {
		&:not(:last-child) {
			margin-bottom: 20px;
		}
	}

	&-wrapper {
		display: grid;
		padding-bottom: 40px;

		grid-gap: 20px;
		grid-template-columns: 1fr 1fr;

		@media (max-width: 767px) {
			display: block;
		}
	}
	&__item {
		margin: 0;
		font-weight: 500;
		line-height: 20px;
	}
	h3 {
		font-size: 15px !important;
		font-weight: 500;
		margin-bottom: 15px !important;
	}
	h4 {
		align-self: center;
		margin-left: auto;
		color: var(--zb-surface-text-color);
		font-weight: 500;
		margin-bottom: 15px !important;
	}
	h5 {
		width: 100%;
		margin-top: 8px;
		color: var(--zb-surface-text-color);
		font-size: 13px;
		font-weight: 400;
		margin-bottom: 0 !important;
	}
}
</style>
