;(function ($) {
    window.LP_Importer_User = {
        init    : function (args) {
            var uploader = new plupload.Uploader({
                runtimes      : 'html5,flash,silverlight,html4',
                browse_button : 'import-user-uploader-select',
                container     : $('#import-user-uploader').get(0),
                url           : args.url,
                filters       : {
                    max_file_size: '40mb',
                    mime_types   : [
                        {title: "CSV", extensions: "csv"},
                        {title: "XLSX", extensions: "xlsx"},
                    ]
                },
                file_data_name: 'lpie_import_user_file',
                init          : {
                    PostInit: function () {
                        $(document).on('click', '#import-user-start-upload', function () {
                            uploader.setOption('multipart_params', $('#import-user-form').serializeJSON());
                            uploader.start();
                            return false;
                        });
                    },

                    FilesAdded: function (up, files) {
                        up.files.splice(0, up.files.length - 1);
                        plupload.each(files, function (file) {
                            $('#import-user-uploader-select').addClass('has-file').html('<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <strong></strong></div>');
                            $('#import-user-start-upload').addClass('has-file');
                        });
                    },

                    UploadProgress: function (up, file) {
                        $('#' + file.id + ' strong').html(file.percent + "%");
                    },

                    FileUploaded: function (up, file, info) {
                        $('#import-user-form').replaceWith($(info.response).contents().find('#import-user-form'))
                    },

                    Error: function (up, err) {
                        document.getElementById('error-import-user').innerHTML += "\nError #" + err.code + ": " + err.message;
                    }
                }
            });
            uploader.init();

            $(document).on('submit', 'form[name="import-user-form"]', function () {
                var $form = $(this),
                    step = $form.find('[name="step"]').val();

                $.ajax({
                    url     : $form.attr('action'),
                    data    : $form.serialize(),
                    dataType: 'html',
                    success : function (res) {
                        var $newHtml = $(res).contents().find('form[name="import-user-form"]');
                        $form.replaceWith($newHtml);
                    }
                });
                return false;
            })
        },
    };

})(jQuery);