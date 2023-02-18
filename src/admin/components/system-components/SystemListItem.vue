<template>
	<div class="znpb-system-list">
		<h3 class="znpb-system-list__item">{{ data.name }}</h3>
		<h4 class="znpb-system-list__item">
			{{ data.value }}
			<SmallNotice v-if="data.icon" :icon="data.icon" :message="data.message" />
		</h4>
	</div>
</template>

<script lang="ts" setup>
import SmallNotice from './SmallNotice.vue';

defineProps<{
	data: {
		name: string;
		value: string;
		icon?: string;
		message?: string;
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
	font-size: 13px;
	position: relative;
	display: flex;
	align-items: center;
	margin-bottom: 30px;

	&__item {
		font-size: 13px !important;
		flex-basis: 50%;
		margin-bottom: 0;
		color: var(--zb-surface-text-color);
		line-height: 1.4;

		@media (max-width: 767px) {
			&:first-child {
				margin-bottom: 5px;
			}
		}
	}
	h3 {
		font-size: 12px;
		font-weight: 700;
		text-transform: uppercase;
		margin-bottom: 0 !important;
	}
	h4 {
		position: relative;
		display: flex;
		align-items: center;
		font-size: 14px;
		font-weight: 400;
		margin-bottom: 0 !important;
	}
}
</style>
