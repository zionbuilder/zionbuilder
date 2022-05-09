import MD5 from 'crypto-js/md5';

export function hash(object) {
	return MD5(JSON.stringify(object));
}
