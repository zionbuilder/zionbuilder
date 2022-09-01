<template>
	<div class="znpb-options-children__element">
		<div class="znpb-options-children__element-inner">
			<div class="znpb-options-children__element-title">{{ element.options[itemOptionName] || 'ITEM' }}</div>
			<div class="znpb-options-children__element-action">
				<Icon icon="copy" @click.stop="element.duplicate()" />
				<Icon
					icon="delete"
					:class="{ 'znpb-options-children__element-actionDeleteInactive': !showDelete }"
					@click.stop="onDelete"
				/>
				<Icon icon="edit" @click.stop="UIStore.editElement(element)" />
			</div>
		</div>
	</div>
</template>

<script>
import { useUIStore } from '/@/editor/store';

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
		const UIStore = useUIStore();

		function onDelete() {
			if (props.showDelete) {
				props.element.delete();
			}
		}

		return {
			UIStore,
			onDelete,
		};
	},
};
</script>
<style lang="scss">
.znpb-options-children__element {
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
