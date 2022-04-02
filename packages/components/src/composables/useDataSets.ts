import { ref, Ref, computed } from 'vue';
import { getFontsDataSet } from '@zb/rest';

interface DataSet {
	id: string;
	name: string;
}
interface FontsList {
	google_fonts: DataSet[];
	custom_fonts: DataSet[];
	typekit_fonts: DataSet[];
}

export interface Icons {
	id: string;
	name: string;
	built_in: boolean;
	icons: {
		name: string;
		unicode: string;
	}[];
}

export interface DataSets {
	fonts_list?: FontsList;
	user_roles?: DataSet[];
	post_types?: DataSet[];
	taxonomies?: DataSet[];
	icons?: Icons[];
	image_sizes?: string[];
}

const dataSets: Ref<DataSets> = ref({
	fonts_list: {
		google_fonts: [],
		custom_fonts: [],
		typekit_fonts: [],
	},
	user_roles: [],
	post_types: [],
	icons: [],
	image_sizes: [],
});

getFontsDataSet().then(response => {
	dataSets.value = response.data;
});

export const useDataSets = () => {
	const fontsListForOption = computed(() => {
		let option = [
			{
				id: 'Arial',
				name: 'Arial',
			},
			{
				id: 'Times New Roman',
				name: 'Times New Roman',
			},
			{
				id: 'Verdana',
				name: 'Verdana',
			},
			{
				id: 'Trebuchet',
				name: 'Trebuchet',
			},
			{
				id: 'Georgia',
				name: 'Georgia',
			},
			{
				id: 'Segoe UI',
				name: 'Segoe UI',
			},
		];

		// Add fonts
		Object.keys(dataSets.value.fonts_list).forEach(fontProviderId => {
			const fontsList = dataSets.value.fonts_list[fontProviderId];

			option = [...fontsList, ...option];
		});

		return option;
	});

	const addIconsSet = iconSet => {
		dataSets.value.icons.push(iconSet);
	};

	const deleteIconSet = icons => {
		const iconsPackage = dataSets.value.icons.find(iconSet => {
			return iconSet.id === icons;
		});

		if (iconsPackage !== undefined) {
			const iconsPackageIndex = dataSets.value.icons.indexOf(iconsPackage);
			dataSets.value.icons.splice(iconsPackageIndex, 1);
		}
	};

	return {
		dataSets,
		fontsListForOption,
		addIconsSet,
		deleteIconSet,
	};
};
