<?php
	$theme = Array();

	$theme['name'] = 'Hot Threads';
	$theme['description'] = 'Standalone page showing threads with the most replies in the selected time window.';
	$theme['version'] = 'v1.3.0';

	$theme['config'] = Array();

	$theme['config'][] = Array(
		'title' => 'Site title',
		'name' => 'title',
		'type' => 'text'
	);

	$theme['config'][] = Array(
		'title' => 'Slogan',
		'name' => 'subtitle',
		'type' => 'text',
		'comment' => '(optional)'
	);

	$theme['config'][] = Array(
		'title' => 'File',
		'name' => 'file',
		'type' => 'text',
		'default' => 'hotthreads.html',
		'comment' => '(eg. "hotthreads.html")'
	);

	$theme['config'][] = Array(
		'title' => 'Reply window (hours)',
		'name' => 'hours',
		'type' => 'text',
		'default' => 48,
		'size' => 4,
		'comment' => '(how far back replies are counted)'
	);

	$theme['config'][] = Array(
		'title' => '# of hot threads',
		'name' => 'limit',
		'type' => 'text',
		'default' => 20,
		'size' => 4,
		'comment' => '(maximum number of threads to display)'
	);

	$theme['config'][] = Array(
		'title' => 'Excluded boards',
		'name' => 'exclude',
		'type' => 'text',
		'comment' => '(space separated board URIs)'
	);

	$theme['build_function'] = 'hotthreads_build';
	$theme['install_callback'] = 'hotthreads_install';

	if (!function_exists('hotthreads_install')) {
		function hotthreads_install($settings) {
			if (!is_numeric($settings['hours']) || $settings['hours'] < 1)
				return Array(false, '<strong>' . utf8tohtml($settings['hours']) . '</strong> is not a positive integer.');

			if (!is_numeric($settings['limit']) || $settings['limit'] < 1)
				return Array(false, '<strong>' . utf8tohtml($settings['limit']) . '</strong> is not a positive integer.');

			return Array(true);
		}
	}
?>
