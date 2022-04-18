export default function (s) {
	return s.replace(/([-_][a-z])/gi, $1 => {
		return $1.toUpperCase().replace('-', '').replace('_', '');
	});
}
