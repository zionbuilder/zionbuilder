<template>
	<div v-if="userData" class="znpb-admin-user-template">
		<UserTemplate
			:permission="permissionsNumber"
			:has-delete="true"
			@edit-permission="showModal = true"
			@delete-permission="deleteUserPermission(userId)"
		>
			{{ userData.name }}
		</UserTemplate>

		<Modal
			v-model:show="showModal"
			class="znpb-admin-permissions-modal"
			:width="560"
			:title="userData.name + ' ' + $translate('permissions')"
			:show-backdrop="false"
		>
			<UserModalContent :permissions="permissions" @edit-role="editUserPermission(userId, $event)" />
		</Modal>
	</div>
</template>

<script>
import { computed } from 'vue';
import { useBuilderOptionsStore, useUsersStore } from '/@/common/store';

// Components
import UserModalContent from './UserModalContent.vue';
import UserTemplate from './UserTemplate.vue';

export default {
	name: 'SingleUser',
	components: {
		UserModalContent,
		UserTemplate,
	},
	props: {
		permissions: {
			type: Object,
			required: true,
		},
		userId: {
			type: Number,
			required: true,
		},
	},
	setup(props) {
		const { getUserInfo } = useUsersStore();
		const { editUserPermission, deleteUserPermission } = useBuilderOptionsStore();
		const userData = getUserInfo(props.userId);

		const permissionsNumber = computed(() => {
			return Object.keys(props.permissions).length;
		});

		return {
			permissionsNumber,
			userData,
			editUserPermission,
			deleteUserPermission,
		};
	},
	data() {
		return {
			showModal: false,
		};
	},
};
</script>
