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
		:key="activePopup.key"
	>
		<template #content>
			<ColumnTemplates
				:element="activePopup.element"
				@close="hideAddElementsPopup"
			/>
		</template>
	</Tooltip>
</template>

<script>
import { watch } from 'vue'
import { useAddElementsPopup, useWindows } from '@composables'

// Components
import ColumnTemplates from './ColumnTemplates.vue'

export default {
	name: "AddElementPopup",
	components: {
		ColumnTemplates
	},
	setup () {
		const { activePopup, hideAddElementsPopup } = useAddElementsPopup()
		const { addEventListener, removeEventListener } = useWindows()

		watch(activePopup, (newValue) => {
			if (newValue) {
				addEventListener('scroll', hideAddElementsPopup)
			} else {
				removeEventListener('scroll', hideAddElementsPopup)
			}
		})

		return {
			activePopup,
			hideAddElementsPopup
		}
	}
}
</script>