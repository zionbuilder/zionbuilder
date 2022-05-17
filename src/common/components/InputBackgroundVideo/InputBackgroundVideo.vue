<template>
	<div class="znpb-input-background-video">
		<div class="znpb-input-background-video__holder">
			<div v-if="hasVideo" ref="videoPreview" class="znpb-input-background-video__source" />

			<EmptyList
				v-else
				class="znpb-input-background-video__empty znpb-input-background-video__source"
				:no-margin="true"
				@click="openMediaModal"
			>
				{{ $translate('no_video_selected') }}
			</EmptyList>

			<Icon
				v-if="videoSourceModel == 'local' && hasVideo"
				class="znpb-input-background-video__delete"
				icon="delete"
				:bg-size="30"
				bg-color="#fff"
				@click.stop="deleteVideo"
			/>
		</div>
		<OptionsForm v-model="computedValue" :schema="schema" class="znpb-input-background-video__holder" />
	</div>
</template>

<script lang="ts">
export default {
	name: 'InputBackgroundVideo',
};
</script>

<script lang="ts" setup>
import { computed, onMounted, ref, Ref, watch, nextTick } from 'vue';
import { Icon } from '../Icon';
import { EmptyList } from '../EmptyList';
import Video from '../../../video';
import { useOptionsSchemas } from '../../composables/useOptionsSchemas';

interface VideoValue {
	mp4?: string;
	bgType?: string;
	autoplay?: boolean;
	muted?: boolean;
	controlsPosition?: string;
	controls?: boolean;
	videoSource?: string;
	vimeoURL?: string;
	youtubeURL?: string;
}

const props = defineProps<{
	modelValue?: VideoValue;
	options?: any;
	exclude_options?: string[];
}>();

const emit = defineEmits<{
	(e: 'update:modelValue', value: VideoValue): void;
}>();
const videoInstance: Ref<Video | null> = ref(null);
const mediaModal: Ref<Record<string, any> | null> = ref(null);
const videoPreview: Ref<HTMLDivElement | null> = ref(null);

const { getSchema } = useOptionsSchemas();

const schema = computed(() => {
	const schema: Record<string, any> = { ...getSchema('videoOptionSchema') };

	if (props.exclude_options) {
		props.exclude_options.forEach(optionToRemove => {
			if (schema[optionToRemove]) {
				delete schema[optionToRemove];
			}
		});
	}
	return schema;
});

const computedValue = computed({
	get() {
		return props.modelValue || {};
	},
	set(newValue: VideoValue) {
		emit('update:modelValue', newValue);
	},
});

const hasVideo = computed(() => {
	if (videoSourceModel.value === 'local' && computedValue.value.mp4) {
		return true;
	}

	if (videoSourceModel.value === 'youtube' && computedValue.value.youtubeURL) {
		return true;
	}

	if (videoSourceModel.value === 'vimeo' && computedValue.value.vimeoURL) {
		return true;
	}

	return false;
});

const videoSourceModel = computed({
	get() {
		return computedValue.value['videoSource'] || 'local';
	},
	set(newValue: string) {
		emit('update:modelValue', {
			...props.modelValue,
			videoSource: newValue,
		});
	},
});

watch(computedValue, () => {
	if (videoInstance.value) {
		videoInstance.value.destroy();
	}
	if (hasVideo.value) {
		initVideo();
	}
});

function initVideo() {
	nextTick(() => {
		videoInstance.value = new Video(videoPreview.value as HTMLDivElement, computedValue.value);
	});
}

function openMediaModal() {
	if (mediaModal.value === null) {
		const args = {
			frame: 'select',
			state: 'library',
			library: { type: 'video' },
			button: { text: 'Add video' },
			selection: computedValue.value,
		};

		// Create the frame
		mediaModal.value = window.wp.media(args) as Record<string, any>;

		mediaModal.value.on('select update insert', selectMedia);
	}

	// Open the media modal
	mediaModal.value.open();
}

function selectMedia() {
	const selection = (mediaModal.value as Record<string, any>).state().get('selection').toJSON();

	emit('update:modelValue', {
		...computedValue.value,
		mp4: selection[0].url,
	});
}

function deleteVideo() {
	const { mp4, ...rest } = computedValue.value;
	emit('update:modelValue', {
		...rest,
	});
}

onMounted(() => {
	if (hasVideo.value) {
		initVideo();
	}
});
</script>

<style lang="scss">
.znpb-input-background-video .znpb-options-form-wrapper {
	padding: 0;
	margin-right: -5px;
	margin-left: -5px;
}

.znpb-input-background-video {
	&__delete {
		position: absolute;
		top: 10px;
		right: 10px;
		z-index: 1;
		display: flex;
		justify-content: flex-end;
		border-radius: 3px;
		transition: all 0.2s ease;
		cursor: pointer;
		opacity: 0;
	}
	&__holder {
		position: relative;
		margin-bottom: 20px;

		&:hover {
			.znpb-input-background-video__delete {
				opacity: 1;
			}
		}
		iframe {
			width: 100%;
		}
		&--preview {
			width: 100%;
		}
	}
	&__input {
		.znpb-input-content {
			display: flex;
		}
		.zion-input {
			margin-right: 5px;
		}
	}
	.znpb-input-media-wrapper {
		width: 100%;
	}
	.znpb-empty-list__content {
		padding: 50px 30px;
	}

	& > .znpb-input-wrappers__wrapper {
		.znpb-input-wrapper {
			padding: 0;
		}
	}

	&__source iframe,
	&__source video {
		max-width: 100%;
		height: auto;
		border: none;
	}
}
</style>
