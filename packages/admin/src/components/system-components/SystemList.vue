<template>
	<div class="znpb-system-list-wrapper">
		<h2 class="znpb-system-subtitle">{{categoryData.category_name}}</h2>
		<SystemListItem v-for="(value,i) in categoryData.values" :key="i" :data="value">

		</SystemListItem>
	</div>
</template>

<script>
import SystemListItem from './SystemListItem.vue'
export default {
	name: 'SystemList',
	components: {
		SystemListItem
	},
	props: {
		categoryData: {
			type: Object,
			required: true
		}
	}
}
</script>
<style lang="scss" scoped>
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

	&__item {
		flex-basis: 50%;
		margin-bottom: 0;
		color: $font-color;
		line-height: 1;
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
