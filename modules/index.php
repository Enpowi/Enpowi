<?php
require_once 'setup/run.php';

use Enpowi\App;
use Enpowi\Modules\Module;

$moduleName = App::param('module') ?: App::param('m');
$componentName = App::param('component') ?: App::param('c');
$path = dirname(__FILE__);
$component = App::loadComponent($path, $moduleName, $componentName);

if ($component !== null && !empty($component->file)) {
	if ($component->isActive()) {
		Module::run();
		$component->runInit();
		require_once $component->file;

		$paramResponse = Module::getParamResponse();
		if ( $paramResponse !== null ) {
			echo json_encode( [
				'paramResponse' => $paramResponse
			] );
		} else {
			$successResponse = Module::getSuccessResponse();
			if ( $successResponse !== null ) {
				echo json_encode( [
					'successResponse' => $successResponse
				] );
			}
		}
	} else {
		require_once $component->file;
	}
} else if (!defined('testing')) {
	echo -1;
}