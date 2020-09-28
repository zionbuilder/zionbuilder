<template>
	<div class="znpb-about-modal znpb-fancy-scrollbar">
		<div class="znpb-about-modal__content">
			<p class="znpb-about-modal__description"
				v-html="$translate('about_zion_description')"
			></p>
			<div class="znpb-about-modal__card-wrapper">
				<!-- free -->
				<pluginCard
					:is-pro="false"
					:version="pluginInfo.free_version"
					:update-version="pluginFreeUpdate.new_version "
				/>

				<!-- pro -->
				<pluginCard
					:is-pro="true"
					:version="pluginInfo.pro_version"
					:update-version="pluginProUpdate.new_version "
				/>

			</div>
			<a href="" class="znpb-about-modal__help">{{$translate('need_help')}}</a>

		</div>
	</div>
</template>

<script>
import pluginCard from './about-modal/pluginCard.vue'
export default {
	name: 'aboutModal',
	components: {
		pluginCard
	},
	data () {
		return {

		}
	},
	computed: {
		pluginInfo () {
			return window.ZnPbInitalData.plugin_info
		},
		pluginProUpdate () {
			return this.pluginInfo.pro_plugin_update || {}
		},
		pluginFreeUpdate () {
			return this.pluginInfo.free_plugin_update || {}
		}

	}
}
</script>

<style lang="scss">
.znpb-about-modal {
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	padding: 20px;

	&__content {
		display: flex;
		flex-direction: column;
		height: 100%;
		min-height: 0;

		& > p.znpb-about-modal__description {
			margin-bottom: 15px;
		}
	}

	&__card-wrapper {
		display: flex;
		justify-content: center;
	}

	&__help {
		color: $secondary;
		text-align: center;
	}
}
</style>
