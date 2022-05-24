<template>
	<div class="znpb-options-childs__element">
		<div class="znpb-options-childs__element-inner">
			<div class="znpb-options-childs__element-title">{{ element.options[itemOptionName] || 'ITEM' }}</div>
			<div class="znpb-options-childs__element-action">
				<Icon icon="copy" @click.stop="element.duplicate()" />
				<Icon
					icon="delete"
					:class="{ 'znpb-options-childs__element-actionDeleteInactive': !showDelete }"
					@click.stop="onDelete"
				/>
				<Icon icon="edit" @click.stop="editElement(element)" />
			</div>
		</div>
	</div>
</template>

<script>
import { useEditElement } from '../../../composables';
import { useUIStore } from '../../../store';

export default {
	name: 'SingleChild',
	props: {
		element: {
			type: Object,
			required: true,
		},
		itemOptionName: {
			type: String,
			required: false,
		},
		showDelete: {
			type: Boolean,
			default: true,
		},
	},
	setup(props) {
		const { openPanel } = useUIStore();
		const { editElement } = useEditElement();

		function onDelete() {
			if (props.showDelete) {
				props.element.delete();
			}
		}

		return {
			openPanel,
			editElement,
			element: props.element,
			onDelete,
		};
	},
};
</script>
<style lang="scss">
.znpb-options-childs__element {
	background-color: var(--zb-surface-lighter-color);

	&-inner {
		z-index: 9;
		display: flex;
		justify-content: space-between;
		align-items: center;
		width: 100%;
		padding: 12px 15px;
		margin-bottom: 5px;
		border-radius: 3px;
		cursor: pointer;
	}

	.znpb-editor-icon-wrapper {
		padding: 5px;
	}

	&-title {
		color: var(--zb-surface-text-active-color);
		font-size: 13px;
		font-weight: 500;
		line-height: 1.4;
	}

	&-action {
		flex-shrink: 0;

		&DeleteInactive {
			opacity: 0.4;
		}
	}
}
</style>
