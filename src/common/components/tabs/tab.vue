<template>
	<div
		v-if="isActive"
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
		}
	},
	data () {
		return {
			isActive: false
		}
	},
	computed: {
		tabId () {
			return this.id ? this.id : this.name.toLowerCase().replace(/ /g, '-')
		},
		_isTab () {
			// For parent sniffing of child
			return true
		}
	},
	updated () {
		if (this.$slots.title) {
			this.Tabs.updateTitleSlots(this)
		}
	}
}
</script>
