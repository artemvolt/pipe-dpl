/**
 * @param {string} url
 * @param {object} data
 */
function post(url, data) {
	jQuery.ajax({
		url: url,
		data: data,
		method: 'POST'
	}).done(function(data) {
		if (true === data.success) {
		} else {
		}
	});
}