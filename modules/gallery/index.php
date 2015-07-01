<?php
use Enpowi\App;
use Enpowi\Files\Gallery;
use Enpowi\Modules\Module;
use Enpowi\Modules\DataOut;
Module::is();

$galleryId = App::paramInt('g');
$gallery = null;
$images = null;
$galleries = null;
$galleriesImages = null;

if ($galleryId > 0) {
	$possibleGallery = new Gallery($galleryId);
	if ($possibleGallery->userId === App::user()->id) {
		$gallery = $possibleGallery;
		$images = $gallery->images(App::paramInt('page'));
	}
} else {
	$galleriesImages = [];
	$galleries = Gallery::galleries(App::get()->user()->id, App::paramInt('page'));
	foreach ($galleries as $_gallery) {
		$images = $_gallery->images(1);
		if (isset($images[0])) {
			$galleriesImages[] = $images[0]->hash;
		}
	}
}

$data = (new DataOut())
	->add('galleries', $galleries)
	->add('galleriesImages', $galleriesImages)
	->add('gallery', $gallery)
	->add('images', $images)
	->add('g', $galleryId)
	->out();
?>
<div
	data="<?php echo $data;?>"
	v-module
	class="container">
	<!--galleries list-->
	<div
		v-show="galleries !== null">
		<h3>
			<span v-t>Galleries</span>
			<a
				v-show="g < 1"
				v-title="Create Gallery"
				href="#/gallery/create"><span class="glyphicon glyphicon-plus-sign"></span></a>
		</h3>
		<div
			class="galleries"
			style="opacity: 0.01">
			<img
				v-repeat="gallery : galleries"
				title="{{ gallery.name }}"
				v-show="galleriesImages[ $index ]"
				src="modules/?m=gallery&c=image&image={{ galleriesImages[ $index ] }}"
				href="#/gallery?g={{ gallery.id }}">
		</div>
	</div>

	<!--gallery images-->
	<div v-show="galleries === null">
		<h3>
			<a v-t href="#/gallery">Gallery:</a>
			<span>{{ gallery ? gallery.name : '' }}</span>
			<a
				v-title="Upload Images"
				href="#/gallery/upload?g={{ g }}"><span class="glyphicon glyphicon-plus-sign"></span></a>

			<a
				v-title="Back to Gallery"
				href="#/gallery"><span class="glyphicon glyphicon-backward pull-right"></span></a>
		</h3>
		<div
			class="gallery"
			style="opacity: 0.01;">
			<img
				v-repeat="image : images"
				title="{{ image.name }}"
				src="modules/?m=gallery&c=image&image={{ image.hash }}"
				data-fullsrc="modules/?m=gallery&c=image&image={{ image.hash }}"
				data-desc="{{ image.description }}">
		</div>
	</div>
</div>
<link href="vendor/galereya/dist/css/jquery.galereya.css" rel="stylesheet"/>
<script src="vendor/galereya/dist/js/jquery.galereya.js"></script>
<script>
	if (datas[0].galleries === null) {
		app.oneTo()._continue(function () {
			$('div.gallery')
				.galereya({
					wave: false
				})
				.css('opacity', '1');
		});
	} else {
		app.oneTo()._continue(function () {
			$('div.galleries')
				.mousedown(function(e) {
					var href = $(e.target)
						.parent()
						.find('img:first')
						.attr('href');

					document.location = href;
				})
				.galereya({
					disableSliderOnClick: true,
					wave: false
				})
				.css('opacity', '1');
		});
	}
</script>