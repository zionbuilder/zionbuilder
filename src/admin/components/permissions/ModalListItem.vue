<template>
	<li class="znpb-baseSelect-list__option znpb-add-specific-permissions__list-item" @click.self="addNewUser">
		{{ user.name }}

		<Tooltip
			v-if="userPermissionsExists"
			:content="i18n.__('This user already has permissions. Click to remove', 'zionbuilder')"
		>
			<Icon icon="delete" @click.stop="deletePermission" />
		</Tooltip>

		<Loader v-if="loadingDelete" />
	</li>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { ref, computed } from 'vue';

const props = defineProps({
	user: {
		type: Object,
		required: true,
	},
});

const emit = defineEmits(['close-modal']);

const { addUser } = window.zb.store.useUsersStore();
const { getUserPermissions, addUserPermissions, deleteUserPermission } = window.zb.store.useBuilderOptionsStore();
const loadingDelete = ref(false);
const userPermissionsExists = computed(() => getUserPermissions(props.user.id));

function addNewUser() {
	// Add user data to users object
	addUser(props.user);

	// Add default User permissions to user permissions object
	addUserPermissions(props.user);

	emit('close-modal', true);
}

function deletePermission() {
	deleteUserPermission(props.user.id);
}
</script>

<style lang="scss">
.znpb-add-specific-permissions__list-item {
	position: relative;
	span:first-child {
		display: flex;
	}
	.znpb-admin-small-loader {
		position: relative;
		top: auto;
		right: 0;
		left: auto;
		width: 14px;
		height: 14px;
		margin-right: 0;
	}
}
</style>
