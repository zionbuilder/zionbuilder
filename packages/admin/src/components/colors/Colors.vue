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
						@option-updated="editLColor(color,$event)"
						@delete-color="deleteLColor(color)"
					/>
					<ColorBox
						type="addcolor"
						@option-updated="addLColor"
					/>
				</div>

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
							@delete-color="deleteGColor(color)"
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

import ColorBox from './ColorBox.vue'
import { generateUID } from '@zionbuilder/utils'

export default {
	name: 'Colors',
	components: {
		ColorBox
	},
	computed: {
		isPro () {
			return window.ZnPbAdminPageData.is_pro_active
		},
		localColorPatterns () {
			return this.$zb.options.getOptionValue('local_colors')
		},
		globalColorPatterns () {
			return this.$zb.options.getOptionValue('global_colors')
		}
	},
	methods: {
		// local colors
		editLColor (oldColor, color) {
			let key = oldColor
			let value = color
			this.$zb.options.updateOptionValue('local_colors', { key, value })
		},
		addLColor (color) {
			this.$zb.options.addOptionValue('local_colors', color)
		},
		deleteLColor (color) {
			this.$zb.options.deleteOptionValue('local_colors', color)
		},
		// global colors
		addGColor (color) {
			let globalColor = {
				id: generateUID(),
				color: color,
				name: color
			}
			this.$zb.options.addOptionValue('global_colors', globalColor)
		},
		editGColor (index, newcolor) {
			let editColor = this.globalColorPatterns[index]

			let clone = { ...editColor }
			clone.color = newcolor

			this.$zb.options.editGlobalColor({ index: index, color: clone })
		},
		deleteGColor (color) {
			this.$zb.options.deleteOptionValue('global_colors', color)
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
