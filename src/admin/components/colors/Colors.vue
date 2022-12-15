<template>
	<PageTemplate class="znpb-admin-colors__wrapper">
		<h3>{{ $translate('color_presets') }}</h3>

		<Tabs tab-style="minimal">
			<Tab name="Local">
				<Sortable v-model="computedLocalColors" class="znpb-admin-colors__container" :revert="true" axis="horizontal">
					<ColorBox
						v-for="(color, i) in computedLocalColors"
						:key="color + i"
						:color="color"
						@option-updated="editLocalColor(color, $event)"
						@delete-color="deleteLocalColor(color)"
					/>

					<template #end>
						<ColorBox type="addcolor" @option-updated="addLocalColor" />
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
						v-model="computedGlobalColors"
						class="znpb-admin-colors__container"
						:revert="true"
						axis="horizontal"
					>
						<ColorBox
							v-for="(color, i) in computedGlobalColors"
							:key="color.color + i"
							:color="color.color"
							@option-updated="editGlobalColor(i, $event)"
							@delete-color="deleteGlobalColor(color)"
						/>

						<template #end>
							<ColorBox type="addcolor" @option-updated="addGlobal" />
						</template>
					</Sortable>
				</template>
			</Tab>
		</Tabs>
		<template #right>
			<p class="znpb-admin-info-p">{{ $translate('create_color_palette') }}</p>
		</template>
	</PageTemplate>
</template>

<script>
import { computed } from 'vue';

// Components
import ColorBox from './ColorBox.vue';

const { generateUID } = window.zb.utils;
const { useBuilderOptionsStore } = window.zb.store;
const { Sortable } = window.zb.components;
export default {
	name: 'Colors',
	components: {
		ColorBox,
		Sortable,
	},
	setup() {
		const isPro = window.ZnPbAdminPageData.is_pro_active;
		const {
			addLocalColor,
			getOptionValue,
			deleteLocalColor,
			editLocalColor,
			addGlobalColor,
			deleteGlobalColor,
			editGlobalColor,
			updateOptionValue,
		} = useBuilderOptionsStore();

		const computedLocalColors = computed({
			get() {
				return getOptionValue('local_colors');
			},
			set(newValue) {
				updateOptionValue('local_colors', newValue);
			},
		});
		const computedGlobalColors = computed({
			get() {
				return getOptionValue('global_colors');
			},
			set(newValue) {
				updateOptionValue('global_colors', newValue);
			},
		});

		function addGlobal(color) {
			const colorId = generateUID();
			let globalColor = {
				id: colorId,
				color: color,
				name: colorId,
			};

			addGlobalColor(globalColor);
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
			editGlobalColor,
		};
	},
};
</script>
<style lang="scss">
.znpb-admin-colors__wrapper {
	.znpb-tabs__header {
		margin: 0 auto;
		&-item {
			padding: 15px 20px 30px 0;

			&--active,
			&:hover {
				color: var(--zb-primary-color);
			}
		}
	}
}
</style>
