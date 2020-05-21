<template>
	<div class="znpb-horizontal-accordion">
		<transition name="slide-title">
			<div
				v-show="!localCollapsed"
				@click="openAccordion"
				class="znpb-horizontal-accordion__header"
				:class="{'znpb-horizontal-accordion__header--has-slots': $slots.header}"
			>
				<template v-if="!$slots.header">
					<span class="znpb-horizontal-accordion__title">
						<BaseIcon
							v-if="icon"
							:icon="icon"
						/>
						{{title}}
						<slot name="title"></slot>
					</span>

				</template>
				<!-- @slot Content that will be placed inside the header -->
				<slot name="header"></slot>

				<!-- @slot Actions that will be placed inside the header -->
				<div class="znpb-horizontal-accordion__header-actions">
					<slot name="actions"></slot>
					<BaseIcon
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
				<div class="znpb-horizontal-accordion-wrapper znpb-fancy-scrollbar">
					<!-- @slot Content that will be placed inside the content -->
					<slot></slot>
				</div>
			</div>
		</transition>
	</div>
</template>

<script>
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
		OptionBreadcrumbs
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
			breadCrumbConfig: null
		}
	},
	watch: {
		collapsed (newValue) {
			this.localCollapsed = newValue
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
		},
		removeBreadcrumb (breadcrumb) {
			const breadCrumbIndex = this.breadcrumbs.indexOf(breadcrumb)
			if (breadCrumbIndex !== -1) {
				this.breadcrumbs.splice(breadCrumbIndex, 1)
			}
		},
		closeAccordion () {
			this.localCollapsed = false

			this.$el.parentNode.style.overflow = null

			if (this.parentAccordion !== null && this.parentAccordion) {
				this.parentAccordion.removeBreadcrumb(this.breadCrumbConfig)
			}

			this.$emit('collapse', true)
		},
		openAccordion () {
			this.localCollapsed = true

			// Inject to existing breadcrumbs
			if (this.combineBreadcrumbs && this.parentAccordion) {
				this.injectBreadcrumbs()
			}

			this.$el.parentNode.style.overflow = 'hidden'
			this.$el.parentNode.scrollTop = 0

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
		background: #f1f1f1;
		border-radius: 3px;
		cursor: pointer;

		.znpb-accordion-title {
			margin-right: 15px;
		}
		&--has-slots {
			padding: 0;
		}

		&:hover .znpb-horizontal-accordion__title {
			color: darken($font-color, 30);
		}

		&-actions {
			display: flex;

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
		color: $surface-active-color;
		font-size: 13px;
		transition: color .15s ease-in-out;

		& > .znpb-editor-icon-wrapper {
			margin-right: 15px;
			color: #c6c6c6;
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
		background: #fff;
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

.slide-title-before-enter {
	transform: translateX(-100%);
}
.slide-title-enter {
	transform: translateX(-100%);
}
.slide-title-after-enter {
	transform: translateX(0);
}
.slide-title-before-leave {
	transform: translateX(-100%);
}
.slide-title-leave-to {
	transform: translateX(-100%);
}
.slide-title-enter-active, .slide-title-leave-active {
	transition: all .2s;
}

.slide-body-enter {
	transform: translateX(100%);
}
.slide-body-after-enter {
	transform: translateX(0%);
}
.slide-body-before-leave {
	transform: translateX(50%);
}
.slide-body-leave-to {
	transform: translateX(100%);
}
.slide-body-enter-active, .slide-body-leave-active {
	transition: all .2s;
}
</style>
