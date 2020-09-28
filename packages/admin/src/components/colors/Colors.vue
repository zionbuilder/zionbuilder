<template>
	<PageTemplate class="znpb-admin-colors__wrapper">
		<h3>{{$translate('color_presets')}}</h3>

		<Tabs tab-style="minimal">
			<Tab name="Local">
				<div class="znpb-admin-colors__container">
					<ColorBox
						v-for="(color,i) in localColorPatterns"
						:color="color"
						v-bind:key="color + i"
						@option-updated="editLColor(i,$event)"
						@delete-color="deleteLColor(i)"
					/>
				</div>
				<template v-slot:end>
					<ColorBox
						type="addcolor"
						@option-updated="addLColor"
					/>
				</template>
			</Tab>
			<Tab name="Global">
				<UpgradeToPro
					v-if="!isPro"
					:message_title="$translate('pro_manage_global_colors_free_title')"
					:message_description="$translate('pro_manage_global_colors_free')"
				/>
				<template v-else>

					<div class="znpb-admin-colors__container">
						<ColorBox
							type="addcolor"
							@option-updated="addGColor"
						/>

						<ColorBox
							v-for="(color,i) in globalColorPatterns"
							v-bind:key="color.color + i"
							:color="color.color"
							@option-updated="editGColor(i, $event)"
							@delete-color="deleteGColor(i)"
						/>
					</div>
				</template>

			</Tab>
		</Tabs>
		<template v-slot:right>
			<p class="znpb-admin-info-p">{{$translate('create_color_palette')}} </p>
		</template>
	</PageTemplate>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import ColorBox from './ColorBox.vue'
import { generateUID } from '@zb/utils'
import { Tabs, Tab, UpgradeToPro } from '@zb/components'

export default {
	name: 'Colors',
	components: {
		ColorBox,
		UpgradeToPro,
		Tabs,
		Tab
	},
	computed: {
		...mapGetters([
			'getOptionValue',
			'isPro'
		]),
		localColorPatterns () {
			return this.getOptionValue('local_colors')
		},
		globalColorPatterns () {
			return this.getOptionValue('global_colors')
		}
	},
	methods: {
		...mapActions([
			'addLocalColor',
			'deleteLocalColor',
			'editLocalColor',
			'addGlobalColor',
			'deleteGlobalColor',
			'editGlobalColor'
		]),
		// local colors
		editLColor (index, color) {
			this.editLocalColor({ index, color })
		},
		addLColor (color) {
			this.addLocalColor(color)
		},
		deleteLColor (index) {
			this.deleteLocalColor(index)
		},
		// global colors
		addGColor (color) {
			let globalColor = {
				id: generateUID(),
				color: color,
				name: color
			}
			this.addGlobalColor(globalColor)
		},
		editGColor (index, newcolor) {
			let editColor = this.globalColorPatterns[index]

			let clone = { ...editColor }
			clone.color = newcolor

			this.editGlobalColor({ index: index, color: clone })
		},
		deleteGColor (index) {
			this.deleteGlobalColor(index)
		}
	}
}
</script>
<style lang="scss" >
.znpb-admin-colors__wrapper {
	.znpb-tabs__header {
		margin: 0 auto;
		&-item {
			padding: 15px 20px 30px 0;

			&--active, &:hover {
				color: $primary-color;
			}
		}
	}
}
</style>
