<template>
	<PageTemplate class="znpb-admin-gradients__wrapper">
		<h3>{{$translate('gradients')}}</h3>
		<Tabs
			tab-style="minimal"
			@changed-tab="activeLibrary=$event,activeGradientIndex=0 "
		>
			<Tab name="Local">
				<div class="znpb-admin-gradient__container">
					<GradientBox
						v-for="(gradient, index) in getLocalGradients"
						:key="index"
						:config="gradient.config"
						@delete-gradient="deleteGradientElement(gradient)"
						@click="onGradientSelect(index)"
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
							v-for="(gradient, index) in getGlobalGradients"
							:key="index"
							:config="gradient.config"
							@click="onGradientSelect(index)"
							@delete-gradient="deleteGlobalGradientElement(gradient)"
						/>
						<AddGradient @click="onAddNewGlobalGradient" />
					</div>
				</template>

			</Tab>
		</Tabs>

		<GradientModalContent
			v-model:show="showModal"
			:gradient="activeGradient"
			@update-gradient="onGradientUpdate"
			@save-gradient="onSaveGradient"
		/>

		<template v-slot:right>
			<p class="znpb-admin-info-p">{{$translate('create_gradient_info')}} </p>
		</template>
	</PageTemplate>
</template>

<script>

import { computed, ref, inject, reactive } from 'vue'
import GradientBox from './GradientBox.vue'
import { getDefaultGradient } from '@zionbuilder/components/utils'
import GradientModalContent from './GradientModalContent.vue'
import AddGradient from './AddGradient.vue'
import { Tabs, Tab, UpgradeToPro } from '@zb/components'

export default {
	name: 'Gradients',
	components: {
		GradientBox,
		AddGradient,
		GradientModalContent,
		UpgradeToPro,
		Tabs,
		Tab
	},
	setup (props, context) {
		const $zb = inject('$zb')

		const activeLibrary = ref('local')
		const activeGradientIndex = ref(0)
		const showModal = ref(false)
		const storegradient = reactive({})

		const getLocalGradients = computed(() => {
			return $zb.options.getOptionValue('local_gradients')
		})

		const getGlobalGradients = computed(() => {
			return $zb.options.getOptionValue('global_gradients')
		})

		const isPro = computed(() => {
			return window.ZnPbAdminPageData.is_pro_active
		})

		const activeGradient = computed({
			get: () => {
				const gradientsArray = activeLibrary.value === 'local' ? getLocalGradients.value : getGlobalGradients.value
				return gradientsArray !== undefined && gradientsArray[activeGradientIndex.value] !== undefined ? gradientsArray[activeGradientIndex.value]['config'] : []

			},
			set: val => {
				const gradientslocal = activeLibrary.value === 'local' ? getLocalGradients.value : getGlobalGradients.value
				const gradient = gradientslocal[activeGradientIndex.value]
				storegradient.value = val
			}
		})

		function deleteGradientElement (gradient) {
			$zb.options.deleteOptionValue('local_gradients', gradient)
		}

		function deleteGlobalGradientElement (gradient) {
			$zb.options.deleteOptionValue('global_gradients', gradient)
			activeGradientIndex.value = getGlobalGradients.length - 1
		}

		function onGradientSelect (index) {
			activeGradientIndex.value = index
			showModal.value = true
		}

		function onGradientUpdate (gradient) {
			activeGradient.value = gradient
		}

		function onSaveGradient () {
			$zb.options.saveOptions()
		}

		function onAddNewGradient () {
			let arrayLength = getLocalGradients.value.length

			let dynamicName = `gradientPreset${arrayLength + 1}`
			const defaultGradient = {
				id: dynamicName,
				name: dynamicName,
				config: getDefaultGradient()
			}

			// Add the gradient to store
			$zb.options.addGradient('local_gradients', defaultGradient)
			// activeGradient.value = defaultGradient

			activeGradientIndex.value = getLocalGradients.value.length - 1
			showModal.value = true
		}
		function onAddNewGlobalGradient () {
			let arrayLength = getGlobalGradients.length

			let dynamicName = `gradientPreset${arrayLength + 1}`
			const defaultGradient = {
				id: dynamicName,
				name: dynamicName,
				config: getDefaultGradient()
			}
			$zb.options.addGradient('global_gradients', defaultGradient)
			activeGradientIndex.value = getGlobalGradients.value.length === 1 ? 0 : getGlobalGradients.value.length - 1
			showModal.value = true
		}


		return {
			getLocalGradients,
			getGlobalGradients,
			activeLibrary,
			activeGradientIndex,
			showModal,
			deleteGradientElement,
			deleteGlobalGradientElement,
			onGradientSelect,
			onGradientUpdate,
			onSaveGradient,
			onAddNewGlobalGradient,
			onAddNewGradient,
			activeGradient,
			isPro
		}
	},

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
