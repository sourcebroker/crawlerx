<?php

namespace SourceBroker\Crawlerx\Command;

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

use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Class CrawlerxCommandController
 * @package SourceBroker\Crawlerx\Command
 */
class CrawlerxCommandController extends CommandController
{

    protected $tablesToTruncate = [
        'index_fulltext',
        'index_grlist',
        'index_phash',
        'index_rel',
        'index_section',
        'index_stat_search',
        'index_stat_word',
        'index_words',
        'tx_crawler_process',
        'tx_crawler_queue',
    ];

    /**
     *  Truncates some tables of idexed_seach and crawler extensions to make proper testing for "Indexed Config"
     *
     */
    public function cleanForTestCommand()
    {
        foreach ($this->tablesToTruncate as $tableToTruncate) {
            self::getDatabaseConnection()->exec_TRUNCATEquery($tableToTruncate);
        }
        self::getDatabaseConnection()->exec_UPDATEquery('index_config', '', ['set_id' => 0, 'timer_next_indexing' => 0]);
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected static function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
