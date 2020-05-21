<template>
	<HorizontalAccordion
		class="znpb-option-layout__menu"
		:title="title"
		:icon="$attrs.icon"
		:show-back-button="true"
		:show-home-button="true"
		:home-button-text="homeButtonText || 'Options'"
		@expand="onAccordionExpanded"
		@collapse="onAccordionCollapsed"
		:has-breadcrumbs="showBreadcrumbs"
		ref="accordion"
	>
		<ZionLabel
			v-if="label"
			slot="title"
			:text="label.text"
			:type="label.type"
		/>
		<HasChanges
			slot="title"
			v-if="showChanges && hasChanges"
			@remove-styles="$emit('input', null)"
		/>
		<OptionsForm
			class="znpb-option-layout__menu-options-form"
			:schema="child_options"
			v-model="optionsValue"
			:show-changes="showChanges"
			v-if="expanded"
		></OptionsForm>
	</HorizontalAccordion>
</template>

<script>
import HasChanges from '@/editor/components/elementOptions/HasChanges.vue'
import ZionLabel from '@/editor/common/Label'

export default {
	name: 'LayoutMenu',
	inject: {
		parentAccordion: {
			default: null
		},
		showChanges: {
			default: true
		}
	},
	props: {
		child_options: {
			type: Object,
			required: false
		},
		title: {
			type: String,
			required: true
		},
		value: {
			required: false
		},
		homeButtonText: {
			type: String
		},
		add_to_parent_breadcrumbs: {
			type: Boolean,
			required: false,
			default: false
		},
		label: {
			type: Object,
			required: false
		}
	},
	computed: {
		valueModel () {
			return this.value || {}
		},
		hasChanges () {
			return this.$parent.hasChanges
		},
		optionsValue: {
			get () {
				return this.value || {}
			},
			set (newValue) {
				this.$emit('input', newValue)
			}
		}
	},
	data () {
		return {
			showBreadcrumbs: this.parentAccordion === null,
			expanded: false
		}
	},
	components: {
		HasChanges,
		ZionLabel
	},
	methods: {
		onAccordionExpanded () {
			if (this.parentAccordion !== null) {
				this.breadCrumbConfig = {
					title: this.title,
					previousCallback: this.$refs.accordion.closeAccordion
				}

				this.parentAccordion.addBreadcrumb(this.breadCrumbConfig)
			}

			this.expanded = true
		},
		onAccordionCollapsed () {
			if (this.parentAccordion !== null && this.parentAccordion) {
				this.parentAccordion.removeBreadcrumb(this.breadCrumbConfig)
			}

			this.expanded = false
		}
	},
	mounted () {
		this.$el.parentNode.classList.add('znpb-option-layout__menu-container')
	},
	beforeDestroy () {
		if (this.breadCrumbConfig) {
			this.parentAccordion.removeBreadcrumb(this.breadCrumbConfig)
		}
	}
}
</script>
<style lang="scss">
.znpb-options-form-wrapper .znpb-input-type--accordion_menu {
	margin-bottom: 0;
}

.znpb-option-layout__menu {
	padding: 0 5px;

	& > .znpb-horizontal-accordion__content > .znpb-horizontal-accordion-wrapper > &-options-form {
		overflow: visible;
		padding-top: 0;
	}

	& > .znpb-horizontal-accordion__header >  {
		.znpb-horizontal-accordion__title {
			display: flex;
			align-items: center;
		}
	}

	&-has-changes {
		display: flex;
		align-items: center;
		margin-left: 15px;
	}

	&-container {
		overflow-x: hidden;
	}

	&-options-form {
		padding: 0;
	}
}
.znpb-option-layout__menu > .znpb-horizontal-accordion__header {
	& > .znpb-horizontal-accordion__title {
		color: $surface-active-color;
		font-size: 13px;
		font-weight: 500;
		text-transform: capitalize;
	}
	&:hover {
		.znpb-horizontal-accordion__title {
			color: lighten($surface-active-color, 10%);
			& .znpb-editor-icon {
				path {
					fill: $surface-headings-color;
				}
				circle {
					fill: $surface-headings-color;
				}
			}
		}
	}
}
.znpb-accordion-expanded {
	overflow: hidden !important;
}
</style>
