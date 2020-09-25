<template>
	<transition
		appear
		name="move"
	>
		<div class="znpb-notices-wrapper">
			<div
				class="znpb-notice"
				:class="`znpb-notice--${type}`"
			>
				<BaseIcon
					class="znpb-notice__close"
					icon="close"
					@click.native="$emit('close-notice')"
				/>
				<div
					v-if="error.title"
					class="znpb-notice__title"
				>{{error.title}}</div>
				<div
					class="znpb-notice__message"
					v-html="error.message"
				></div>
			</div>
		</div>
	</transition>
</template>

<script>
import BaseIcon from './BaseIcon.vue'

export default {
	name: 'Notice',
	components: {
		BaseIcon
	},
	props: {
		error: {
			type: Object,
			required: true
		}
	},
	data () {
		return {
			type: this.error.type ? this.error.type : 'success',
			delayClose: typeof this.error.delayClose !== 'undefined' ? this.error.delayClose : 5000
		}
	},
	mounted () {
		if (this.delayClose !== 0) {
			setTimeout(() => {
				this.$emit('close-notice')
			}, this.delayClose)
		}

		document.addEventListener('keydown', this.hideOnEscape)
	},
	methods: {
		hideOnEscape (event) {
			if (event.which === 27) {
				this.$emit('close-notice')
				event.preventDefault()
				document.removeEventListener('keydown', this.hideOnEscape)
			}
		}
	},
	beforeUnmount () {
		document.removeEventListener('keydown', this.hideOnEscape)
	}
}
</script>

<style lang="scss">
.znpb-notices-wrapper {
	position: absolute;
	right: 30px;
	bottom: 20px;
	z-index: 1000;
	width: 100%;
	max-width: 320px;
	transform: translateX(0);
}
.znpb-notice {
	padding: 16px 20px;
	margin-bottom: 10px;
	color: #fff;
	line-height: 1.8;
	background: #1bb934;
	border-radius: 3px;

	&__title {
		display: inline-block;
		margin-bottom: 10px;
		color: #fff;
		font-weight: 500;
		border-bottom: 1px solid rgba(255, 255, 255, .4);
	}

	&__close {
		position: absolute;
		top: 10px;
		right: 10px;
		color: $surface;
		font-size: 12px;
		cursor: pointer;
	}

	&--info-blue {
		color: #fff;
		background: #18208d;
	}

	&--warning {
		background: #eec643;
	}

	&--info {
		background: #2ea1f8;
	}

	&--error {
		background: #e84655;
	}
}

.move-enter-active {
	transition: all .2s cubic-bezier(1, .5, .8, 1);
}
.move-leave-active {
	transition: all .2s cubic-bezier(1, .5, .8, 1);
}
.move-enter {
	transform: translateX(20px);
	opacity: 0;
}
.move-leave-to {
	transform: translateX(20px);
	opacity: 0;
}
</style>
