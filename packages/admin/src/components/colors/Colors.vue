<template>
	<PageTemplate class="znpb-admin-colors__wrapper">
		<h3>{{$translate('color_presets')}}</h3>

		<Tabs tab-style="minimal">
			<Tab name="Local">
				<Sortable
					class="znpb-admin-colors__container"
					v-model="computedLocalColors"
					:revert="true"
					axis="horizontal"
				>
					<ColorBox
						v-for="(color,i) in computedLocalColors"
						:color="color"
						v-bind:key="color + i"
						@option-updated="editLocalColor(color, $event)"
						@delete-color="deleteLocalColor(color)"
					/>

					<template v-slot:end>
						<ColorBox
							type="addcolor"
							@option-updated="addLocalColor"
						/>
					</template>
				</Sortable>

			</Tab>
			<Tab name="Global">
				<UpgradeToPro
					v-if="!isPro"
					:message_title="$translate('pro_manage_global_colors_free_title')"
					:message_description="$translate('pro_manage_global_colors_free')"
				/>

				<template v-else>
					<Sortable
						class="znpb-admin-colors__container"
						v-model="computedGlobalColors"
						:revert="true"
						axis="horizontal"
					>
						<ColorBox
							v-for="(color,i) in computedGlobalColors"
							v-bind:key="color.color + i"
							:color="color.color"
							@option-updated="editGlobalColor(i, $event)"
							@delete-color="deleteGlobalColor(color)"
						/>

						<template v-slot:end>
							<ColorBox
								type="addcolor"
								@option-updated="addGlobal"
							/>
						</template>
					</Sortable>
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
import { useBuilderOptions } from '@zionbuilder/composables'
import { generateUID } from '@zb/utils'

// Components
import ColorBox from './ColorBox.vue'
import Sortable from '../../../../sortable/src/components/sortable/Sortable.vue'

export default {
	name: 'Colors',
	components: {
		ColorBox,
		Sortable
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
			updateOptionValue,
		} = useBuilderOptions()

		const computedLocalColors = computed({
			get () {
				return getOptionValue('local_colors')
			},
			set (newValue) {
				updateOptionValue('local_colors', newValue)

			}
		})
		const computedGlobalColors = computed({
			get () {
				return getOptionValue('global_colors')
			},
			set (newValue) {
				updateOptionValue('global_colors', newValue)

			}
		})


		function addGlobal (color) {
			const colorId = generateUID()
			let globalColor = {
				id: colorId,
				color: color,
				name: colorId
			}

			addGlobalColor(globalColor)
		}

		return {
			isPro,
			addLocalColor,
			editLocalColor,
			deleteLocalColor,
			computedLocalColors,
			computedGlobalColors,
			// Global colors
			addGlobal,
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
				color: var(--zb-primary-color);
			}
		}
	}
}
</style>
