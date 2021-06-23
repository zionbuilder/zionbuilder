<template>
	<div class="znpb-horizontal-accordion">
		<transition name="slide-title">
			<div
				v-show="!localCollapsed"
				@click="openAccordion"
				class="znpb-horizontal-accordion__header"
				:class="{'znpb-horizontal-accordion__header--has-slots': hasHeaderSlot}"
			>
				<template v-if="!hasHeaderSlot">
					<span class="znpb-horizontal-accordion__title">
						<template v-if="!hasTitleSlot">
							<Icon
								v-if="icon"
								:icon="icon"
							/>
							<span v-html="title"></span>
						</template>
						<slot name="title"></slot>
					</span>

				</template>
				<!-- @slot Content that will be placed inside the header -->
				<slot name="header"></slot>

				<!-- @slot Actions that will be placed inside the header -->
				<div class="znpb-horizontal-accordion__header-actions">
					<slot name="actions"></slot>
					<Icon
						v-if="showTriggerArrow"
						icon="right-arrow"
					/>
				</div>
			</div>
		</transition>
		<transition name="slide-body">
			<div
				v-if="localCollapsed"
				class="znpb-horizontal-accordion__content"
			>
				<OptionBreadcrumbs
					v-if="hasBreadcrumbs && !(combineBreadcrumbs && parentAccordion)"
					:show-back-button="showBackButton"
					:breadcrumbs="breadcrumbs"
				/>

				<!-- end bred -->
				<div
					class="znpb-horizontal-accordion-wrapper znpb-fancy-scrollbar"
					:style="wrapperStyles"
				>
					<!-- @slot Content that will be placed inside the content -->
					<slot></slot>
				</div>
			</div>
		</transition>
	</div>
</template>

<script>
import { computed } from 'vue'
import { Icon } from '../Icon'
import OptionBreadcrumbs from './OptionBreadcrumbs.vue'

export default {
	name: 'HorizontalAccordion',
	provide () {
		return {
			parentAccordion: this.parentAccordion ? this.parentAccordion : this
		}
	},
	inject: {
		'parentAccordion': {
			from: 'parentAccordion',
			default: null
		}
	},
	components: {
		OptionBreadcrumbs,
		Icon
	},
	props: {
		/**
		 * If the accordion content has breadcrumbs
		**/
		hasBreadcrumbs: {
			type: Boolean,
			required: false,
			default: true
		},

		/**
		 * Collapsed state
		 */
		collapsed: {
			type: Boolean,
			required: false,
			default: false
		},
		/**
		 * Title of the accordion
		 */
		title: {
			type: String,
			required: false
		},
		/**
		 * If Title has icon behind
		 */
		icon: {
			type: String,
			required: false
		},
		/**
		 * Level Name for breadcrumbs only if no title
		 */
		level: {
			type: String,
			required: false
		},
		/**
		 * If the accordion header should show the trigger icon
		 */
		showTriggerArrow: {
			type: Boolean,
			required: false,
			default: true
		},
		/**
		 * If the breadcrumbs should show the back button
		 */
		showBackButton: {
			type: Boolean,
			required: false
		},
		/**
		 * If the breadcrumbs should show the home button
		 */
		showHomeButton: {
			type: Boolean,
			required: false
		},
		/**
		 * Breadcrumbs home text
		 */
		homeButtonText: {
			type: String,
			required: false
		},
		combineBreadcrumbs: {
			type: Boolean,
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
	data () {
		return {
			localCollapsed: this.collapsed,
			breadcrumbs: [
				{
					title: this.homeButtonText,
					callback: this.closeAccordion
				},
				{
					title: this.title
				}
			],
			breadCrumbConfig: null,
			firstChildOpen: false
		}
	},
	watch: {
		collapsed (newValue) {
			this.localCollapsed = newValue
		}
	},
	computed: {
		/**
		 * Returns the position of the pointer
		 */
		wrapperStyles () {
			const cssStyles = {}
			if (!this.combineBreadcrumbs && (this.parentAccordion === null) && this.localCollapsed && this.firstChildOpen) {
				cssStyles['overflow'] = 'hidden'
			}
			return cssStyles
		}
	},
	methods: {
		addBreadcrumb (breadcrumb) {
			// Check to see if we need to add a callback to previous item
			if (typeof breadcrumb.previousCallback === 'function') {
				const lastItem = this.breadcrumbs[this.breadcrumbs.length - 1]

				lastItem.callback = breadcrumb.previousCallback
			}
			this.breadcrumbs.push(breadcrumb)
			this.firstChildOpen = true
		},
		removeBreadcrumb (breadcrumb) {
			const breadCrumbIndex = this.breadcrumbs.indexOf(breadcrumb)
			if (breadCrumbIndex !== -1) {
				this.breadcrumbs.splice(breadCrumbIndex, 1)
				this.firstChildOpen = false
			}
		},
		closeAccordion () {
			this.localCollapsed = false

			if (this.parentAccordion !== null && this.parentAccordion) {
				this.parentAccordion.removeBreadcrumb(this.breadCrumbConfig)
			}

			this.$emit('collapse', true)
		},
		openAccordion () {
			this.localCollapsed = true

			let firstParent = this.$el.parentNode
			let higherParent = window.jQuery(firstParent).parents('.znpb-horizontal-accordion-wrapper')

			window.jQuery(higherParent).scrollTop(0)

			// Inject to existing breadcrumbs
			if (this.combineBreadcrumbs && this.parentAccordion) {
				this.injectBreadcrumbs()
			}

			this.$emit('expand', false)
		},
		injectBreadcrumbs () {
			this.breadCrumbConfig = {
				title: this.title,
				previousCallback: this.closeAccordion
			}

			this.parentAccordion.addBreadcrumb(this.breadCrumbConfig)
		}
	}
}
</script>

<style lang="scss">
.znpb-horizontal-accordion {
	width: 100%;

	&__header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 16px;
		margin-bottom: 5px;
		font-size: 13px;
		font-weight: 500;
		background: var(--zb-surface-lighter-color);
		border-radius: 3px;
		cursor: pointer;

		.znpb-accordion-title {
			margin-right: 15px;
		}
		&--has-slots {
			padding: 0;
		}

		&-actions {
			display: flex;
			align-items: center;

			& > * {
				margin-right: 10px;

				&:last-child {
					margin-right: 0;
				}
			}
		}
	}

	&__title {
		flex-grow: 1;
		padding-right: 15px;
		color: var(--zb-surface-text-active-color);
		font-size: 13px;
		transition: color 0.15s ease-in-out;

		& > .znpb-editor-icon-wrapper {
			margin-right: 15px;
			color: var(--zb-surface-icon-color);
			font-size: 22px;
		}
	}

	&__content {
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		z-index: 9;
		display: flex;
		flex-direction: column;
		height: 100%;
		background: var(--zb-surface-color);
	}
	&-wrapper {
		position: relative;
		overflow-x: hidden;
		height: 100%;

		.znpb-horizontal-accordion-wrapper {
			padding: 0;
		}
	}
}

.slide-title-enter-from {
	transform: translateX(-100%);
}
.slide-title-enter-to {
	transform: translateX(0);
}
.slide-title-leave-from {
	transform: translateX(-100%);
}

.slide-title-enter-to,
.slide-title-leave-from {
	transition: all 0.2s;
}

.slide-body-enter-from {
	transform: translateX(100%);
}
.slide-body-enter-to {
	transform: translateX(0%);
}
.slide-body-leave-from {
	transform: translateX(50%);
}
.slide-body-leave-to {
	transform: translateX(100%);
}
.slide-body-enter-to,
.slide-body-leave-from {
	transition: all 0.2s;
}
</style>
