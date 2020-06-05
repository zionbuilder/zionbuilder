<template>
	<PageTemplate class="znpb-admin-gradients__wrapper">
		<h3>{{$translate('gradients')}}</h3>
		<Tabs
			tab-style="minimal"
			@changed-tab="activeLibrary=$event"
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
							v-for="(gradient, index) in getArrayGradients"
							:key="index"
							:config="gradient"
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
			activeGlGradientIndex: 0,
			activeLibrary: 'Local'
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
					return this.getArrayGradients[this.activeGlGradientIndex]
				}
			},
			set (newValue) {
				if (this.activeLibrary === 'local') {
					const gradients = [...this.getLocalGradients]
					gradients[this.activeGradientIndex] = newValue
					this.updateLocalGradients(gradients)
				} else {
					// to do update global gradient
				}
			}
		},
		getArrayGradients () {
			let newArray = []
			this.getGlobalGradients.forEach((item) => {
				let gradientName = Object.keys(item)
				let newObject = item[gradientName]

				newArray.push(Object.values(newObject))
			})
			return newArray
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
		onAddNewGlobalGradient () {
			let arrayLength = this.getGlobalGradients.length
			let dynamicName = `gradientPreset${arrayLength}`
			const defaultGradient = {}
			defaultGradient[dynamicName] = {
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
			this.addGlobalGradient(
				[defaultGradient]
			)

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
