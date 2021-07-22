<template>
	<div class="znpb-help-modal">
		<div class="znpb-help-modal__list znpb-fancy-scrollbar">
			<div
				class="znpb-help-modal__list-item"
				:class="{['znpb-help-modal__list-item--active']: activeIndex === index}"
				v-for="(item, index) in helpData"
				:key="index"
				@click="selectContent(index)"
			>
				<h3 class="znpb-help-modal__list-title">{{item.title}}</h3>
				<span class="znpb-help-modal__list-duration">{{item.duration}}</span>

			</div>
		</div>
		<div class="znpb-help-modal__video znpb-fancy-scrollbar">
			<h3 class="znpb-help-modal__video-title">{{content.title}}</h3>
			<iframe
				class="znpb-help-modal__video-iframe"
				:src="`https://www.youtube.com/embed/${this.videoUrl}`"
				frameborder="0"
				allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
				allowfullscreen
			></iframe>
			<p class="znpb-help-modal__video-description">{{content.description}}</p>
		</div>
	</div>
</template>

<script>
import { youtubeUrlParser } from '@zb/utils'

export default {
	name: 'Help',
	data () {
		return {
			content: {},
			activeIndex: 0,
			videoUrl: '',
			helpData: [
				{
					title: 'Page builder overview 1',
					description: 'In this and how to install one of the demos that comes with it. This video is based on a more recent version of Kallyas. 1 In this video we will explain how to Install and register Kallyas and how to install one of the demos that comes with it. This video is based on a more recent version of Kallyas. 1 In this and how to install one of the demos that comes with it. This video is based on a more recent version of Kallyas. 1 In this video we will explain how to Install and register Kallyas and how to install one of the demos that comes with it. This video is based on a more recent version of Kallyas. 1 ',
					video: 'https://www.youtube.com/watch?v=ZsBAkSxwU5c',
					duration: '4:32'
				},
				{
					title: 'Page builder overview 2',
					description: 'In this video we will explain how to Install and register Kallyas and how to install one of the demos that comes with it. This video is based on a more recent version of Kallyas. 2',
					video: 'https://www.youtube.com/watch?v=UrouueTnZYo',
					duration: '4:32'
				},
				{
					title: 'Page builder overview 3',
					description: 'In this video we will explain how to Install and register Kallyas and how to install one of the demos that comes with it. This video is based on a more recent version of Kallyas. 3',
					video: 'https://www.youtube.com/watch?v=M1F81V-NhP0',
					duration: '4:32'
				},
				{
					title: 'Page builder overview 4',
					description: 'In this video we will explain how to Install and register Kallyas and how to install one of the demos that comes with it. This video is based on a more recent version of Kallyas. 4',
					video: 'https://www.youtube.com/embed/7Mz92k2r7AY',
					duration: '4:32'
				},
				{
					title: 'Page builder overview 5',
					description: 'In this video we will explain how to Install and register Kallyas and how to install one of the demos that comes with it. This video is based on a more recent version of Kallyas.5 ',
					video: 'https://www.youtube.com/embed/7Mz92k2r7AY',
					duration: '4:32'
				},
				{
					title: 'Page builder overview 6',
					description: 'In this video we will explain how to Install and register Kallyas and how to install one of the demos that comes with it. This video is based on a more recent version of Kallyas.6 ',
					video: 'https://www.youtube.com/embed/7Mz92k2r7AY',
					duration: '4:32'
				},
				{
					title: 'Page builder overview 4',
					description: 'In this video we will explain how to Install and register Kallyas and how to install one of the demos that comes with it. This video is based on a more recent version of Kallyas. 4',
					video: 'https://www.youtube.com/embed/7Mz92k2r7AY',
					duration: '4:32'
				},
				{
					title: 'Page builder overview 5',
					description: 'In this video we will explain how to Install and register Kallyas and how to install one of the demos that comes with it. This video is based on a more recent version of Kallyas.5 ',
					video: 'https://www.youtube.com/embed/7Mz92k2r7AY',
					duration: '4:32'
				},
				{
					title: 'Page builder overview 6',
					description: 'In this video we will explain how to Install and register Kallyas and how to install one of the demos that comes with it. This video is based on a more recent version of Kallyas.6 ',
					video: 'https://www.youtube.com/embed/7Mz92k2r7AY',
					duration: '4:32'
				},
				{
					title: 'Page builder overview 4',
					description: 'In this video we will explain how to Install and register Kallyas and how to install one of the demos that comes with it. This video is based on a more recent version of Kallyas. 4',
					video: 'https://www.youtube.com/embed/7Mz92k2r7AY',
					duration: '4:32'
				},
				{
					title: 'Page builder overview 5',
					description: 'In this video we will explain how to Install and register Kallyas and how to install one of the demos that comes with it. This video is based on a more recent version of Kallyas.5 ',
					video: 'https://www.youtube.com/embed/7Mz92k2r7AY',
					duration: '4:32'
				},
				{
					title: 'Page builder overview 6',
					description: 'In this video we will explain how to Install and register Kallyas and how to install one of the demos that comes with it. This video is based on a more recent version of Kallyas.6 ',
					video: 'https://www.youtube.com/embed/7Mz92k2r7AY',
					duration: '4:32'
				}
			]
		}
	},
	mounted () {
		this.content = this.helpData[0]
		this.videoUrl = youtubeUrlParser(this.helpData[0].video)
	},
	methods: {
		selectContent (index) {
			this.activeIndex = index
			this.content = this.helpData[index]
			this.videoUrl = youtubeUrlParser(this.helpData[index].video)
		}
	}

}
</script>

<style lang="scss">
.znpb-helpmodal-wrapper {
	.znpb-modal__wrapper,
	.znpb-modal__content {
		height: 100%;
		max-height: 560px;
	}

	.znpb-modal__wrapper--full-size {
		.znpb-modal__wrapper,
		.znpb-modal__content,
		.znpb-help-modal {
			height: 100%;
			max-height: none;
		}
	}
}

.znpb-help-modal {
	display: flex;
	max-height: 100%;

	.znpb-help-modal__video-title {
		margin-bottom: 20px;
		color: var(--zb-surface-text-active-color);
		font-weight: 500;
	}
}

.znpb-help-modal__list {
	display: flex;
	flex-direction: column;
	flex: 1 0 40%;
	max-width: 40%;
	border-right: 1px solid var(--zb-surface-lighter-color);
}

.znpb-help-modal__list-item {
	display: flex;
	justify-content: space-between;
	padding: 20px;
	border-bottom: 1px solid var(--zb-surface-lighter-color);
	transition: all 0.15s ease;
	cursor: pointer;

	&--active {
		color: var(--zb-surface-text-active-color);
	}

	&:hover {
		color: var(--zb-surface-text-active-color);
	}
}

.znpb-help-modal__video-iframe {
	width: 100%;
	min-height: 260px;
	margin-bottom: 20px;
}

.znpb-help-modal__video {
	display: flex;
	flex-direction: column;
	flex: 1;
	padding: 20px;

	video {
		width: 100%;
		min-height: 280px;
		margin-bottom: 30px;
	}
}

.znpb-help-modal__list-duration {
	font-size: 13px;
	font-weight: 500;
	opacity: 0.6;
}

.znpb-help-modal__list-title {
	font-size: 14px;
	font-weight: 500;
}

.znpb-help-modal__video-description {
	font-size: 14px;
}
</style>
