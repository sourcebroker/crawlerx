<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "crawlerx".
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'Extends crawler and indexed_search extension',
	'description' => 'Extends crawler and indexed_search extension to make it easier to implement complex indexed_search/crawler scenarios.',
	'category' => 'be',
	'version' => '1.0.0',
	'state' => 'stable',
	'uploadfolder' => false,
	'createDirs' => '',
	'clearcacheonload' => true,
	'author' => 'SourceBroker Team',
	'author_email' => 'office@sourcebroker.net',
	'author_company' => 'SourceBroker',
	'constraints' => 
	array (
		'depends' => 
		array (
			'typo3' => '6.2.0-7.6.99',
			'crawler' => '5.0.0-5.0.9',
		),
		'conflicts' => 
		array (
		),
		'suggests' => 
		array (
		),
	),
);

?>