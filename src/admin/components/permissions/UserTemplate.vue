<template>
	<div class="znpb-single-role">
		<h3 class="znpb-single-role__item">
			<slot></slot>
		</h3>
		<div class="znpb-single-role__permission">
			<h4 class="znpb-single-role-permission-subtitle">{{ permission }} {{ __('Permissions', 'zionbuilder') }}</h4>
		</div>
		<div class="znpb-single-role__actions">
			<Icon
				v-znpb-tooltip="__('Customize the permissions for this user', 'zionbuilder')"
				class="znpb-edit-icon-pop"
				icon="edit"
				@click="emit('edit-permission')"
			/>

			<Icon
				v-if="hasDelete"
				v-znpb-tooltip="__('Delete permissions for this user', 'zionbuilder')"
				icon="delete"
				@click="emit('delete-permission')"
			/>
		</div>
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';

withDefaults(
	defineProps<{
		permission: number;
		hasDelete?: boolean;
	}>(),
	{
		hasDelete: false,
	},
);

const emit = defineEmits(['edit-permission', 'delete-permission']);
</script>

<style lang="scss">
@import '/@/common/scss/_mixins.scss';
.znpb-admin__wrapper {
	.znpb-single-role {
		@extend %list-item-helper;
		padding: 17px 20px;

		@media (max-width: 767px) {
			flex-direction: column;
			align-items: flex-start;

			&__item,
			&__permission {
				margin-bottom: 10px !important;
			}
		}

		&__permission {
			display: flex;
			justify-content: space-between;
			flex-grow: 1;
		}
		&__actions {
			display: flex;
			font-size: 14px;
			& > .znpb-edit-icon-pop {
				margin-right: 7px;

				&:last-of-type {
					margin-right: 0;
				}
			}
			.znpb-editor-icon-wrapper {
				display: block;
				align-self: center;
				box-sizing: content-box;
				width: 15px;
				padding: 5px;
				margin-right: 10px;
				cursor: pointer;
				&:last-child {
					margin-right: 0;
				}
				&:hover {
					color: var(--zb-surface-text-hover-color);
				}
				&.znpb-edit-icon-pop {
					margin-right: 10px;
				}
			}
		}
	}
	h3.znpb-single-role__item {
		flex-basis: 0;
		flex-grow: 2;
		margin: 0;
		color: var(--zb-surface-text-active-color);
		font-size: 13px;
		font-weight: 500;
		line-height: 1;
	}
	.znpb-single-role-permission-subtitle {
		margin: 0;
		color: var(--zb-surface-text-color);
		font-size: 13px;
		font-weight: 400;
	}
}
</style>
