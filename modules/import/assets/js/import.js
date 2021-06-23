function RefreshProgress(processImportUrl, progressWidgetId) {
	let progressBar = $("#" + progressWidgetId).find('[role="progressbar"]');
	jQuery.ajax({
		url: processImportUrl,
		async: true,
		method: 'GET'
	}).done(function(data) {
		progressBar.css("width", data.percent + '%');
		if (data.messages) {
			console.log(data.messages);
		}
		if (true === data.done) {
			window.location.reload();
		} else {
			RefreshProgress(processImportUrl, progressWidgetId);
		}
	})
}