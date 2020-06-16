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
						:config="gradient"
						@delete-gradient="deleteGradientElement(index)"
						@click.native="onGradientSelect(index)"
					/>

					<AddGradient @click.native="onAddNewGradient" />
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
							@click.native="onGradientSelect(index)"
							@delete-gradient="deleteGlobalGradientElement(index)"
						/>
						<AddGradient @click.native="onAddNewGlobalGradient" />
					</div>
				</template>

			</Tab>
		</Tabs>

		<GradientModalContent
			:show.sync="showModal"
			:gradient="activeGradient"
			@update-gradient="onGradientUpdate"
			@save-gradient="onSaveGradient"
		/>

		<template slot="right">
			<p class="znpb-admin-info-p">{{$translate('create_gradient_info')}} </p>
		</template>
	</PageTemplate>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'

import GradientBox from './GradientBox.vue'
import GradientModalContent from './GradientModalContent.vue'
import AddGradient from './AddGradient.vue'
import UpgradeToPro from '@/editor/manager/options/UpgradeToPro/UpgradeToPro.vue'

export default {
	name: 'Gradients',
	components: {
		GradientBox,
		AddGradient,
		GradientModalContent,
		UpgradeToPro
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
					return this.getLocalGradients[this.activeGradientIndex]
				} else {
					return this.getGlobalGradients !== undefined && this.getGlobalGradients[this.activeGradientIndex] !== undefined ? this.getGlobalGradients[this.activeGradientIndex].config : []
				}
			},
			set (newValue) {
				if (this.activeLibrary === 'local') {
					const gradients = [...this.getLocalGradients]
					gradients[this.activeGradientIndex] = newValue
					this.updateLocalGradients(gradients)
				} else {
					const globalGradients = [...this.getGlobalGradients]
					let newState = [
						...globalGradients.slice(0, this.activeGradientIndex),
						{
							...globalGradients[this.activeGradientIndex],
							config: newValue
						},
						...globalGradients.slice(this.activeGradientIndex + 1, globalGradients.length)
					]

					this.updateGlobalGradients(newState)
				}
			}
		}
	},

	methods: {
		...mapActions([
			'addLocalGradient',
			'addGlobalGradient',
			'updateLocalGradients',
			'updateGlobalGradients',
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
			this.addLocalGradient([
				{
					'type': 'linear',
					'angle': 114,
					'colors': [
						{
							'color': '#18208d',
							'position': 0
						},
						{
							'color': '#06bee1',
							'position': 100
						}
					],
					'position': {
						'x': 75,
						'y': 48
					}
				}
			])

			this.activeGradientIndex = this.activeGradientIndex + 1
			this.showModal = true
		},
		defaultGlobalObject () {
			return [
				{
					'type': 'linear',
					'angle': 114,
					'colors': [
						{
							'color': '#18208d',
							'position': 0
						},
						{
							'color': '#06bee1',
							'position': 100
						}
					],
					'position': {
						'x': 75,
						'y': 48
					}
				}
			]
		},
		onAddNewGlobalGradient () {
			let arrayLength = this.getGlobalGradients.length

			let dynamicName = `gradientPreset${arrayLength + 1}`
			const defaultGradient = {
				id: dynamicName,
				config: this.defaultGlobalObject()
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
