/**
 * Загружает в тело существующей модалки новый контент из renderAjax-вью
 * @param {string} dataUrl
 * @param {string} modalDivId
 * @constructor
 */
function LoadModal(dataUrl, modalDivId) {
	let modal = $("#" + modalDivId),
		modalBody = modal.find('.modal-body');
	modalBody.load(dataUrl, function() {
		modal.modal('show');
	});
}

/**
 * Загружает renderPartial - вью в контейнер с прелоадером
 * @param {string} dataUrl
 * @param {string} modalDivId
 * @param {string} modalContainerId
 */
function AjaxModal(dataUrl, modalDivId, modalContainerId) {
	let modalContainerDiv;
	if (undefined === modalContainerId) {
		modalContainerId = 'modal-ajax-div';
	}
	modalContainerDiv = $("#" + modalContainerId);
	if (0 === modalContainerDiv.length) {
		modalContainerDiv = $('<div />', {
			'id': modalContainerId,
		});
		$('body').prepend(modalContainerDiv);
	}
	modalContainerDiv.addClass('preloading');
	modalContainerDiv.load(dataUrl, function() {
		$('#' + modalDivId).modal('show');
		modalContainerDiv.removeClass('preloading');
		document.dispatchEvent(new Event('modalIsReady'));
	}).show();
}

function formSubmitAjax(event) {
	event.preventDefault();
	var form = jQuery(event.target);
	var self = this;
	if (form.attr('method') !== 'GET' && window.FormData !== undefined) {

		// Convert form to ajax submit
		jQuery.ajax({
			method: form.attr('method'),
			url: form.attr('action'),
			data: new FormData(form[0]),
			processData: false,
			contentType: false,
			context: this,
			beforeSend: function(xhr, settings) {
				jQuery(self.element).triggerHandler('AjaxBeforeSubmit', [xhr, settings]);
			},
			success: function(data, status, xhr) {
				var contentType = xhr.getResponseHeader('content-type') || '';
				if (contentType.indexOf('html') > -1) {
					// Assume form contains errors if html
					this.injectHtml(data);
					status = false;
				}
				jQuery(self.element).triggerHandler('AjaxSubmit', [data, status, xhr, this.selector]);
			},
			complete: function(xhr, textStatus) {
				jQuery(self.element).triggerHandler('AjaxSubmitComplete', [xhr, textStatus]);
			}
		});
	} else {
		// Convert form to ajax submit
		jQuery.ajax({
			method: form.attr('method'),
			url: form.attr('action'),
			data: form.serialize(),
			context: this,
			beforeSend: function(xhr, settings) {
				jQuery(self.element).triggerHandler('AjaxBeforeSubmit', [xhr, settings]);
			},
			success: function(data, status, xhr) {
				var contentType = xhr.getResponseHeader('content-type') || '';
				if (contentType.indexOf('html') > -1) {
					// Assume form contains errors if html
					this.injectHtml(data);
					status = false;
				}
				jQuery(self.element).triggerHandler('AjaxSubmit', [data, status, xhr, this.selector]);
			},
			complete: function(xhr, textStatus) {
				jQuery(self.element).triggerHandler('AjaxSubmitComplete', [xhr, textStatus]);
			}
		});
	}

	return false;

};