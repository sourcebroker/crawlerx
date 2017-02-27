# TYPO3 Extension ``crawlerx``

This extension extends crawler and indexed_search extension to make it easier to implement 
complex indexed_search/crawler scenarios.

### Installation

Install the extension using composer ``composer require sourcebroker/crawlerx``.

### Usage

1. After instalation you can use cli command clean all indexed search tables and crawler queue:

   `` php ./typo3/cli_dispatch.phpsh extbase crawlerx:cleanfortest``
        

2. There is ability to use all record fields in field "GET parameter string (with ###UID### substitution)" of "Indexing Configuration" record.

3. The indexed_search results show now also records with sys_language_uid=-1