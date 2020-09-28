<template>
	<div class="znpb-pannel-accordion">
		<div
			class="znpb-pannel-accordion__header"
			@click="toggle"
		>
			<p class="znpb-pannel-accordion__header-title">{{title}}</p>
			<BaseIcon
				class="znpb-option-group-selector__clone-icon"
				:icon=" this.expanded ? 'minus' : 'plus'"
			></BaseIcon>
		</div>
		<OptionsForm
			v-if="child_options && expanded"
			class="znpb-option__type-option-accordion"
			ref="accordionOption"
			:schema="child_options"
			v-model="valueModel"
		/>
	</div>
</template>

<script>
export default {
	name: 'PanelAccordion',
	props: {
		value: {},
		child_options: {
			type: Object,
			required: false
		},
		title: {
			type: String
		},
		collapsed: {
			type: Boolean,
			default: false
		}
	},
	data () {
		return {
			expanded: !this.collapsed,
			height: null
		}
	},
	computed: {
		valueModel: {
			get () {
				return this.value || {}
			},
			set (newValue) {
				this.$emit('input', newValue)
			}
		}
	},
	methods: {
		toggle () {
			this.expanded = !this.expanded
		}
	}
}
</script>

<style lang="scss">
.znpb-pannel-accordion__header {
	top: 0;
	display: flex;
	justify-content: space-between;
	width: calc(100% + 40px);
	padding: 9px 20px;
	margin: 0 -20px 10px -20px;
	background-color: $surface-variant;
	cursor: pointer;
}

.znpb-pannel-accordion {
	position: relative;
	width: 100%;
	.znpb-pannel-accordion__header-title {
		color: $surface-active-color;
		font-size: 13px;
		font-weight: 500;
	}
}

.znpb-options-form-wrapper.znpb-option__type-option-accordion {
	padding: 10px 0 0;
	transition: all .3s ease-in-out;
	.znpb-input-type--dimensions {
		padding-bottom: 10px;
	}
}
</style>
