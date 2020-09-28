<template>
	<div
		v-if="isTabActive"
		class="znpb-tab__wrapper"
	>
		<!-- @slot Content that will be added inside button -->
		<slot></slot>
	</div>
</template>

<script>
export default {
	name: 'Tab',
	inject: {
		Tabs: {
			default () {
				return {}
			}
		}
	},
	props: {
		/**
		 * Tab Name
		 */
		name: {
			required: true,
			type: String
		},
		/**
		 * Tab id
		 */
		id: {
			required: false,
			default: null
		},
		active: {

		}
	},
	data () {
		return {
			isActive: false
		}
	},
	created () {
		this.Tabs.registerTab(this.computedTabInfo)
	},
	beforeUnmount () {
		this.Tabs.unRegisterTab(this.computedTabInfo)
	},
	computed: {
		isTabActive() {
			return this.Tabs.computedActiveTab === this.id
		},
		computedTabInfo() {
			return {
				name: this.name,
				id: this.id
			}
		},
		tabId () {
			return this.id ? this.id : this.name.toLowerCase().replace(/ /g, '-')
		},
		_isTab () {
			// For parent sniffing of child
			return true
		}
	},
	// TODO: check if this is needed
	updated () {
		if (this.$slots.title) {
			this.Tabs.updateTitleSlots(this)
		}
	}
}
</script>
