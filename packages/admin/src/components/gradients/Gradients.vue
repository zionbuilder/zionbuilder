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
						@delete-gradient="deleteGradientElement(index)"
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
							@delete-gradient="deleteGlobalGradientElement(index)"
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
import { mapGetters, mapActions } from 'vuex'

import GradientBox from './GradientBox.vue'
import { getDefaultGradient } from '@zb/components/utils'
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
	data () {
		return {
			showModal: false,
			activeGradientIndex: 0,
			activeLibrary: 'local'
		}
	},
	computed: {
		...mapGetters([
			'getLocalGradients',
			'getGlobalGradients',
			'isPro'
		]),
		activeGradient: {
			get () {
				if (this.activeLibrary === 'local') {
					return this.getLocalGradients !== undefined && this.getLocalGradients[this.activeGradientIndex] !== undefined ? this.getLocalGradients[this.activeGradientIndex].config : []
				} else {
					return this.getGlobalGradients !== undefined && this.getGlobalGradients[this.activeGradientIndex] !== undefined ? this.getGlobalGradients[this.activeGradientIndex].config : []
				}
			},
			set (newValue) {
				const gradients = this.activeLibrary === 'local' ? this.getLocalGradients : this.getGlobalGradients
				const gradient = gradients[this.activeGradientIndex]

				this.updateGradient({
					gradient,
					newValue
				})
			}
		}
	},

	methods: {
		...mapActions([
			'addLocalGradient',
			'addGlobalGradient',
			'updateGradient',
			'deleteLocalGradient',
			'deleteGlobalGradient',
			'saveOptions'
		]),
		deleteGradientElement (index) {
			this.deleteLocalGradient(index)
		},
		deleteGlobalGradientElement (index) {
			this.deleteGlobalGradient(index)
			this.activeGradientIndex = this.getGlobalGradients.length - 1
		},
		onGradientSelect (index) {
			this.activeGradientIndex = index
			this.showModal = true
		},
		onGradientUpdate (gradient) {
			this.activeGradient = gradient
		},
		onSaveGradient () {
			this.saveOptions()
		},

		onAddNewGradient () {
			let arrayLength = this.getLocalGradients.length

			let dynamicName = `gradientPreset${arrayLength + 1}`
			const defaultGradient = {
				id: dynamicName,
				name: dynamicName,
				config: getDefaultGradient()
			}

			// Add the gradient to store
			this.addLocalGradient(defaultGradient)

			this.activeGradientIndex = this.getLocalGradients.length - 1
			this.showModal = true
		},
		onAddNewGlobalGradient () {
			let arrayLength = this.getGlobalGradients.length

			let dynamicName = `gradientPreset${arrayLength + 1}`
			const defaultGradient = {
				id: dynamicName,
				name: dynamicName,
				config: getDefaultGradient()
			}

			this.addGlobalGradient(defaultGradient)

			this.activeGradientIndex = this.getGlobalGradients.length === 1 ? 0 : this.getGlobalGradients.length - 1
			this.showModal = true
		}
	}
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
