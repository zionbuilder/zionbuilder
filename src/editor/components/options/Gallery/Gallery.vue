<template>
	<div class="znpb-option__image-gallery">
		<EmptyList v-if="!sortableModel.length" @click="openMediaModal">
			{{ translate('no_images_selected') }}
		</EmptyList>

		<Sortable v-else v-model="sortableModel" class="znpb-option__image-gallery__images-wrapper">
			<div
				v-for="(image, index) in modelValue"
				:key="index"
				class="znpb-option__image-gallery__images-item"
				:style="{
					'background-image': `url(${image.image})`,
					'background-size': 'cover',
					'border-radius': '3px',
				}"
			>
				<!-- <img :src="image.image"/> -->

				<span class="znpb-option__image-gallery__images-item--delete" @click="deleteImage(index)">
					<Icon :rounded="true" icon="delete" :bg-size="30" bg-color="#fff" />
				</span>
			</div>
			<template #end>
				<div class="znpb-option__image-gallery__images-item--add" @click="openMediaModal">+</div>
			</template>
		</Sortable>
	</div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

import { translate } from '/@/common/modules/i18n';
import { getImageIds } from '/@/common/api';

const { wp } = window;

type imageValueType = {
	image: string;
};

const props = withDefaults(
	defineProps<{
		modelValue?: imageValueType[];
		title?: string;
		type?: string;
	}>(),
	{
		modelValue() {
			return [];
		},
		title: '',
		type: 'image',
	},
);

const emit = defineEmits(['update:modelValue']);

const sortableModel = computed({
	get() {
		return props.modelValue || [];
	},
	set(newValue: imageValueType[]) {
		emit('update:modelValue', newValue);
	},
});

let mediaModal = null;
function openMediaModal() {
	if (mediaModal === null) {
		const args = {
			frame: 'select',
			state: 'zion-media',
			library: {
				type: 'image',
			},
			multiple: true,
			selection: [],
		};

		// Create the frame
		mediaModal = new wp.media.view.MediaFrame.ZionBuilderFrame(args);
		mediaModal.on('select update insert', selectMedia);

		// Select the images
		mediaModal.on('open', setMediaModalSelection);
	}

	// Open the media modal
	mediaModal.open();
}

function setMediaModalSelection() {
	if (typeof props.modelValue === 'undefined') return;

	// Get image ids from DB
	let imagesUrls = props.modelValue.map(image => image.image);
	getImageIds({
		images: imagesUrls,
	}).then(response => {
		const imageIds = Object.keys(response.data).map(image => {
			return response.data[image];
		});

		const selection = mediaModal.state().get('selection');
		imageIds.forEach(imageId => {
			var attachment = wp.media.attachment(imageId);
			selection.add(attachment ? [attachment] : []);
		});
	});
}

function selectMedia(e) {
	let selection = mediaModal.state().get('selection').toJSON();
	// In case we have multiple items
	if (typeof e !== 'undefined') {
		selection = e;
	}

	const values = selection.map(selectedItem => {
		return { image: selectedItem.url };
	});

	emit('update:modelValue', values);
}

function deleteImage(index: number) {
	const values = [...props.modelValue];
	values.splice(index, 1);

	emit('update:modelValue', values);
}
</script>
<style lang="scss">
.znpb-option__image-gallery {
	padding: 0 5px;

	.znpb-empty-list__container {
		background-color: var(--zb-surface-lighter-color);
	}
	.znpb-empty-list__content {
		padding: 50px 0;
	}
	&__images-wrapper {
		display: flex;
		flex-wrap: wrap;
		margin: 0 -4px;
	}

	&__images-item {
		position: relative;
		display: flex;
		justify-content: center;
		align-items: center;
		flex: 0 0 calc((100% - 16px) / 3);
		max-width: calc((100% - 16px) / 3);
		margin: 0 8px 8px 0;
		box-shadow: 0 0 0 0 #f1f1f1;

		&::after {
			content: '';
			display: block;
			padding-top: 100%;
		}

		&--delete {
			position: absolute;
			top: 5px;
			right: 5px;
			z-index: 9;
			margin-top: 5px;
			margin-right: 5px;
			cursor: pointer;
			opacity: 0;
			visibility: hidden;
		}
		&--add {
			display: flex;
			justify-content: center;
			align-items: center;
			flex: 0 0 calc((100% - 16px) / 3);
			max-width: calc((100% - 16px) / 3);
			margin: 0 0 8px;
			color: var(--zb-surface-text-color);
			font-size: 20px;
			border: 2px dashed var(--zb-surface-border-color);
			cursor: pointer;

			&::after {
				content: '';
				display: block;
				padding-top: 100%;
			}
		}
		&:hover &--delete {
			opacity: 1;
			visibility: visible;
		}

		&:nth-child(3n) {
			margin-right: 0;
		}
	}
}
</style>
