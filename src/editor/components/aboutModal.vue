<template>
	<div class="znpb-about-modal znpb-fancy-scrollbar">
		<div class="znpb-about-modal__content">
			<p
				class="znpb-about-modal__description"
				v-html="
					__(
						'Producing <b>smashing design</b> is now possible with Zion Builder. <br/>Complex elements, library system, responsive building design, multilingual adaptability, speed and performance, control not only over the actions but also over the whole website, and powerful blog options are barely few of the features for this <b> blue-chip </b> plugin. <br/><br/>Choose the version that fits your needs, as Zion Builder offers you the possibility to <b> build a website in no-time </b>even if just the free version is active.',
						'zionbuilder',
					)
				"
			></p>
			<div class="znpb-about-modal__card-wrapper">
				<!-- free -->
				<pluginCard
					:is-pro="false"
					:version="EnvironmentStore.plugin_free.version"
					:update-version="EnvironmentStore.plugin_free.update_version"
				/>

				<!-- pro -->
				<pluginCard
					:is-pro="true"
					:is-pro-active="EnvironmentStore.plugin_pro.is_active"
					:version="EnvironmentStore.plugin_pro.version"
					:update-version="EnvironmentStore.plugin_pro.update_version"
				/>
			</div>
			<a
				:href="EnvironmentStore.urls.documentation_url"
				class="znpb-about-modal__help"
				target="_blank"
				title="documentation"
				>{{ __('Do you need help?', 'zionbuilder') }}</a
			>
		</div>
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import pluginCard from './about-modal/pluginCard.vue';
import { useEnvironmentStore } from '@zb/store';

const EnvironmentStore = useEnvironmentStore();

function openWindow(link: string) {
	window.open(link);
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
		color: var(--zb-secondary-color);
		text-align: center;
	}
}
</style>
