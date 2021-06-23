/**
 * @param {string} name
 * @param {boolean} value
 * @param {string} statusContainerId
 * @param {string} url
 */
function SetSystemOptionBool(name, value, statusContainerId, url) {
	statusContainerId = statusContainerId || name;
	let statusContainer = $("#" + statusContainerId);
	statusContainer.removeClass('btn-danger').removeClass('btn-success').addClass('btn-info');
	jQuery.ajax({
		url: url || '/ajax/set-system-option',
		data: {
			name: name,
			value: value,
			type: typeof (value)
		},
		method: 'POST'
	}).done(function(data) {
		if (true === data.success) {
			statusContainer.removeClass('btn-info').addClass('btn-success');
		} else {
			statusContainer.removeClass('btn-info').addClass('btn-danger');
		}
	});
}