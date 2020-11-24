<template>
	<div class="znpb-column-size">
		<div class="znpb-column-size-options">
			<div
				class="znpb-column-size__option"
				v-for="(option, index) in options"
				:key="index"
				@click="valueModel = option.id"
				:style="{'flex': option.name === 'auto' ? `0 0 ${100}%` : 1}"
				:class="{
					'znpb-column-size__option--active': option.id === valueModel
				}"
			>
				{{option.name}}
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'ColumnSize',
	props: {
		options: {
			type: Array
		},
		modelValue: {
			type: [String, Number],
			required: false
		}
	},
	computed: {
		valueModel: {
			get () {
				return this.modelValue
			},
			set (newValue) {
				this.$emit('update:modelValue', newValue)
			}
		}
	},
	methods: {
	}
}
</script>

<style lang="scss">
.znpb-column-size-options {
	display: flex;
	flex-wrap: wrap;
	overflow: hidden;
	padding: 3px;
	background-color: $surface-variant;
	border-radius: 3px;
}
.znpb-column-size__option {
	padding: 15px 0;
	font-size: 13px;
	font-weight: 500;
	text-align: center;
	border-radius: 2px;
	cursor: pointer;

	&:hover {
		background-color: darken($surface-variant, 3%);
	}
}
.znpb-column-size__option--active {
	color: $surface;
	background-color: $secondary;

	&:hover {
		color: $surface;
		background-color: $secondary;
	}
}
</style>
