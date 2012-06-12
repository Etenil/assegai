<?php

/**
 * @package assegai.modules.paginator
 *
 * Interface for models to be paginated.
 */
interface IPaginatorModel
{
    /**
     * Returns the number of items within the model.
     */
    public function count();

    /**
     * Returns a range of items.
     */
    public function getItems($start, $length);
}