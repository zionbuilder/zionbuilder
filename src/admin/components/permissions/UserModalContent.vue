<template>
	<div>
		<OptionsForm v-model="modelValue" :schema="schema" />
	</div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
	permissions: Record<string, unknown>;
}>();
const emit = defineEmits(['edit-role']);

const schema = window.ZnPbAdminPageData.schemas.permissions;

const modelValue = computed({
	get() {
		return props.permissions;
	},
	set(newValue) {
		emit('edit-role', newValue);
	},
});
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
