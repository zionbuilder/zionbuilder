<template>
	<div class="znpb-template-preview__wrapper znpb-fancy-scrollbar">
		<Loader v-if="!loaded"
			class="znpb-template-preview__loader"
		/>
		<iframe
			frameborder="0"
			:src="frameUrl"
			@load="loaded = true"
			scrolling="yes"
		>
		</iframe>

	</div>
</template>

<script>
import { Loader } from '@zionbuilder/components'

export default {
	name: 'modal-template-preview',
	components: {
		Loader
	},
	props: {
		frameUrl: {
			type: String,
			required: true
		}
	},
	data: function () {
		return {
			loaded: false
		}
	}
}
</script>

<style lang="scss">
.znpb-template-preview {
	&__wrapper {
		position: relative;
		min-width: 800px;
		max-width: 100%;
		height: 0;
		padding-bottom: 56.25%;

		iframe {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
		}
	}

	&__loader {
		position: absolute;
		top: 0;
		left: 0;
		display: flex;
		justify-content: center;
		align-items: center;
		width: 100%;
		height: 100%;
		background: #fff;
	}
}

</style>
