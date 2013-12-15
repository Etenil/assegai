<?php

namespace assegai\modules\paginator\providers;

use assegai\modules\paginator;

/**
 * @package assegai.modules.paginator
 *
 * Array provider for the paginator.
 */
class PaginatorArrayProvider implements IPaginatorProvider
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function count()
    {
        return count($this->data);
    }

    public function getPage($pagenum, $pagelength)
    {
        return array_slice($this->data, ($pagenum - 1) * $pagelength, $pagelength);
    }
}
