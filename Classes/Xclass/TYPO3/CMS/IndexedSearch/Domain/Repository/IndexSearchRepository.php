<?php

namespace SourceBroker\Crawlerx\Xclass\TYPO3\CMS\IndexedSearch\Domain\Repository;

/**
 * Repository Class IndexSearchRepository
 *
 * @package SourceBroker\Crawlerx\Xclass\TYPO3\CMS\IndexedSearch\Domain\Repository
 */
class IndexSearchRepository extends \TYPO3\CMS\IndexedSearch\Domain\Repository\IndexSearchRepository
{
    /**
     * Returns AND statement for selection of language
     *
     * @return string AND statement for selection of language
     */
    public function languageWhere()
    {
        if ($this->languageUid >= 0) {
            return ' AND IP.sys_language_uid IN(-1,' . (int)$this->languageUid.')';
        }
        return '';
    }
}