<template>
	<li
		@click.self="addNewUser"
		class="znpb-baseselect-list__option znpb-add-specific-permissions__list-item"
	>
		{{user.name}}

		<Tooltip
			v-if="userPermissionsExists"
			:content="$translate('user_has_permissions_remove')"
		>
			<Icon
				icon="delete"
				@click.stop="deletePermission"
			/>
		</Tooltip>

		<Loader v-if="loadingDelete" />

	</li>
</template>

<script>
import { ref, computed } from 'vue'
import { useUsers, useBuilderOptions } from '@zionbuilder/composables'

export default {
	name: 'ModalListItem',
	props: {
		user: {
			type: Object,
			required: true
		}
	},
	setup (props, { emit }) {
		const { addUser } = useUsers()
		const { getUserPermissions, addUserPermissions, deleteUserPermission } = useBuilderOptions()
		const loadingDelete = ref(false)
		const userPermissionsExists = computed(() => getUserPermissions(props.user.id))

		function addNewUser () {
			// Add user data to users object
			addUser(props.user)

			// Add default User permissions to user permissions object
			addUserPermissions(props.user)

			emit('close-modal', true)
		}

		function deletePermission () {
			deleteUserPermission(props.user.id)
		}

		return {
			loadingDelete,
			addNewUser,
			deletePermission,
			userPermissionsExists
		}
	}
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
