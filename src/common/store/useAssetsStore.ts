import { defineStore } from 'pinia';
import { regenerateCache, getCacheList, finishRegeneration } from '/@/common/api';

export const useAssetsStore = defineStore('assets', {
	state: () => {
		return {
			isLoading: false,
			currentIndex: 0,
			filesCount: 0,
		};
	},
	actions: {
		async regenerateCache() {
			this.isLoading = true;
			try {
				const { data: cacheFiles } = await getCacheList();
				this.filesCount = cacheFiles.length;

				if (this.filesCount > 0) {
					for (const fileData of cacheFiles) {
						try {
							this.currentIndex++;
							await regenerateCache(fileData);
						} catch (error) {
							console.error(error);
						}
					}
				}
			} catch (error) {
				console.error(error);
			}

			this.isLoading = false;
			this.filesCount = 0;
			this.currentIndex = 0;
		},
		finish() {
			return finishRegeneration();
		},
	},
});
