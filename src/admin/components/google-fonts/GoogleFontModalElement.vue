<template>
	<div class="znpb-admin__google-fonts-modal-item">
		<div class="znpb-admin__google-fonts-modal-item-header">
			<div>{{font.family}}</div>
			<div
				v-if="! isActive"
				@click="$emit('font-selected', font)"
				class="znpb-circle-icon-line"
			>
				<Icon icon="plus"></Icon>
			</div>
			<div
				v-if="isActive"
				@click="$emit('font-removed', font.family)"
				class="znpb-circle-icon-line znpb-circle-delete"
			>
				<Icon icon="minus"></Icon>
			</div>
		</div>
		<div
			class="znpb-admin__google-fonts-modal-item-preview"
			contenteditable="true"
			:style="fontStyle"
		>{{font.family}}</div>
	</div>
</template>

<script>
import { computed } from 'vue'

export default {
	name: 'GoogleFontModalElement',
	props: {
		font: {
			type: Object,
			required: true
		},
		isActive: {
			type: Boolean,
			required: true
		}
	},
	setup (props) {
		const fontStyle = computed(() => {
			return {
				'font-family': props.font.family
			}
		})

		return {
			fontStyle
		}
	}
}
</script>

<style lang="scss">
.znpb-admin__google-fonts-modal-item {
	flex-basis: 100%;
	padding: 20px;
	margin-bottom: 10px;
	color: var(--zb-surface-text-color);
	background: var(--zb-surface-color);
	box-shadow: 0 5px 10px 0 var(--zb-surface-shadow);
	border: 1px solid var(--zb-surface-border-color);
	border-radius: 3px;

	&-preview {
		color: var(--zb-surface-text-active-color);
		font-size: 24px;
		line-height: 30px;
		word-break: break-word;
	}

	&-header {
		display: flex;
		flex-direction: row;
		justify-content: space-between;
		font-weight: 500;
	}

	.znpb-editor-icon-wrapper {
		width: 24px;
		height: 24px;
		color: var(--zb-surface-icon-color);
		font-size: 10px;
		text-align: center;
		border: 2px solid var(--zb-surface-border-color);
		border-radius: 50%;
		cursor: pointer;
	}

	&:hover {
		.znpb-editor-icon-wrapper {
			color: var(--zb-secondary-color);
			border-color: var(--zb-secondary-color);
		}
		.znpb-circle-delete {
			.znpb-editor-icon-wrapper {
				color: var(--zb-red);
				border-color: var(--zb-red);
			}
		}
	}
}
</style>
