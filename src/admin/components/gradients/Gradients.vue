<template>
	<PageTemplate class="znpb-admin-gradients__wrapper">
		<h3>{{ i18n.__('Gradients', 'zionbuilder') }}</h3>
		<Tabs tab-style="minimal" @changed-tab="(activeLibrary = $event), (activeGradient.value = {})">
			<Tab name="Local">
				<div class="znpb-admin-gradient__container">
					<GradientBox
						v-for="(gradient, index) in localGradients"
						:key="index"
						:config="gradient.config"
						@delete-gradient="deleteLocalGradient(gradient)"
						@click="onGradientSelect(gradient)"
					/>

					<AddGradient @click="onAddNewGradient" />
				</div>
			</Tab>
			<Tab name="Global">
				<UpgradeToPro
					v-if="!isPro"
					:message_title="i18n.__('Meet Global Gradients', 'zionbuilder')"
					:message_description="
						i18n.__(
							'Global gradients allows you to define a gradient configuration that you can use in builder, and every time this gradient configuration changes it will be updated automatically in all locations where it was used. ',
							'zionbuilder',
						)
					"
				/>
				<template v-else>
					<div class="znpb-admin-gradient__container">
						<GradientBox
							v-for="(gradient, index) in globalGradients"
							:key="index"
							:config="gradient.config"
							@click="onGradientSelect(gradient)"
							@delete-gradient="deleteGlobalGradient(gradient)"
						/>
						<AddGradient @click="onAddNewGradient" />
					</div>
				</template>
			</Tab>
		</Tabs>

		<GradientModalContent
			v-model:show="showModal"
			:gradient="activeGradient.config"
			@update-gradient="onGradientUpdate"
			@save-gradient="saveOptionsToDB"
		/>

		<template #right>
			<p class="znpb-admin-info-p">
				{{ i18n.__('Create Astonishing Gradients that you will use in all the pages of your website', 'zionbuilder') }}
			</p>
		</template>
	</PageTemplate>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { ref } from 'vue';

// Components
import GradientBox from './GradientBox.vue';
import GradientModalContent from './GradientModalContent.vue';
import AddGradient from './AddGradient.vue';

const { generateUID, getDefaultGradient } = window.zb.utils;

function getPro() {
	if (window.ZBCommonData !== undefined) {
		return window.ZBCommonData.environment.plugin_pro.is_active;
	}

	return false;
}

const isPro = getPro();

const {
	getOptionValue,
	saveOptionsToDB,
	addLocalGradient,
	deleteLocalGradient,
	editLocalGradient,
	addGlobalGradient,
	deleteGlobalGradient,
	editGlobalGradient,
} = window.zb.store.useBuilderOptionsStore();

const activeLibrary = ref('local');
const showModal = ref(false);

const localGradients = getOptionValue('local_gradients');
const globalGradients = getOptionValue('global_gradients');
const activeGradient = ref({});

function onGradientSelect(gradient) {
	activeGradient.value = gradient;
	showModal.value = true;
}

function onGradientUpdate(newValue) {
	if (activeLibrary.value === 'local') {
		editLocalGradient(activeGradient.value.id, newValue);
	} else {
		editGlobalGradient(activeGradient.value.id, newValue);
	}
}

function onAddNewGradient() {
	const dynamicName = generateUID();

	const defaultGradient = {
		id: dynamicName,
		name: dynamicName,
		config: getDefaultGradient(),
	};

	if (activeLibrary.value === 'local') {
		addLocalGradient(defaultGradient);
	} else {
		addGlobalGradient(defaultGradient);
	}

	// Add the gradient to store
	onGradientSelect(defaultGradient);
}
</script>

<style lang="scss">
.znpb-admin-gradients__wrapper {
	.znpb-admin-gradient__container {
		display: grid;

		grid-gap: 20px;
		grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
	}
}
</style>
