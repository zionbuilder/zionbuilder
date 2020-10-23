<template>
	<div class="znpb-options-childs__element">
		<div class="znpb-options-childs__element-title">{{ computedOptions[itemOptionName] || 'ITEM' }}</div>
		<div class="znpb-options-childs__element-action">
			<Icon
				icon="copy"
				@click.stop="cloneElement"
			/>
			<Icon
				icon="delete"
				@click.stop="deleteElement"
			/>
			<Icon
				icon="edit"
				@click.stop="editElement"
			/>
		</div>

	</div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { usePanels } from '@data'

export default {
	name: 'SingleChild',
	props: {
		elementUid: {
			type: String,
			required: true
		},
		itemOptionName: {
			type: String,
			required: true
		}
	},
	setup (props) {
		const { openPanel } = usePanels()

		return {
			openPanel
		}
	},
	computed: {
		...mapGetters([
			'getElementData'
		]),
		elementData () {
			return this.getElementData(this.elementUid)
		},
		computedOptions () {
			return this.elementData.options ? this.elementData.options : {}
		}
	},
	methods: {
		...mapActions([
			'setActiveElement',
			'deleteElement'
		]),
		editElement () {
			this.setActiveElement(this.elementUid)
			this.openPanel('PanelElementOptions')
		},
		deleteElement () {
			this.$emit('delete', this.elementUid)
		},
		cloneElement () {
			this.$emit('clone', this.elementUid)
		}
	}
}
</script>
<style lang="scss">
.znpb-options-childs__element {
	z-index: 9;
	display: flex;
	justify-content: space-between;
	align-items: center;
	width: 100%;
	background-color: $surface-variant;
	border-radius: 3px;
	cursor: pointer;
	.znpb-editor-icon-wrapper {
		padding: 5px;
	}

	&-title {
		color: $surface-active-color;
		font-size: 13px;
		font-weight: 500;
		line-height: 1.4;
	}

	&-action {
		flex-shrink: 0;
	}
}
</style>
