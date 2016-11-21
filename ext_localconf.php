<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

// Register with "crawler" extension:
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['crawler']['cli_hooks']['tx_indexedsearch_crawl'] = \SourceBroker\Crawlerx\Hooks\CrawlerHook::class;
//// Register with TCEmain:
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['tx_indexedsearch'] = \SourceBroker\Crawlerx\Hooks\CrawlerHook::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['tx_indexedsearch'] = \SourceBroker\Crawlerx\Hooks\CrawlerHook::class;


$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = \SourceBroker\Crawlerx\Command\CrawlerxCommandController::class;
