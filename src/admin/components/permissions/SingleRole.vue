<template>
	<div class="znpb-admin-user-template">
		<UserTemplate :permission="permissionsNumber" @edit-permission="showModal = true">
			{{ role.name }}
		</UserTemplate>

		<Modal
			v-model:show="showModal"
			class="znpb-admin-permissions-modal"
			:width="560"
			:title="role.name + ' ' + $translate('permissions')"
			:show-backdrop="false"
		>
			<UserModalContent :permissions="permissionConfig" @edit-role="editRolePermission(role.id, $event)" />
		</Modal>
	</div>
</template>

<script>
import { ref, computed } from 'vue';
import { useBuilderOptions } from '@common/composables';

// Components
import UserModalContent from './UserModalContent.vue';
import UserTemplate from './UserTemplate.vue';

export default {
	name: 'SingleRole',
	components: {
		UserTemplate,
		UserModalContent,
	},
	props: {
		role: {
			type: Object,
			required: true,
		},
	},
	setup(props) {
		const { getRolePermissions, editRolePermission } = useBuilderOptions();

		const isPro = window.ZnPbAdminPageData.is_pro_active;
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
				for (let i in permissionConfig.value.permissions.features) {
					permNumber++;
				}
				for (let i in permissionConfig.value.permissions.post_types) {
					permNumber++;
				}

				return permNumber;
			}
		});

		return {
			isPro,
			showModal,
			permissionConfig,
			permissionsNumber,
			editRolePermission,
		};
	},
};
</script>
