<template>
	<div class="znpb-icon-options">
		<div class="znpb-icon-trigger" :class="{ ['znpb-icon-trigger--no-icon']: !modelValue }" @click.self="open">
			<div v-if="modelValue" class="znpb-icon-options__delete">
				<span
					class="znpb-icon-preview"
					:data-znpbiconfam="modelValue.family"
					:data-znpbicon="unicode(modelValue.unicode)"
					@click.passive.stop="showModal = true"
				>
				</span>
				<Icon icon="delete" :rounded="true" @click.stop="$emit('update:modelValue', null)" />
			</div>
			<span v-else @click="showModal = true">
				{{ __('Select an icon', 'zionbuilder') }}
			</span>
		</div>
		<Modal
			v-model:show="showModal"
			:width="590"
			:fullscreen="false"
			append-to=".znpb-center-area"
			:show-maximize="false"
			class="znpb-icon-library-modal"
			:title="__('Icon Library', 'zionbuilder')"
		>
			<IconsLibraryModalContent
				v-model="valueModel"
				:special-filter-pack="specialFilterPack"
				@selected="$emit('update:modelValue', valueModel)"
			/>
		</Modal>
	</div>
</template>

<script lang="ts">
export default {
	name: 'IconLibrary',
};
</script>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { ref, computed } from 'vue';
import type { Icons } from '../../composables';
import IconsLibraryModalContent from './IconsLibraryModalContent.vue';

type Icon = { family: string; name: string; unicode: string };

const props = defineProps<{
	specialFilterPack?: Icons[];
	title: string;
	modelValue?: Icon | null;
}>();

const emit = defineEmits<{
	(e: 'update:modelValue', value: Icon | null | undefined): void;
}>();
const showModal = ref(false);

const valueModel = computed({
	get() {
		return props.modelValue;
	},
	set(newValue: Icon | null | undefined) {
		emit('update:modelValue', newValue);
	},
});

function unicode(unicode: string) {
	return JSON.parse('"\\' + unicode + '"');
}

function open() {
	showModal.value = true;
}
</script>
<style lang="scss">
.znpb-icon-options {
	width: 100%;
	padding: 0 5px 20px;
}
.znpb-icon-trigger {
	position: relative;
	display: flex;
	justify-content: center;
	align-items: center;
	height: 100px;
	font-weight: 500;
	background-color: var(--zb-surface-lighter-color);
	border-radius: 3px;
	cursor: pointer;

	&--no-icon {
		background: transparent;
		border: 2px dashed var(--zb-surface-border-color);
	}

	.znpb-editor-icon-wrapper {
		position: absolute;
		right: 15px;
		bottom: 15px;
		background: var(--zb-surface-color);
		padding: 7px;
		transition: all 0.2s;
		&:hover {
			opacity: 0.7;
		}
	}
}
.znpb-icon-preview {
	color: var(--zb-surface-text-active-color);
	font-size: 28px;
}
.znpb-icon-library-modal > .znpb-modal__wrapper {
	width: 100%;
}
</style>
