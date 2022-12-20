<template>
	<div class="znpb-admin-content-wrapper znpb-permissions-wrapper">
		<div class="znpb-admin-content znpb-admin-content--left znpb-admin-content--hiddenXs"></div>
		<div class="znpb-admin-content__permission-container">
			<PageTemplate>
				<Loader v-if="loading" />
				<template v-else>
					<div class="znpb-admin-role-manager-wrapper">
						<h3>{{ __('Role manager', 'zionbuilder') }}</h3>
						<SingleRole v-for="(role, i) in dataSets.user_roles" :key="i" :role="role" />
					</div>
				</template>
				<template #right>
					<p class="znpb-admin-info-p">
						{{
							__(
								'Manage the permissions by selecting which users are allowed to use the page builder. Select to edit only the content, the post types such as Post, Pages, and the main features such as the header and the footer builder.',
								'zionbuilder',
							)
						}}
					</p>
				</template>
			</PageTemplate>

			<PageTemplate>
				<UpgradeToPro
					v-if="!is_pro_active"
					:info-text="proLink"
					:message_title="__('specific users control', 'zionbuilder')"
					:message_description="__('Want to give control to specific users?', 'zionbuilder')"
				/>
				<template v-else-if="!loading">
					<div class="znpb-admin-user-specific-wrapper">
						<h3>{{ __('User specific permissions', 'zionbuilder') }}</h3>

						<EmptyList v-if="Object.entries(userPermissions).length === 0" @click="showModal = true">{{
							__('no user added yet', 'zionbuilder')
						}}</EmptyList>

						<SingleUser
							v-for="(permissions, userId) in userPermissions"
							:key="userId"
							:user-id="userId"
							:permissions="permissions"
						/>
					</div>
					<div class="znpb-admin-user-specific-actions">
						<Button type="secondary" @click="showModal = true">
							<span class="znpb-add-element-icon"></span>
							{{ __('Add a User', 'zionbuilder') }}
						</Button>
						<Modal
							v-model:show="showModal"
							class="znpb-admin-permissions-modal"
							:width="560"
							:title="__('Add a User', 'zionbuilder')"
							:show-backdrop="false"
						>
							<AddUserModalContent @close-modal="showModal = false" />
						</Modal>
					</div>
				</template>
				<template #right>
					<p class="znpb-admin-info-p">
						{{
							__(
								'Manage your wordpress users permissions. Adding a new user will allow the basic permissions which can be edited afterwards.',
								'zionbuilder',
							)
						}}
					</p>
				</template>
			</PageTemplate>
		</div>
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { storeToRefs } from 'pinia';
import { ref } from 'vue';
import { useEnvironmentStore } from '@zb/store';

// Components
import SingleRole from './SingleRole.vue';
import SingleUser from './SingleUser.vue';
import AddUserModalContent from './AddUserModalContent.vue';

const { is_pro_active } = useEnvironmentStore();
const { fetchUsersData } = window.zb.store.useUsersStore();
const { getOptionValue } = window.zb.store.useBuilderOptionsStore();

const { dataSets } = storeToRefs(window.zb.store.useDataSetsStore());
const userPermissions = getOptionValue('users_permissions');

const loading = ref(true);
const showModal = ref(false);
const proLink = ref(null);

// Fetch system information from rest api
const userIds = Object.keys(userPermissions);

if (userIds.length > 0) {
	fetchUsersData(userIds).finally(() => {
		loading.value = false;
	});
} else {
	loading.value = false;
}
</script>

<style lang="scss">
.znpb-admin-permissions-modal {
	& > .znpb-modal__wrapper--full-size {
		width: 100%;
		height: 90%;
	}

	& > .znpb-modal__wrapper {
		width: calc(100% - 40px);
	}
}
.znpb-admin-content__permission-container {
	display: block;
	width: 100%;
	.znpb-admin-content--center {
		flex: 0;
	}
	.znpb-admin-content__center {
		// flex-basis: 100%;
		flex-grow: 2;
	}
	.znpb-admin-content__right {
		flex-grow: 0;
	}
}
.znpb-admin-role-manager-wrapper {
	position: relative;
	width: 100%;
	padding-bottom: 30px;
	border-bottom: 1px solid var(--zb-surface-border-color);
}
.znpb-permissions-wrapper {
	.znpb-admin-content {
		&__center {
			padding-bottom: 0;
		}
	}
}

.znpb-admin-user-specific-wrapper {
	margin-bottom: 30px;
	.znpb-empty-list__container {
		color: var(--zb-surface-text-color);
	}
}

.znpb-admin-user-specific-actions {
	.znpb-button {
		float: right;
		margin-bottom: 30px;
	}
	.znpb-add-element-icon {
		display: inline-block;
		width: 10px;
		height: 10px;
		&:before {
			height: 10px;
		}
		&:after {
			width: 10px;
		}
	}
}
</style>
