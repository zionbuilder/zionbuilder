<template>

	<Tooltip
		v-if="activePopup"
		tooltip-class="hg-popper--big-arrows"
		placement='auto'
		:show="true"
		append-to="body"
		trigger="click"
		:close-on-outside-click="true"
		:close-on-escape="true"
		:popperRef="activePopup.selector"
		@hide="hideAddElementsPopup"
	>
		<template #content>
			<ColumnTemplates
				:empty-sortable="true"
				:data="activePopup.element"
				:element="activePopup.element"
				@added-element="hideAddElementsPopup"
			/>
		</template>
	</Tooltip>
</template>

<script>
import ColumnTemplates from './ColumnTemplates.vue'
import { useAddElementsPopup } from '@data'

export default {
	name: "AddElementPopup",
	components: {
		ColumnTemplates
	},
	setup() {
		const { activePopup, hideAddElementsPopup } = useAddElementsPopup()

		return {
			activePopup,
			hideAddElementsPopup
		}
	}
}
</script>