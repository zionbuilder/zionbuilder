<script>
import { computed, h, ref, watch, cloneVNode, Fragment, Comment } from 'vue'

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
	setup (props, { emit, slots }) {
		const localActiveTab = ref(props.activeTab)
		const computedActiveTab = computed({
			get: () => {
				return localActiveTab.value
			},
			set: (newValue) => {
				emit('update:activeTab', newValue)

			}
		})

		watch(() => props.activeTab, (newValue) => {
			localActiveTab.value = newValue
		})

		// Computed
		const cssClasses = computed(() => {
			return {
				'znpb-tabs': true,
				[`znpb-tabs--${props.tabStyle}`]: props.tabStyle
			}
		})
		const navigationClasses = computed(() => {
			return {
				'znpb-tabs__header': true,
				[`znpb-tabs__header--${props.titlePosition}`]: true
			}
		})

		/**
		 * Activates a tab based on tabId
		 */
		function selectTab (tabId) {
			if (tabId === computedActiveTab.value) {
				return
			}

			computedActiveTab.value = tabId
			emit('changed-tab', tabId)
			localActiveTab.value = tabId
		}

		function getIdForTab (vnode) {
			const props = vnode.props
			return props && props.id ? props.id : props.name.toLowerCase().replace(/ /g, '-')
		}

		function generateTitleVNode ({ props, children }) {
			const title = children.title ? children.title() : props.name

			return h(
				'div',
				{
					title: props['tooltip-title'],
					class: {
						'znpb-tabs__header-item--active': props.active,
						[`znpb-tabs__header-item--${props.name.toLowerCase()}`]: props.name,
						'znpb-tabs__header-item': true
					},
					onClick: function () {
						selectTab(props.id)
					}
				},
				[title]
			)
		}

		function extractChilds (slotContent) {
			const items = []
			if (Array.isArray(slotContent)) {
				slotContent.forEach(vNode => {
					if (vNode.type === Fragment) {
						const fragmentItems = extractChilds(vNode.children)
						items.push(...fragmentItems)
					} else {
						if (vNode.type !== Comment) {
							items.push(vNode)
						}
					}
				})
			}

			return items
		}

		return () => {
			const childContent = slots.default()

			const childItems = extractChilds(childContent).map((vNode, i) => {
				const tabId = getIdForTab(vNode)

				if (!computedActiveTab.value && i === 0) {
					localActiveTab.value = tabId
				}

				return cloneVNode(vNode, {
					id: tabId,
					active: computedActiveTab.value === tabId
				})
			})

			const headerItems = childItems.map(vNode => {
				return generateTitleVNode(vNode)
			})

			const header = h(
				'div',
				{
					class: navigationClasses.value
				},
				headerItems
			)

			const content = h(
				'div', {
				class: {
					'znpb-tabs__content': true,
					['znpb-fancy-scrollbar']: props.hasScroll.includes(computedActiveTab.value)
				}
			},
				[childItems]
			)

			return h(
				'div',
				{
					class: cssClasses.value
				},
				[header, content]
			)
		}
	}
}
</script>

<style lang="scss">
.znpb-tabs {
	max-width: 100%;

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
