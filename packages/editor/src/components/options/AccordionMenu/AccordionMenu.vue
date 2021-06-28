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

		<template
			v-if="hasHeaderSlot"
			#header
		>
			<slot name="header"></slot>
		</template>

		<template
			v-if="hasTitleSlot"
			#title
		>
			<slot name="title"></slot>
		</template>

		<template
			v-else
			#title
		>
			<Icon
				v-if="$attrs.icon"
				:icon="$attrs.icon"
			/>
			<span v-html="title"></span>
			<ZionLabel
				v-if="label"
				:text="label.text"
				:type="label.type"
			/>
			<ChangesBullet
				v-if="showChanges && hasChanges"
				@remove-styles="$emit('update:modelValue', null)"
			/>
		</template>

		<template #actions>
			<slot name="actions" />
		</template>

		<OptionsForm
			class="znpb-option-layout__menu-options-form"
			:schema="child_options"
			v-model="optionsValue"
			:show-changes="showChanges"
			v-if="expanded"
		/>
	</HorizontalAccordion>
</template>

<script>
import { computed } from 'vue'

import ZionLabel from '../../../common/Label.vue'

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
	components: {
		ZionLabel
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
		modelValue: {
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
	setup (props, { slots }) {
		const hasHeaderSlot = computed(() => !!slots.header)
		const hasTitleSlot = computed(() => !!slots.title)

		return {
			hasHeaderSlot,
			hasTitleSlot
		}
	},
	computed: {
		valueModel () {
			return this.modelValue || {}
		},
		hasChanges () {
			return this.$parent.hasChanges
		},
		optionsValue: {
			get () {
				return this.modelValue || {}
			},
			set (newValue) {
				this.$emit('update:modelValue', newValue)
			}
		}
	},
	data () {
		return {
			showBreadcrumbs: this.parentAccordion === null,
			expanded: false
		}
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
	beforeUnmount () {
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

.znpb-options-form-wrapper .znpb-option-layout__menu {
	padding: 0;
}

.znpb-option-layout__menu {
	padding: 0 5px;

	&
		> .znpb-horizontal-accordion__content
		> .znpb-horizontal-accordion-wrapper
		> &-options-form {
		overflow: visible;
		padding-top: 0;
	}

	& > .znpb-horizontal-accordion__header > {
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
}
.znpb-option-layout__menu > .znpb-horizontal-accordion__header {
	& > .znpb-horizontal-accordion__title {
		color: var(--zb-surface-text-active-color);
		font-size: 13px;
		font-weight: 500;
		text-transform: capitalize;
	}
	&:hover {
		.znpb-horizontal-accordion__title {
			& .znpb-editor-icon {
				path {
					fill: var(--zb-surface-icon-color);
				}
				circle {
					fill: var(--zb-surface-icon-color);
				}
			}
		}
	}
}
.znpb-accordion-expanded {
	overflow: hidden !important;
}
</style>
