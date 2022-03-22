<template>
	<span v-znpb-tooltip="discardChangesTitle" class="znpb-options-has-changes-wrapper">
		<span
			class="znpb-options__has-changes"
			@click.stop="$emit('remove-styles')"
			@mouseover="showIcon = true"
			@mouseleave="showIcon = false"
		>
			<span v-if="!showIcon"></span>
			<Icon v-else class="znpb-options-has-changes-wrapper__delete" icon="close" :size="6" />
		</span>
	</span>
</template>

<script lang="ts">
export default {
	name: 'ChangesBullet',
};
</script>

<script lang="ts" setup>
import { ref } from 'vue';

import { translate } from '@zb/i18n';

interface IProps {
	discardChangesTitle?: string;
}

withDefaults(defineProps<IProps>(), {
	discardChangesTitle: (() => translate('discard_changes')) as unknown as string,
});

defineEmits(['remove-styles']);

const showIcon = ref(false);
</script>

<style lang="scss">
.znpb-options__has-changes {
	position: relative;
	top: 0;
	display: flex;
	justify-content: center;
	align-items: center;
	width: 10px;
	height: 10px;
	cursor: pointer;
}

.znpb-options__has-changes::after {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: #31d783;
	border-radius: 50%;
	transition: transform 0.15s, background-color 0.1s;
}

.znpb-options__has-changes:hover::after {
	background-color: var(--zb-surface-icon-color);
	transform: scale(1.6);
}

.znpb-options-has-changes-wrapper {
	margin-left: 5px;
	cursor: pointer;
	.znpb-options-has-changes-wrapper__delete {
		z-index: 9;
		justify-content: center;

		stroke: var(--zb-surface-text-active-color);
		.zion-svg-inline {
			margin: 0;
		}
	}

	.znpb-editor-icon-wrapper {
		width: 9px;
		height: 9px;

		stroke-width: 5px;
	}
}
.znpb-horizontal-accordion__header--has-slots {
	.znpb-editor-icon-wrapper.znpb-options-has-changes-wrapper__delete {
		padding: 0;
	}
}
</style>
