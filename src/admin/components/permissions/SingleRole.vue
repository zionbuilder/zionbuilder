<template>
	<div class="znpb-admin-user-template">
		<UserTemplate :permission="permissionsNumber" @edit-permission="showModal = true">
			{{ role.name }}
		</UserTemplate>

		<Modal
			v-model:show="showModal"
			class="znpb-admin-permissions-modal"
			:width="560"
			:title="role.name + ' ' + i18n.__('Permissions', 'zionbuilder')"
			:show-backdrop="false"
		>
			<UserModalContent :permissions="permissionConfig" @edit-role="editRolePermission(role.id, $event)" />
		</Modal>
	</div>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { ref, computed } from 'vue';

// Components
import UserModalContent from './UserModalContent.vue';
import UserTemplate from './UserTemplate.vue';

const props = defineProps({
	role: {
		type: Object,
		required: true,
	},
});

const { getRolePermissions, editRolePermission } = window.zb.store.useBuilderOptionsStore();

const showModal = ref(false);

const permissionConfig = computed(() => getRolePermissions(props.role.id));

const permissionsNumber = computed(() => {
	let permNumber = 1;
	if (!permissionConfig.value || permissionConfig.value.allowed_access === false) {
		return 0;
	} else {
		if (permissionConfig.value.permissions.only_content === true) {
			permNumber++;
		}

		permissionConfig.value.permissions.features.forEach(() => {
			permNumber++;
		});

		permissionConfig.value.permissions.post_types.forEach(() => {
			permNumber++;
		});

		return permNumber;
	}
});
</script>
