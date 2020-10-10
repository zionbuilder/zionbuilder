<template>

	<Tooltip
		v-if="activeElementConfig.active"
		tooltip-class="hg-popper--big-arrows"
		placement='auto'
		:show="true"
		append-to="body"
		trigger="click"
		:close-on-outside-click="true"
		:close-on-escape="true"
		:popperRef="activeElementConfig.selector"
		@hide="close"
	>
		<template #content>
			<ColumnTemplates
				:empty-sortable="true"
				:data="activeElementConfig.element"
				:element="activeElementConfig.element"
				@added-element="close"
			/>
		</template>
	</Tooltip>
</template>

<script>
import ColumnTemplates from './ColumnTemplates.vue'

export default {
	name: "AddElementPopup",
	computed: {
		activeElementConfig () {
			return this.$zb.editor.interactions.addElementPopup.activePopup
		}
	},
	components: {
		ColumnTemplates
	},
	methods: {
		close () {
			this.$zb.editor.interactions.addElementPopup.hide()
		}
	}
}
</script>