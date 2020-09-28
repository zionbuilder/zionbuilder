<template>
	<div class="znpb-options-childs__element">
		<div class="znpb-options-childs__element-title">{{ computedOptions[itemOptionName] || 'ITEM' }}</div>
		<div class="znpb-options-childs__element-action">
			<BaseIcon
				icon="copy"
				@click.native.stop="cloneElement"
			/>
			<BaseIcon
				icon="delete"
				@click.native.stop="deleteElement"
			/>
			<BaseIcon
				icon="edit"
				@click.native.stop="editElement"
			/>
		</div>

	</div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

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
			'openPanel',
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
