<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "restrictfe".
 *
 * Auto generated | Identifier: d28cd7a1db7384cbe224017186c3e7af
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'Extends crawler extension',
	'description' => 'Allows to use more markers for link building.',
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