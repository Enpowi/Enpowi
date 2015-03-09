<?php
if(!defined('Modular')) die('Direct access not permitted');

$parentDir = dirname(__FILE__) . '/../';
$not = array('.', '..', 'index.php', 'index.html');
$moduleFolders = array_diff(scandir($parentDir), $not);


$moduleMap = [
	'*'=> '*'
];

foreach($moduleFolders as $moduleFolder) {
	$componentsRaw = array_diff(scandir($parentDir . $moduleFolder), $not);

	$components = [];
	foreach($componentsRaw as $componentRaw) {
		if (!preg_match('/Service[.]php$/', $componentRaw)) {
			$components[] = preg_replace('/[.](php|html)$/i', '', $componentRaw);
		}
	}
	array_unshift($components, '*');
	$moduleMap[$moduleFolder] = $components;
}

$id = (new \Enpowi\Modules\DataOut())
	->add('moduleMap', $moduleMap)
	->bind();

?><form
	v-module
	data="<?php echo $id;?>">

	<table>
		<thead>
			<tr>
				<th v-t>Module</th>
				<th v-t>Component</th>
			</tr>
		</thead>
		<tbody>
			<tr v-repeat="components : moduleMap">
				<td>{{ $key }}</td>
				<td>
					<ul>
						<li v-repeat="component : components">{{ component }}</li>
					</ul>
				</td>
			</tr>
		</tbody>
	</table>

</form>
