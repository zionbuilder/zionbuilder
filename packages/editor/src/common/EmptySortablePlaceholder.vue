<template>
	<div class="znpb-empty-placeholder">
		<div
			class="znpb-empty-placeholder__add-element-button"
			@click="toggleAddElementsPopup"
			ref="addElementsPopupButton"
		>
			<Icon
				icon="plus"
				:rounded="true"
				class="znpb-empty-placeholder__tour-icon"
			/>
		</div>
	</div>
</template>

<script>
import { ref } from 'vue'
import { mapActions, mapGetters } from 'vuex'
import ColumnTemplates from './ColumnTemplates.vue'
import eventMarshall from './eventMarshall'
import { useAddElementsPopup } from '@zb/editor'

export default {
	name: 'EmptySortablePlaceholder',
	components: {
		ColumnTemplates
	},
	props: {
		element: Object
	},
	setup(props) {
		const showColumnTemplates = ref(false)
		const addElementsPopupButton = ref(null)

		function toggleAddElementsPopup () {
			const { showAddElementsPopup } = useAddElementsPopup()
			showAddElementsPopup(props.element, addElementsPopupButton)
		}

		return {
			toggleAddElementsPopup,
			addElementsPopupButton,
			showColumnTemplates
		}
	},
	computed: {
		...mapGetters([
			'shouldOpenAddElementsPopup',
			'getElementFocus',
			'getActiveShowElementsPopup'
		])
	},
	watch: {
		getActiveShowElementsPopup (newValue, oldValue) {
			if (this.element) {
				this.showColumnTemplates.value = newValue === this.element.uid
			}
		}
	},
	methods: {
		...mapActions([
			'setShouldOpenAddElementsPopup',
			'setActiveShowElementsPopup'
		]),

		onAddElementsHide () {
			this.showColumnTemplates.value = false

			this.resetAddElementsPopup()

			// remove active element popup
			if (this.element && this.getActiveShowElementsPopup === this.element.uid) {
				this.setActiveShowElementsPopup(null)
			}
		},
		onAddColumnsShow () {
			this.showColumnTemplates.value = true

			if (eventMarshall.getActiveTooltip) {
				eventMarshall.getActiveTooltip.showColumnTemplates = false
			}

			eventMarshall.addActiveTooltip(this)
		},
		resetAddElementsPopup () {
			if (eventMarshall.getActiveTooltip && eventMarshall.getActiveTooltip === this) {
				eventMarshall.reset()
			}
		}
	},
	mounted () {
		if (this.shouldOpenAddElementsPopup) {
			// Use a timeout because the pop-up triggers a clickoutside event and hides the tooltip
			setTimeout(() => {
				this.showColumnTemplates.value = true
				this.setShouldOpenAddElementsPopup(false)
			}, 10)
		}
	},
	beforeUnmount () {
		this.resetAddElementsPopup()
	}
}
</script>

<style lang="scss">
.znpb-empty-placeholder {
	position: relative;
	display: flex;
	justify-content: center;
	align-items: center;
	width: 100%;
	height: 100%;
	min-height: 80px;

	&__add-element-button {
		position: relative;
		width: 34px;
		height: 34px;
		color: $surface;
		font-size: 10px;
		font-size: 14px;
		line-height: 1 !important;
		border-radius: 50%;
		// transition: all .2s;

		&::before {
			@extend %iconbg;
			background: $column-color;
			transition: all .2s;
		}

		.znpb-element__wrapper &::before {
			background-color: $elements-toolbox-color;
		}
		.zb-column &::before {
			background-color: $column-color;
		}

		.zb-section > .zb-section__innerWrapper > .znpb-empty-placeholder
		&::before {
			background-color: $section-color;
		}

		&:hover {
			&::before {
				transform: scale(1.1);
			}
		}
		.znpb-editor-icon-wrapper {
			position: relative;
			width: 34px;
			height: 34px;
			cursor: pointer;
		}
	}
}
</style>
