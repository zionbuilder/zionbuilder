<template>
	<PageTemplate class="znpb-admin-colors__wrapper">
		<h3>{{$translate('color_presets')}}</h3>

		<Tabs tab-style="minimal">
			<Tab name="Local">
				<div class="znpb-admin-colors__container">
					<ColorBox
						v-for="(color,i) in localColors"
						:color="color"
						v-bind:key="color + i"
						@option-updated="editLocalColor(color, $event)"
						@delete-color="deleteLocalColor(color)"
					/>
					<ColorBox
						type="addcolor"
						@option-updated="addLocalColor"
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
							@option-updated="addGlobalColor"
						/>

						<ColorBox
							v-for="(color,i) in globalColors"
							v-bind:key="color.color + i"
							:color="color.color"
							@option-updated="editGlobalColor(i, $event)"
							@delete-color="deleteGlobalColor(color)"
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
import { computed } from 'vue'
import { generateUID } from '@zionbuilder/utils'
import { useBuilderOptions } from '@zionbuilder/composables'

// Components
import ColorBox from './ColorBox.vue'

export default {
	name: 'Colors',
	components: {
		ColorBox
	},
	setup () {
		const isPro = window.ZnPbAdminPageData.is_pro_active
		const {
			addLocalColor,
			getOptionValue,
			deleteLocalColor,
			editLocalColor,
			addGlobalColor,
			deleteGlobalColor,
			editGlobalColor,
		} = useBuilderOptions()
		const localColors = getOptionValue('local_colors')
		const globalColors = getOptionValue('global_colors')

		return {
			isPro,
			localColors,
			addLocalColor,
			editLocalColor,
			deleteLocalColor,
			// Global colors
			globalColors,
			addGlobalColor,
			deleteGlobalColor,
			editGlobalColor
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
