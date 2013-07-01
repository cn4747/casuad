        <h1>Загрузить видео</h1>

        <div id="register">

            <div id="uploader-container">
                <div id="filelist">Нужно установить Flash, чтобы загружать видео.</div>

                <div class="button" style="margin: 5px 10px;"><a id="quick-add" href="javascript: void(0);">Быстрое добавление</a></div>
                <div class="button" style="margin: 5px 10px;"><a id="pickfiles" href="javascript: void(0);">Выбрать файл</a></div>
                <div class="button" style="margin: 5px 10px;"><a id="uploadfiles" href="javascript: void(0);">Загрузить</a></div>
            </div>

        <form action="" method="post" accept-charset="utf-8" id="upload-form">

            {message}

            <input type="hidden" name="filename" value=""/>

            <div class="text">Ключевые слова (*)</div>
            <input required="required" type="text" class="input-text" name="keywords" value="" />
            <div class="clear"></div>

            <div class="text">Название видео</div>
            <input type="text" class="input-text" name="title" value="" />
            <div class="clear"></div>

            <div class="text">Описание видео</div>
            <textarea rows="4" cols="45" class="input-text" name="descr"></textarea>
            <div class="clear"></div>

            <div class="button button-margin"><input type="submit" name="save" value="Сохранить"/></div>

        </form>
        </div>

        <div id="loader-place"><img src="{base_url}images/loading.gif" alt=""/></div>

        <div id="swf-object" align="center" style="display: none; padding-bottom:20px;">
            <OBJECT
                classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
                codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"
                WIDTH="509"
                HEIGHT="358"
                wmmode="opaque"
                id="videorecord.swf" ALIGN="">
    	     <param Name="wmode" Value="opaque"> 
             <PARAM NAME=movie VALUE="/videorecord.swf"> <PARAM NAME=quality VALUE=high> <PARAM NAME=bgcolor VALUE=#333399>
             <EMBED src="/videorecord.swf" quality=high bgcolor=#333399 WIDTH="509" HEIGHT="358" NAME="videorecord.swf" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED>
            </OBJECT>
        </div>

        <script type="text/javascript">
	    function record_complete (filename) {
		$('#loader-place').show();
		$('#swf-object').css('visibility','hidden');
		setTimeout(function(){
		    $('#loader-place').hide();
			$('#uploader-container').hide();
			$('#upload-form').show();
			$('#upload-form input[name="filename"]').val(filename + '.flv');
			$('<input type="hidden" name="stream" value="1">').appendTo('#upload-form');
		},5000);
		
	    }
            $(function() {
                var uploader = new plupload.Uploader({
                    runtimes : 'html5,flash',
                    browse_button : 'pickfiles',
                    container : 'uploader-container',
                    max_file_size : '100mb',
                    //max_total_queue : 1,
                    chunk_size : '100kb',
                    url : '{base_url}upload',
                    flash_swf_url : 'js/plupload.flash.swf',
                    filters : [],
                    filters : [
                        {title : "Video files", extensions : "3g2,3gp,asf,asx,avi,flv,mov,mp4,mpg,rm,srt,swf,vob,wmv"},
                    ]
                    //resize : {width : 320, height : 240, quality : 90}
                });

                uploader.bind('Init', function(up, params) {
                    $('#filelist').html('');
                });

                $('#uploadfiles').click(function(e) {
                    $('#loader-place').show();
                    uploader.start();
                    e.preventDefault();
                });

                uploader.init();

                uploader.bind('FilesAdded', function(up, files) {
                    $.each(files, function(i, file) {
                        if($('#filelist div').length < 1) {
                            var pattern = /[^\w\._]+/ig;
                            file.name = file.name.replace(pattern, '_');

                            $('#filelist').append(
                                '<div id="' + file.id + '">' +
                                file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
                            '</div>');
                        }
                    });

                    up.refresh(); // Reposition Flash/Silverlight
                });

                uploader.bind('UploadProgress', function(up, file) {
                    $('#' + file.id + " b").html(file.percent + "%");
                });

                uploader.bind('Error', function(up, err) {
                    $('#filelist').append("<div>Ошибка " + err.code +
                        ": " + err.message +
                        (err.file ? ", Файл: " + err.file.name : "") +
                        "</div>"
                    );

                    up.refresh(); // Reposition Flash/Silverlight
                });

                uploader.bind('FileUploaded', function(up, file) {
                    $('#loader-place').hide();
                    $('#' + file.id + " b").html("100%");
                    $('#uploader-container').hide();
                    $('#upload-form').show();
                    $('#upload-form input[name="filename"]').val(file.name);
                });

                $('#quick-add').click(function() {
                    $('#swf-object').show()
                });
            });
        </script>