<?php

namespace assegai\modules\paginator;

/**
 * @package assegai.modules.paginator
 *
 * Interface for paginator providers.
 */
interface IPaginatorProvider
{
    /**
     * Returns the number of items in the collection.
     */
    public function count();

    /**
     * Returns an array of items for a page.
     * @param int $pagenum is the requested page's number (starts at 0).
     * @param int $pagelength is the number of items per page.
     * @return array a collection of items.
     */
    public function getPage($pagenum, $pagelength);
}
