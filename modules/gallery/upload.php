<?php
use Enpowi\Modules\Module;
Module::is();
?>
<form
	class="form container"
	action="gallery"
	v-module>
	<h3 v-t>Upload image</h3>
	<input id="image" name="files[]" type="file" multiple="true">

</form>

<link href="vendor/bootstrap-fileinput/css/fileinput.min.css" rel="stylesheet">
<script src="vendor/bootstrap-fileinput/js/fileinput.min.js"></script>
<script>
	$(app.getElementById('image')).fileinput({
		uploadAsync: false,
		uploadUrl: "modules/?module=gallery&component=uploadService",
		allowedFileExtensions: ["jpg", "png", "gif"]
	});
</script>