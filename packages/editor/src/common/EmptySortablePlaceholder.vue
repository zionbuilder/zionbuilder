<template>
	<div class="znpb-empty-placeholder">
		<div class="znpb-empty-placeholder__add-element-button">
			<Tooltip
				tooltip-class="hg-popper--big-arrows"
				placement='auto'
				:show="showColumnTemplates"
				append-to="body"
				trigger="click"
				:close-on-outside-click="true"
				:close-on-escape="true"
				@hide="onAddElementsHide"
				@show="onAddColumnsShow"
				strategy="fixed"
				tag="div"
			>

				<Icon
					icon="plus"
					:rounded="true"
					class="znpb-empty-placeholder__tour-icon"
					key="12"
				/>

				<template #content>
					<ColumnTemplates
						slot="content"
						:empty-sortable="true"
						@close-popper="showColumnTemplates=false"
						:parentUid="parentUid"
						:data="data"
						key="123"
					/>
				</template>
			</Tooltip>
		</div>
	</div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { Tooltip } from '@zb/components'
import ColumnTemplates from './ColumnTemplates.vue'
import eventMarshall from './eventMarshall'

export default {
	name: 'EmptySortablePlaceholder',
	components: {
		ColumnTemplates,
		Tooltip
	},
	props: {
		parentUid: {
			type: String,
			required: true
		},
		data: {
			type: Object,
			required: false
		}
	},
	data () {
		return {
			showColumnTemplates: false
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
			if (this.data) {
				this.showColumnTemplates = newValue === this.data.uid
			}
		}
	},
	methods: {
		...mapActions([
			'setShouldOpenAddElementsPopup',
			'setActiveShowElementsPopup'
		]),

		onAddElementsHide () {
			this.showColumnTemplates = false

			this.resetAddElementsPopup()

			// remove active element popup
			if (this.data && this.getActiveShowElementsPopup === this.data.uid) {
				this.setActiveShowElementsPopup(null)
			}
		},
		onAddColumnsShow () {
			this.showColumnTemplates = true

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
				this.showColumnTemplates = true
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
