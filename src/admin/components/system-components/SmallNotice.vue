<template>
	<Tooltip v-if="message" placement="top" class="znpb-admin-system-notice-wrapper" :close-delay="150">
		<template #content>
			<!-- eslint-disable-next-line vue/no-v-html -->
			<div v-html="message"></div>
		</template>
		<Icon :icon="iconType" class="znpb-admin-system-notice" :class="`znpb-admin-system-notice--${icon}`" />
	</Tooltip>
	<Icon
		v-else
		:icon="iconType"
		class="znpb-admin-system-notice znpb-admin-system-notice--no-tooltip"
		:class="`znpb-admin-system-notice--${icon}`"
	/>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
	icon: string;
	message?: string;
}>();

const iconType = computed(() => {
	if (props.icon === 'warning' || props.icon === 'not_ok') {
		return 'warning';
	} else return 'info';
});
</script>

<style lang="scss">
.znpb-admin-system-notice {
	color: var(--zb-secondary-text-color);
	font-size: 16px;

	&--no-tooltip {
		width: 18px;
		height: 18px;
		margin-left: 7px;
	}

	&-wrapper {
		display: block;
		width: 18px;
		height: 18px;
		margin-left: 7px;
	}

	&--not_ok {
		color: var(--zb-warning-color);
		cursor: pointer;
	}
	&--ok {
		color: var(--zb-success-color);
	}
	&--warning {
		color: var(--zb-error-color);
		cursor: pointer;
	}
}
</style>
