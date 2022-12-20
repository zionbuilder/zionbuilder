<template>
	<div class="znpb-radio-image-container">
		<BaseInput
			v-if="useSearch"
			v-model="searchKeyword"
			:placeholder="searchText"
			:clearable="true"
			class="znpb-radio-image-search"
		/>

		<div class="znpb-radio-image-wrapper znpb-fancy-scrollbar">
			<ul class="znpb-radio-image-list" :class="[`znpb-radio-image-list--columns-${columns}`]">
				<li
					v-for="(option, index) in visibleItems"
					:key="index"
					class="znpb-radio-image-list__item-wrapper"
					@click="changeValue(option.value)"
				>
					<div
						class="znpb-radio-image-list__item"
						:class="{ ['znpb-radio-image-list__item--active']: modelValue === option.value }"
					>
						<img v-if="option.image" :src="option.image" class="znpb-image-wrapper" />
						<span v-if="option.class" class="znpb-radio-image-list__preview-element animated" :class="option.value">
						</span>
						<Icon v-if="option.icon" class="znpb-radio-image-list__icon" :icon="option.icon" />
					</div>
					<span v-if="option.name" class="znpb-radio-image-list__item-name">
						{{ option.name }}
					</span>
				</li>

				<li v-if="useSearch && visibleItems.length === 0" class="znpb-radio-image-search--noItems">
					{{ __('No items found', 'zionbuilder') }}
				</li>
			</ul>
		</div>
	</div>
</template>

<script lang="ts">
export default {
	name: 'InputRadioImage',
};
</script>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { ref, computed } from 'vue';
import Icon from '../Icon/Icon.vue';

const props = withDefaults(
	defineProps<{
		modelValue: string;
		options: { name?: string; value: string; class?: string; image?: string; icon?: string }[];
		columns?: 1 | 2 | 3 | 4;
		useSearch?: boolean;
		searchText?: string;
	}>(),
	{
		useSearch: true,
		columns: 3,
		searchText: () => {
			return __('Search', 'zionbuilder') as unknown as string;
		},
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: string): void;
}>();

const searchKeyword = ref('');

const visibleItems = computed(() => {
	if (searchKeyword.value.length > 0) {
		return props.options.filter(
			option => option.name && option.name.toLowerCase().includes(searchKeyword.value.toLowerCase()),
		);
	}

	return props.options;
});

function changeValue(newValue: string) {
	emit('update:modelValue', newValue);
}
</script>

<style lang="scss">
@function list-grid($col) {
	$fr: '1fr ';
	$grid: '';

	@for $i from 1 through $col {
		$grid: str-insert($grid, '#{$fr}', (str-length($grid) + 1));
	}

	@return (unquote($grid));
}
.znpb-input-wrapper.znpb-input-type--radio_image {
	margin-bottom: 20px;

	> .znpb-input-content {
		width: auto;
	}
}
.znpb-radio-image-wrapper {
	padding: 20px 20px;
	color: var(--zb-surface-text-color);
	border: 1px solid var(--zb-surface-border-color);
	border-radius: 3px;
}
.znpb-radio-image-list__item-wrapper {
	display: flex;
	flex-direction: column;
	width: 82px;
	padding-bottom: 20px;
}
.znpb-radio-image-list__preview-element {
	display: block;
	width: 12px;
	height: 12px;
	background: var(--zb-surface-text-color);
	border-radius: 50%;
	animation-duration: 1s;
	animation-iteration-count: infinite;
	animation-fill-mode: both;
}
ul.znpb-radio-image-list {
	display: grid;
	max-height: 300px;

	column-gap: 12px;
}
.znpb-radio-image-list {
	&__icon {
		animation-duration: 1s;
		animation-iteration-count: infinite;
		animation-fill-mode: both;
	}
	&__item {
		position: relative;
		display: flex;
		flex-direction: column;
		flex-wrap: wrap;
		justify-content: space-around;
		align-items: center;
		overflow: hidden;
		height: 82px;
		padding: 15px;
		margin-bottom: 8px;
		background-color: var(--zb-surface-color);
		box-shadow: 0 5px 10px 0 var(--zb-surface-shadow);
		border: 1px solid var(--zb-surface-lighter-color);
		border-radius: 3px;
		transition: all 0.2s ease;
		cursor: pointer;

		&:hover {
			box-shadow: 0 5px 10px 0 var(--zb-surface-shadow-hover);
		}
		&--active {
			border: 2px solid var(--zb-secondary-color);
		}

		&:hover &__item-name,
		&--active &__item-name {
			color: var(--zb-secondary-text-color);
		}
	}

	&__item-name {
		font-size: 12px;
		font-weight: 500;
		line-height: 1.3;
		text-transform: capitalize;
	}

	&--columns-1 {
		width: 100%;
	}
	&--columns-2 {
		grid-template-columns: list-grid(2);
	}
	&--columns-3 {
		grid-template-columns: list-grid(3);
	}
	&--columns-4 {
		grid-template-columns: list-grid(4);
	}

	&__has-image {
		padding: 0;
		border: 0;
		.znpb-radio-image-list__item-name {
			text-align: center;
		}
		.znpb-image-wrapper {
			background-repeat: no-repeat;
			background-position: center center;
			background-size: cover;
		}
		&:hover,
		&.znpb-radio-image-list__item--active {
			.znpb-image-wrapper {
				background-color: var(--zb-secondary-color);

				background-blend-mode: multiply;
			}
		}
	}
	&__icon-text-content {
		display: flex;
		flex-direction: column;
	}
	.znpb-radio-image-list__item-name,
	.znpb-editor-icon-wrapper {
		text-align: center;
		transition: all 0.2s ease;
	}
}

.znpb-radio-image-list__item {
	&:hover {
		.znpb-radio-image-list__item-name,
		.znpb-editor-icon-wrapper {
			color: var(--zb-surface-color);
		}
	}
}
.znpb-radio-image-list__item--active {
	.znpb-radio-image-list__item-name,
	.znpb-editor-icon-wrapper {
		color: var(--zb-surface-color);
	}
}

.znpb-radio-image-search {
	margin-bottom: 20px;
}

.znpb-radio-image-search--noItems {
	text-align: center;

	grid-column: 1 / -1;
}
</style>
