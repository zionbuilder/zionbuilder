<template>
	<div
		class="znpb-tabs"
		:class="cssClasses"
	>
		<div
			class="znpb-tabs__header"
			:class="navigationClasses"
		>
			<TabLink
				v-for="(tab, i) in tabs"
				:key="tab.tabId + i"
				@click="selectTab(tab.tabId)"
				:tab="tab"
				ref="tabLink"
			>
			</TabLink>
		</div>

		<div
			class="znpb-tabs__content"
			:class="{['znpb-fancy-scrollbar']: hasScroll.includes(activeTab) }"
		>

			<!-- @slot Content that will be added inside the tab -->
			<slot>
				"Edit Element options"
			</slot>
		</div>
	</div>
</template>

<script>
import TabLink from './tabLink.vue'

export default {
	name: 'Tabs',
	provide () {
		return {
			Tabs: this
		}
	},
	props: {
		/**
		 * Card style. Can be one of minimal, card, group
		 */
		tabStyle: {
			type: String,
			required: false,
			default: 'card'
		},
		/**
		 * Title Position. Can be one of start, center, end
		 */
		titlePosition: {
			type: String,
			required: false,
			default: 'left'
		},
		activeTab: {
			type: String
		},
		hasScroll: {
			type: Array,
			default () { return [] }
		}
	},
	computed: {
		cssClasses () {
			return {
				[`znpb-tabs--${this.tabStyle}`]: this.tabStyle
			}
		},
		navigationClasses () {
			return {
				[`znpb-tabs__header--${this.titlePosition}`]: true
			}
		}
	},
	data () {
		return {
			tabs: []
		}
	},
	components: {
		TabLink
	},
	watch: {
		activeTab (tabId) {
			this.selectTab(tabId)
		}
	},
	mounted () {
		this.getTabs()
		this.setDefaultActiveTab()
	},
	updated () {
		this.$nextTick(() => {
			this.getTabs()
			this.setDefaultActiveTab()
		})
	},
	methods: {
		/**
		 * Retrieve the list of tabs
		 */
		getTabs () {
			const tabSlots = (this.$slots.default || [])
			const tabsInstances = tabSlots
				.map(vnode => vnode.componentInstance)
				.filter(tab => tab && tab._isTab)

			// Check to see if the tabs has changed
			if (
				!(
					tabsInstances.length === this.tabs.length &&
					tabsInstances.every((tab, index) => tab === this.tabs[index])
				)
			) {
				this.tabs = tabsInstances
			}
		},
		updateTitleSlots (tab) {
			const tabLink = this.$refs.tabLink.find(link => link.tab === tab)
			if (tabLink) {
				tabLink.$forceUpdate()
			}
		},
		setDefaultActiveTab () {
			if (this.activeTab) {
				this.selectTab(this.activeTab)
			} else {
				const hasActiveTab = this.tabs.filter(tab => {
					return tab.isActive
				})

				if (hasActiveTab.length === 0 && this.tabs.length) {
					this.selectTab(this.tabs[0].tabId)
				}
			}
		},
		/**
		 * Activates a tab based on tabId
		 */
		selectTab (tabId) {
			if (tabId === this.selectedTab) {
				return
			}
			/**
			 * Activates a tab based on tabId
			 */
			this.$emit('changed-tab', tabId)
			this.selectedTab = tabId
			this.tabs.forEach(tab => {
				tab.isActive = (tab.tabId === tabId)
			})
		}
	}
}
</script>

<style lang="scss">
.znpb-tabs {
	& > &__header {
		display: flex;
		margin: 0 20px;

		& > .znpb-tabs__header-item {
			display: flex;
			justify-content: center;
			align-items: center;
			color: $font-color;
			font-size: 13px;
			font-weight: 500;
			cursor: pointer;
		}

		// Title position
		&--start {
			justify-content: start;
		}

		&--center {
			justify-content: center;
		}

		&--end {
			justify-content: flex-end;
		}
	}

	& > &__content {
		background: $surface;
	}

	// Styles
	&--minimal &__header {
		margin: 0;
		& > .znpb-tabs__header-item {
			padding: 10px 25px;

			&:first-child {
				padding-left: 0;
			}

			&:last-child {
				padding-right: 0;
			}

			&--active, &:hover {
				color: $surface-active-color;
			}
		}
	}

	&--card > &__header {
		& > .znpb-tabs__header-item {
			padding: 12.5px 20px;

			&--active, &:hover {
				color: $surface-active-color;
				background: $surface;
				border-radius: 3px 3px 0 0;
			}
		}
	}

	&--group > &__header {
		padding: 3px;
		margin: 0 0 20px 0;
		background: $surface-variant;
		border-radius: 3px;

		& > .znpb-tabs__header-item {
			display: flex;
			justify-content: center;
			align-items: center;
			flex-grow: 1;
			padding: 10px 15px;
			color: $font-color;
			font-size: 14px;
			line-height: 1;
			border-radius: 2px;

			&:hover {
				background-color: darken($surface-variant, 5%);
			}

			&--active {
				color: #fff;
				background: $secondary;

				&:hover {
					color: #fff;
					background: $secondary;
				}
			}
		}
	}
}
</style>
