<template>
	<PageTemplate class="znpb-admin-gradients__wrapper">
		<h3>{{ $translate('gradients') }}</h3>
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
					:message_title="$translate('pro_manage_global_gradients_free_title')"
					:message_description="$translate('pro_manage_global_gradients_free')"
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
			<p class="znpb-admin-info-p">{{ $translate('create_gradient_info') }}</p>
		</template>
	</PageTemplate>
</template>

<script>
import { ref } from 'vue';
import { generateUID, getDefaultGradient } from '@zb/utils';

// Components
import GradientBox from './GradientBox.vue';
import GradientModalContent from './GradientModalContent.vue';
import AddGradient from './AddGradient.vue';

export default {
	name: 'Gradients',
	components: {
		GradientBox,
		AddGradient,
		GradientModalContent,
	},
	setup(props, context) {
		function getPro() {
			if (window.ZBCommonData !== undefined) {
				return window.ZBCommonData.is_pro_active;
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
		} = window.zb.common.store.useBuilderOptionsStore();

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
			let dynamicName = generateUID();

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

		return {
			localGradients,
			globalGradients,
			addLocalGradient,
			deleteLocalGradient,
			editLocalGradient,
			saveOptionsToDB,
			onAddNewGradient,
			activeLibrary,
			showModal,
			onGradientSelect,
			onGradientUpdate,
			deleteGlobalGradient,
			activeGradient,
			isPro,
		};
	},
};
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
