<?php

require_once('ipaginatorprovider.php');
require_once('ipaginatormodel.php');
require_once('providers/array.php');
require_once('providers/model.php');

/**
 * @package assegai.modules.paginator
 *
 * A paginator for various data providers.
 */
class Module_Paginator extends \assegai\Module
{
    protected $provider;

    protected $pagenum;
    protected $pagelength;

    public function __construct(IPaginatorProvider $provider)
    {
        $this->provider = $provider;
        $this->pagenum = 1;
        $this->pagelength = 10;
    }

    /**
     * Instanciates a paginator from the given array.
     */
    static function fromArray(array $data)
    {
        return new self(new PaginatorArrayProvider($data));
    }

    /**
     * Instanciates a paginator from the provided model.
     */
    static function fromModel(IPaginatorProvider $data)
    {
        return new self(new PaginatorArrayProvider($data));
    }

    /**
     * Gets the desired page.
     * @param int $pagenum is the page number of the required page.
     * @return array an array of mixed objects depending on what the provider is.
     */
    public function getPage($pagenum)
    {
        $this->setPage($pagenum);
        return $this->getCurrentPage();
    }

    /**
     * Gets the current page.
     */
    public function getCurrentPage()
    {
        return $this->provider->getPage($this->pagenum, $this->pagelength);
    }

    /**
     * Gets the total number of items.
     * @return int the number of items.
     */
    public function count()
    {
        return $this->provider->count();
    }

    /**
     * Sets the current page number.
     * @param int $pagenum is the requested page number.
     */
    public function setPage($pagenum)
    {
        if($pagenum > $this->getPages()) {
            $pagenum = $this->getPages();
        }
        if($pagenum < 1) {
            $pagenum = 1;
        }

        $this->pagenum = $pagenum;
        return $this;
    }

    /**
     * @param int $pagelength is the length of a page.
     */
    public function setPageLength($pagelength)
    {
        $this->pagelength = $pagelength;
        return $this;
    }

    /**
     * Gets the current page's number.
     */
    public function getPageNum()
    {
        return $this->pagenum;
    }

    /**
     * Gets the current page's length.
     */
    public function getPageLength()
    {
        return $this->pagelength;
    }

    /**
     * Returns the total number of pages available.
     */
    public function getPages()
    {
        return (int)ceil((float)$this->provider->count() / (float)$this->pagelength);
    }

    /**
     * Gets the list of pages surrounding the current one as an array.
     * @param int $length is the maximum number of pages surrounding the
     * currently selected one. Default is 10.
     */
    public function getPagesList($length = 10)
    {
         // Pages in range
        $pagenum = $this->getPageNum();
        $pagecount  = $this->count();

        if($length > $pagecount) {
            $length = $pagecount;
        }

        $delta = ceil($length / 2);

        if ($pagenum - $delta > $pagecount - $length) {
            $lowerbound = $pagecount - $length + 1;
            $upperbound = $pagecount;
        } else {
            if ($pagenum - $delta < 0) {
                $delta = $pagenum;
            }

            $offset     = $pagenum - $delta;
            $lowerbound = $offset + 1;
            $upperbound = $offset + $length;
        }

        return range($lowerbound, $upperbound);
    }
}
