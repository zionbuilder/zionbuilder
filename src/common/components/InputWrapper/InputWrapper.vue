<template>
	<div
		class="znpb-input-wrapper"
		:class="{
			[`znpb-input-wrapper--${layout}`]: true,
		}"
		:style="computedWrapperStyle"
	>
		<div v-if="title" class="znpb-form__input-title" :class="{ 'znpb-form__input-title--fake-label': fakeLabel }">
			<span>{{ title }}</span>

			<Tooltip v-if="description" :enterable="false">
				<template #content>
					<div>{{ description }}</div>
				</template>

				<Icon icon="question-mark" />
			</Tooltip>
		</div>
		<div class="znpb-forms-input-content">
			<!-- @slot Content inside wrapper -->
			<slot></slot>
		</div>
	</div>
</template>

<script lang="ts">
export default {
	name: 'InputWrapper',
};
</script>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { computed, CSSProperties } from 'vue';
import { Tooltip } from '../tooltip';
import { Icon } from '../Icon';

const props = withDefaults(
	defineProps<{
		title?: string;
		description?: string;
		layout?: 'full' | 'full-reverse' | 'inline';
		fakeLabel?: boolean;
		schema?: {
			grow?: number;
			width?: number;
		};
	}>(),
	{
		title: '',
		description: '',
		layout: 'full',
	},
);

const computedWrapperStyle = computed(() => {
	const styles: CSSProperties = {};

	if (props.schema !== undefined) {
		if (props.schema.grow) {
			styles.flex = props.schema.grow;
		}

		if (props.schema.width) {
			styles.width = `${props.schema.width}%`;
		}
	}

	return styles;
});
</script>
<style lang="scss">
.znpb-input-wrapper.znpb-forms-input-wrapper {
	padding: 0 20px;
	margin-bottom: 15px;

	.znpb-forms-form__input-title {
		padding: 15px 15px 15px 0;
		color: var(--zb-surface-text-active-color);
		font-size: 14px;
		font-weight: 500;
		line-height: 1;

		&--fake-label {
			margin-bottom: 26px;
		}
	}

	&--full {
		width: 100%;
		.znpb-forms-form__input-title {
			color: var(--zb-surface-text-active-color);
		}
	}
	&--full-reverse {
		display: flex;
		flex-direction: column-reverse;
		.znpb-forms-input-content {
			margin-bottom: 5px;
		}
		.znpb-form__input-title {
			text-transform: uppercase;
		}
	}

	&--inline {
		display: flex;
		justify-content: space-between;
		align-items: center;

		.znpb-forms-form__input-title {
			flex-grow: 1;
			margin-bottom: 0;
		}

		.znpb-form__input-title {
			margin: 0;
		}

		.znpb-forms-input-content {
			width: auto;
		}

		& > .znpb-input-content {
			display: flex;
			justify-content: flex-end;
		}
	}
}
</style>
