<template>
	<div class="znpb-system-list-wrapper">
		<h2 class="znpb-system-subtitle">{{ categoryData.category_name }}</h2>
		<SystemListItem v-for="(value, i) in categoryData.values" :key="i" :data="value" />
	</div>
</template>

<script lang="ts" setup>
import SystemListItem from './SystemListItem.vue';

defineProps<{
	categoryData: {
		category_name: string;
		values: {
			name: string;
			value: string;
			status: string;
			icon?: string;
		}[];
	};
}>();
</script>
<style lang="scss">
.znpb-system-list-wrapper {
	padding-bottom: 40px;
}
.znpb-system-subtitle {
	font-size: 16px;
	font-weight: 600;
	line-height: 1;
}
.znpb-system-list {
	position: relative;
	display: flex;
	margin-bottom: 30px;

	@media (max-width: 767px) {
		flex-direction: column;
		align-items: flex-start;
	}

	&__item {
		font-size: 13px !important;
		flex-basis: 50%;
		margin-bottom: 0;
		color: var(--zb-surface-text-color);
		line-height: 1.4;
	}

	h3 {
		font-size: 12px;
		font-weight: 700;
		text-transform: uppercase;
	}
	h4 {
		font-size: 14px;
		font-weight: 400;
	}
}
</style>
