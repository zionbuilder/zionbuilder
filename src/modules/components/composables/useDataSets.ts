import { ref, computed } from 'vue';
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

export interface IconSet {
	id: string;
	name: string;
	built_in: boolean;
	icons: {
		name: string;
		unicode: string;
	}[];
}

export interface DataSets {
	fonts_list: FontsList;
	user_roles: DataSet[];
	post_types: DataSet[];
	taxonomies?: DataSet[];
	icons: IconSet[];
	image_sizes?: string[];
}

const dataSets = ref<DataSets>({
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
		const option = [
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
		Object.values(dataSets.value.fonts_list).forEach(option => {
			option.push(...option);
		});

		return option;
	});

	const addIconsSet = (iconSet: IconSet) => {
		dataSets.value.icons.push(iconSet);
	};

	const deleteIconSet = (icons: string) => {
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
