class Cache {
	static CACHE_KEY = 'znpb_page_cache'

	getCacheKey(postId: String) {
		const cacheKey = this.constructor.CACHE_KEY
		return `${cacheKey}-${postId}`
	}

	saveItem(postId: Number, data: Object) {
		const key = this.getCacheKey(postId)
		localStorage.setItem(key, JSON.stringify(data))
	}

	getItem(postId: Number) {
		const key = this.getCacheKey(postId)
		return JSON.parse(localStorage.getItem(key))
	}

	deleteItem(postId: Number) {
		const key = this.getCacheKey(postId)
		localStorage.removeItem(key)
	}
}

export default new Cache()
