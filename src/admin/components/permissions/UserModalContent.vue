<template>
	<div class="znpb-single-role-modal-wrapper znpb-fancy-scrollbar">
		<div class="znpb-permission-wrapper">
			<Tooltip :content="$translate('check_access_to_editor')" class="znpb-single-role-modal-title-wrapper">
				<h4 class="znpb-single-role-modal-title">
					{{ $translate('access_to_editor') }}
				</h4>
			</Tooltip>
			<div class="znpb-checkbox-list znpb-checkbox-list--vertical">
				<InputCheckbox v-model="allowConfig" :option-value="allowConfig" :label="$translate('access_to_all_editor')" />
			</div>
		</div>

		<UpgradeToPro
			v-if="!isPro"
			:message_title="$translate('manage_users_permissions_title')"
			:message_description="$translate('manage_users_permissions_access_free')"
		/>
		<template v-else>
			<div class="znpb-content-permission-wrapper">
				<h4 class="znpb-single-role-modal-title">{{ $translate('content') }}</h4>
				<InputCheckbox
					v-model="contentConfig"
					:option-value="contentConfig"
					:label="$translate('edit_only_content')"
					:disabled="allowConfig === false"
				/>
			</div>
			<div class="znpb-content-permission-wrapper">
				<h4 class="znpb-single-role-modal-title">{{ $translate('post_types') }}</h4>
				<InputCheckboxGroup
					v-model="postModel"
					:options="dataSets.post_types"
					:min="0"
					:disabled="allowConfig === false"
				/>
			</div>
			<div class="znpb-content-permission-wrapper">
				<h4 class="znpb-single-role-modal-title">{{ $translate('features') }}</h4>
				<InputCheckboxGroup
					v-model="featureModel"
					:options="featuresConfig"
					:min="0"
					:disabled="allowConfig === false"
				/>
			</div>
		</template>
	</div>
</template>

<script>
import { storeToRefs } from 'pinia';
import { useDataSetsStore } from '/@/common/store';

export default {
	name: 'UserModalContent',
	props: {
		permissions: {
			type: Object,
			required: true,
		},
	},
	setup() {
		const { dataSets } = storeToRefs(useDataSetsStore());

		return {
			dataSets,
		};
	},
	computed: {
		isPro() {
			return window.ZnPbAdminPageData.is_pro_active;
		},
		allowConfig: {
			get() {
				return this.permissions.allowed_access;
			},
			set(newValue) {
				const updatedValue = {
					...this.permissions,
					allowed_access: newValue,
				};
				this.$emit('edit-role', updatedValue);
			},
		},
		featuresConfig() {
			let result = [
				{
					id: 'header_builder',
					name: 'Header builder',
				},
				{
					id: 'footer_builder',
					name: 'Footer builder',
				},
			];
			return result;
		},
		contentConfig: {
			get() {
				return this.permissions.permissions.only_content;
			},
			set(newValue) {
				const updatedValue = {
					...this.permissions,
					permissions: {
						...this.permissions.permissions,
						only_content: newValue,
					},
				};
				this.$emit('edit-role', updatedValue);
			},
		},
		featureModel: {
			get() {
				return this.permissions.permissions.features;
			},
			set(newValue) {
				const updatedValue = {
					...this.permissions,
					permissions: {
						...this.permissions.permissions,
						features: newValue,
					},
				};
				this.$emit('edit-role', updatedValue);
			},
		},
		postModel: {
			get() {
				return this.permissions.permissions.post_types;
			},
			set(newValue) {
				const updatedValue = {
					...this.permissions,
					permissions: {
						...this.permissions.permissions,
						post_types: newValue,
					},
				};
				this.$emit('edit-role', updatedValue);
			},
		},
	},
};
</script>
<style lang="scss">
.znpb-single-role-modal-wrapper {
	display: flex;
	flex-wrap: wrap;
	justify-content: space-between;
	overflow-y: auto;
	padding: 20px;
}
.znpb-content-permission-wrapper {
	flex-basis: calc(33.33333% - 20px);
	max-width: calc(33.33333% - 20px);
	margin-bottom: 20px;

	@media (max-width: 575px) {
		flex-basis: 100%;
		max-width: 100%;
	}

	.znpb-single-role-modal-title {
		margin-bottom: 20px;
	}

	.znpb-checkmark-option {
		padding: 6px 5px 6px 10;
		font-weight: 500;
		text-align: left;
	}
}
.znpb-permission-wrapper {
	display: flex;
	align-items: center;
	width: 100%;
	margin-bottom: 30px;
}

.znpb-single-role-modal-title-wrapper {
	display: flex;
	align-items: center;
	margin-right: 20px;

	.znpb-single-role-modal-title {
		margin: 0;
	}
}

.znpb-single-role-modal-title {
	margin: 0;
	color: var(--zb-surface-text-active-color);
	font-size: 14px;
	font-weight: 500;
	line-height: 1;
}
</style>
