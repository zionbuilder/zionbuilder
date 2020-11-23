<template>
	<div
		v-if="active"
		class="znpb-tab__wrapper"
	>
		<!-- @slot Content that will be added inside button -->
		<slot></slot>
	</div>
</template>

<script>
import { inject, computed } from 'vue'

export default {
	name: 'Tab',
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
			type: Boolean
		}
	}
}
</script>
