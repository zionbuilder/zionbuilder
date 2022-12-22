<template>
	<div
		class="zion-radio-group"
		:class="{
			[`zion-radio-group--${layout}`]: layout,
		}"
	>
		<!-- @slot Content for radiogroup <RadioGroupItem> -->
		<slot></slot>
	</div>
</template>
<script>
export default {
	name: 'InputRadioGroup',
	props: {
		/**
		 * Layout type: row,column
		 */
		layout: {
			type: String,
			required: false,
		},
	},
	data() {
		return {};
	},
};
</script>
<style lang="scss">
.zion-radio-group {
	display: flex;
	flex-wrap: wrap;
	justify-content: flex-start;
	align-items: center;
	width: 100%;

	&--row {
		flex-direction: row;

		& .znpb-radio-item {
			margin-right: 20px;

			&:last-child {
				margin-right: 0;
			}
		}
	}

	&--column {
		flex-direction: column;
		align-items: start;

		& .znpb-radio-item {
			margin-bottom: 7px;
		}
	}
}
</style>
