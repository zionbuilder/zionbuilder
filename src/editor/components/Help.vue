<template>
	<div class="znpb-help-modal">
		<div class="znpb-help-modal__list znpb-fancy-scrollbar">
			<div
				class="znpb-help-modal__list-item"
				:class="{['znpb-help-modal__list-item--active']: activeIndex === index}"
				v-for="(item, index) in getHelpData"
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
import { youtubeUrlParser } from '@/utils/utils.js'
import { mapGetters } from 'vuex'
export default {
	name: 'Help',
	data () {
		return {
			content: {},
			activeIndex: 0,
			videoUrl: ''
		}
	},
	computed: {
		...mapGetters([
			'getHelpData'
		])
	},
	mounted () {
		this.content = this.getHelpData[0]
		this.videoUrl = youtubeUrlParser(this.getHelpData[0].video)
	},
	methods: {
		selectContent (index) {
			this.activeIndex = index
			this.content = this.getHelpData[index]
			this.videoUrl = youtubeUrlParser(this.getHelpData[index].video)
		}
	}

}
</script>

<style lang="scss">
.znpb-helpmodal-wrapper {
	.znpb-modal__wrapper, .znpb-modal__content {
		height: 100%;
		max-height: 560px;
	}

	.znpb-modal__wrapper--full-size {
		.znpb-modal__wrapper, .znpb-modal__content, .znpb-help-modal {
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
		color: $surface-active-color;
		font-weight: 500;
	}
}

.znpb-help-modal__list {
	display: flex;
	flex-direction: column;
	flex: 1 0 40%;
	max-width: 40%;
	border-right: 1px solid $surface-variant;
}

.znpb-help-modal__list-item {
	display: flex;
	justify-content: space-between;
	padding: 20px;
	border-bottom: 1px solid $surface-variant;
	transition: all .15s ease;
	cursor: pointer;

	&--active {
		color: $surface-active-color;
	}

	&:hover {
		color: $surface-active-color;
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
	opacity: .6;
}

.znpb-help-modal__list-title {
	font-size: 14px;
	font-weight: 500;
}

.znpb-help-modal__video-description {
	font-size: 14px;
}
</style>
