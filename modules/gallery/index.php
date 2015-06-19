<?php
use Enpowi\Files\File;
use Enpowi\Modules\Module;
use Enpowi\Modules\DataOut;
Module::is();

$files = File::getUserFiles();

$data = (new DataOut())
	->add('images', $files)
	->out();
?>
<div
	v-module
	data="<?php echo $data;?>"
	class="container">
	<h3>
		<span v-t>Gallery</span>
		<a v-title="Upload Images" href="#/gallery/upload"><span class="glyphicon glyphicon-plus-sign"></span></a>
	</h3>
	<div class="galereya" style="opacity: 0.01;">
		<img
			v-repeat="image : images"
			title="{{ image.name }}"
			src="modules/?m=gallery&c=image&image={{ image.hash }}"
			data-fullsrc="modules/?m=gallery&c=image&image={{ image.hash }}"
			data-desc="{{ image.description }}">
	</div>
</div>
<link href="vendor/galereya/dist/css/jquery.galereya.css" rel="stylesheet"/>
<script src="vendor/galereya/dist/js/jquery.galereya.js"></script>
<script>
	app.oneTo()._continue(function() {
		$('div.galereya')
			.galereya({
				wave: false
			})
			.css('opacity', '1');
	});
</script>