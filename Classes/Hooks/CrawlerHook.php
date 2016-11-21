<?php
namespace SourceBroker\Crawlerx\Hooks;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\IndexedSearch\Indexer;

/**
 * Crawler hook for indexed search. Works with the "crawler" extension
 */
class CrawlerHook extends \TYPO3\CMS\IndexedSearch\Hook\CrawlerHook
{

    /**
     * @var string
     */
    public $callBack = CrawlerHook::class;

    /**
     * Initialization of crawler hook.
     * This function is asked for each instance of the crawler and we must check if something is timed to happen and if so put entry(s) in the crawlers log to start processing.
     * In reality we select indexing configurations and evaluate if any of them needs to run.
     *
     * @param object $pObj Parent object (tx_crawler lib)
     * @return void
     */
    public function crawler_init(&$pObj)
    {
        // Select all indexing configuration which are waiting to be activated:
        $indexingConfigurations = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'index_config', 'hidden=0
				AND set_id=0
				' . BackendUtility::deleteClause('index_config'));
        // For each configuration, check if it should be executed and if so, start:
        foreach ($indexingConfigurations as $cfgRec) {
            // Generate a unique set-ID:
            $setId = GeneralUtility::md5int(microtime());
            // Get next time:
            $nextTime = $this->generateNextIndexingTime($cfgRec);
            // Start process by updating index-config record:
            $field_array = [
                'set_id' => $setId,
                'timer_next_indexing' => $nextTime,
                'session_data' => ''
            ];
            $GLOBALS['TYPO3_DB']->exec_UPDATEquery('index_config', 'uid=' . (int)$cfgRec['uid'], $field_array);
            // Based on configuration type:
            switch ($cfgRec['type']) {
                case 1:
                    // RECORDS:
                    // Parameters:
                    $params = [
                        'indexConfigUid' => $cfgRec['uid'],
                        'procInstructions' => ['[Index Cfg UID#' . $cfgRec['uid'] . ']'],
                        'url' => 'Records (start)'
                    ];
                    //
                    $pObj->addQueueEntry_callBack($setId, $params, $this->callBack, $cfgRec['pid']);
                    break;
                case 2:
                    // FILES:
                    // Parameters:
                    $params = [
                        'indexConfigUid' => $cfgRec['uid'],
                        // General
                        'procInstructions' => ['[Index Cfg UID#' . $cfgRec['uid'] . ']'],
                        // General
                        'url' => $cfgRec['filepath'],
                        // Partly general... (for URL and file types)
                        'depth' => 0
                    ];
                    $pObj->addQueueEntry_callBack($setId, $params, $this->callBack, $cfgRec['pid']);
                    break;
                case 3:
                    // External URL:
                    // Parameters:
                    $params = [
                        'indexConfigUid' => $cfgRec['uid'],
                        // General
                        'procInstructions' => ['[Index Cfg UID#' . $cfgRec['uid'] . ']'],
                        // General
                        'url' => $cfgRec['externalUrl'],
                        // Partly general... (for URL and file types)
                        'depth' => 0
                    ];
                    $pObj->addQueueEntry_callBack($setId, $params, $this->callBack, $cfgRec['pid']);
                    break;
                case 4:
                    // Page tree
                    // Parameters:
                    $params = [
                        'indexConfigUid' => $cfgRec['uid'],
                        // General
                        'procInstructions' => ['[Index Cfg UID#' . $cfgRec['uid'] . ']'],
                        // General
                        'url' => (int)$cfgRec['alternative_source_pid'],
                        // Partly general... (for URL and file types and page tree (root))
                        'depth' => 0
                    ];
                    $pObj->addQueueEntry_callBack($setId, $params, $this->callBack, $cfgRec['pid']);
                    break;
                case 5:
                    // Meta configuration, nothing to do:
                    // NOOP
                    break;
                default:
                    if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['indexed_search']['crawler'][$cfgRec['type']]) {
                        $hookObj = GeneralUtility::getUserObj($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['indexed_search']['crawler'][$cfgRec['type']]);
                        if (is_object($hookObj)) {
                            // Parameters:
                            $params = [
                                'indexConfigUid' => $cfgRec['uid'],
                                // General
                                'procInstructions' => ['[Index Cfg UID#' . $cfgRec['uid'] . '/CUSTOM]'],
                                // General
                                'url' => $hookObj->initMessage($message)
                            ];
                            $pObj->addQueueEntry_callBack($setId, $params, $this->callBack, $cfgRec['pid']);
                        }
                    }
            }
        }
        // Finally, look up all old index configurations which are finished and needs to be reset and done.
        $this->cleanUpOldRunningConfigurations();
    }


    /**
     * Indexing Single Record
     *
     * @param array $r Record to index
     * @param array $cfgRec Configuration Record
     * @param array $rl Rootline array to relate indexing to
     * @return void
     */
    public function indexSingleRecord($r, $cfgRec, $rl = null)
    {
        // Init:
        $rl = is_array($rl) ? $rl : $this->getUidRootLineForClosestTemplate($cfgRec['pid']);
        $fieldList = GeneralUtility::trimExplode(',', $cfgRec['fieldlist'], true);
        $languageField = $GLOBALS['TCA'][$cfgRec['table2index']]['ctrl']['languageField'];
        $sys_language_uid = $languageField ? $r[$languageField] : 0;
        // (Re)-Indexing a row from a table:
        $indexerObj = GeneralUtility::makeInstance(Indexer::class);

        // LOCC START
        $GETparams = $cfgRec['get_params'];
        preg_match_all('/###([a-zA-Z_]+)###/', $cfgRec['get_params'], $matches);
        $fieldsToReplace = $matches[1];
        foreach ($fieldsToReplace as $fieldToReplace) {
            if (isset($r[strtolower($fieldToReplace)])) {
                $GETparams = str_replace('###' . $fieldToReplace . '###', $r[strtolower($fieldToReplace)], $GETparams);
            }
        }
        parse_str($GETparams, $GETparamsParsed);
        // LOCC END

        $indexerObj->backend_initIndexer($cfgRec['pid'], 0, $sys_language_uid, '', $rl, $GETparamsParsed, 0);
        $indexerObj->backend_setFreeIndexUid($cfgRec['uid'], $cfgRec['set_id']);
        $indexerObj->forceIndexing = true;
        $theContent = '';
        $theTitle = '';
        foreach ($fieldList as $k => $v) {
            if (!$k) {
                $theTitle = $r[$v];
            } else {
                $theContent .= $r[$v] . ' ';
            }
        }
        //print_r($r[$GLOBALS['TCA'][$cfgRec['table2index']]['ctrl']['crdate']]);
        // Indexing the record as a page (but with parameters set, see ->backend_setFreeIndexUid())
        $indexerObj->backend_indexAsTYPO3Page(strip_tags(str_replace('<', ' <', $theTitle)), '', '', strip_tags(str_replace('<', ' <', $theContent)), $GLOBALS['LANG']->charSet, $r[$GLOBALS['TCA'][$cfgRec['table2index']]['ctrl']['tstamp']], $r[$GLOBALS['TCA'][$cfgRec['table2index']]['ctrl']['crdate']], $r['uid']);
    }
}
