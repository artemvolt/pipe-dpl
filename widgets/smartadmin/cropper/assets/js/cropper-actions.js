let cropperInitConfig = {
	image: null,
	modal: null,
	uploadedImageURL: null,
	cropperOptions: {},
	cropperUploadInput: null,
	cropperCropElement: null,
	uploadUrl: '/users/logo-upload',
	fileInputName: 'croppedImage',
	userLogos: $('.user-logo'),
	init: function(options) {
		this.image = $(options.imageId);
		if (options.modalId) {
			this.modal = $(options.modalId);
		}
		this.cropperOptions = options.pluginOptions;
		this.cropperUploadInput = $(options.cropperUploadInputId);
		this.cropperCropElement = $(options.cropperCropElementId);
		this.fileInputName = options.fileInputName;
		if (this.modal) {
			this.initModalEvents();
		} else {
			this.initCropper();
		}
		this.initEventsOnUpload();
		this.initEventsOnCrop();
	},
	initEventsOnUpload: function() {
		let _this = this;
		this.cropperUploadInput.on('change', function() {
			let files = this.files;
			let file;

			if (files && files.length) {
				file = files[0];
				if (/^image\/\w+/.test(file.type)) {
					if (_this.uploadedImageURL) {
						URL.revokeObjectURL(_this.uploadedImageURL);
					}

					_this.uploadedImageURL = URL.createObjectURL(file);
					_this.getImgCropper().replace(_this.uploadedImageURL);

					this.value = null;
				} else {
					toastr.warning('Недопустимый формат файла');
				}
			}
		})
	},
	initEventsOnCrop: function() {
		let _this = this;
		this.cropperCropElement.on('click', function() {
			_this.getImgCropper().getCroppedCanvas({width: 300, height: 300}).toBlob((blob) => {
				const formData = new FormData();

				formData.append(_this.fileInputName, blob);

				$.ajax(_this.uploadUrl, {
					method: 'POST',
					data: formData,
					processData: false,
					contentType: false,
				}).done(function() {
					toastr.success('Изображение успешно изменено');

					if (_this.modal) {
						_this.modal.modal('hide');
					}

					let timestamp = new Date().getTime();
					_this.userLogos.each(function () {
						let url = new URL(window.location.origin + $(this).attr('src'));
						url.searchParams.set('t', timestamp);

						$(this).attr('src', url.pathname + url.search);
					});

					_this.getImgCropper().reset();
				}).fail(function(data) {
					toastr.error(data.responseJSON.error);
				})
			});
		});
	},
	initModalEvents: function() {
		let _this = this;
		this.modal.on('shown.bs.modal', function() {
			if (_this.getImgCropper() === undefined) {
				_this.initCropper();
			}
		});
	},
	initCropper: function() {
		this.image.cropper(this.cropperOptions);
	},
	getImgCropper: function() {
		return this.image.data('cropper');
	}
}