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
			:title="userData.name + ' ' + i18n.__('Permissions', 'zionbuilder')"
			:show-backdrop="false"
		>
			<UserModalContent :permissions="permissions" @edit-role="editUserPermission(userId, $event)" />
		</Modal>
	</div>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { computed, ref } from 'vue';

// Components
import UserModalContent from './UserModalContent.vue';
import UserTemplate from './UserTemplate.vue';

const props = defineProps({
	permissions: {
		type: Object,
		required: true,
	},
	userId: {
		type: Number,
		required: true,
	},
});

const showModal = ref(false);

const { getUserInfo } = window.zb.store.useUsersStore();
const { editUserPermission, deleteUserPermission } = window.zb.store.useBuilderOptionsStore();
const userData = getUserInfo(props.userId);

const permissionsNumber = computed(() => {
	return Object.keys(props.permissions).length;
});
</script>
