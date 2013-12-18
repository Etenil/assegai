<?php

namespace assegai\modules\paginator;

/**
 * @package assegai.modules.paginator
 *
 * A paginator for various data providers.
 */
class Paginator extends \assegai\Module
{
    protected $provider;

    protected $pagenum;
    protected $pagelength;

    protected $link;
    protected $getparams;

    public function __construct(IPaginatorProvider $provider)
    {
        $this->provider = $provider;
        $this->pagenum = 1;
        $this->pagelength = 10;
    }

    /**
     * Generic instanciation method for any type of provider.
     */
    static function fromProvider($provider, $data) {
        $providername = 'providers\\' . ucfirst(strtolower($provider)) . 'Provider';
        return new self(new $providername($data));
    }

    /**
     * Instanciates a paginator from the given array.
     */
    static function fromArray(array $data)
    {
        return self::fromProvider('Array', $data);
    }

    /**
     * Instanciates a paginator from the provided model.
     */
    static function fromModel(IPaginatorProvider $data)
    {
        return self::fromProvider('Array', $data);
    }

    /**
     * Instanciates a paginator from PDO statement.
     */
    static function fromPDO(PDOStatement $stmt)
    {
        return new self(new PdoProvider($stmt));
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
        $pagecount  = $this->getPages();

        if($length > $pagecount) {
            $length = $pagecount;
        }

        $delta = ceil($length / 2);

        if($pagenum <= 1) {
            $lowerbound = 1;
            $upperbound = min($pagecount, $length);
        }
        else if($pagenum - $delta > $pagecount - $length) {
            $lowerbound = $pagecount - $length + 1;
            $upperbound = $pagecount;
        }
        else {
            if ($pagenum - $delta < 0) {
                $delta = $pagenum;
            }

            $offset     = $pagenum - $delta;
            $lowerbound = $offset + 1;
            $upperbound = $offset + $length;
        }

        return range($lowerbound, $upperbound);
    }

    /**
     * Sets the link URL used in the paginator.
     */
    public function setLink($url)
    {
        $this->link = $url;
    }

    /**
     * Sets the get parameters for the links.
     */
    public function setGetParams($params)
    {
        // We consider the array is a GET array.
        if(!is_array($params)) {
            $params = explode('&', str_replace('?', '', $params));
        }

        $get = array();
        foreach($params as $varname => $varval) {
            if($varname == 'p') continue; // We don't want to specify the page twice!
            if(is_array($varval)) {
                foreach($varval as $subval) {
                    $get[] = "${varname}[]=$subval";
                }
            } else {
                $get[] = "$varname=$varval";
            }
        }
        $this->getparams = implode('&', $get);
    }

    /**
     * Displays or returns the HTML list of pages.
     * @param $return returns the html when true. Default is false.
     */
    function render($class = '', $id = '', $return = false)
    {
        $link = $this->link . ($this->getparams ? '?'.$this->getparams.'&' : '?');
        ?>
        <div <?=(($id != '')? 'id="'.$id.'"' : '')?> <?=(($class != '')? 'class="'.$class.'"' : '')?>>
	        <a class="start" href="<?=$link?>p=1" title="Start">&nbsp;</a>
	        <a class="back" href="<?=$link?>p=<?=max($this->getPageNum() - 1, 1)?>"
		        title="Back">&nbsp;</a>
	        <?foreach($this->getPagesList() as $page):?>
	            <a class="number<?=($page == $this->getPageNum()? ' selected' : '')?>"
                    href="<?=$link?>p=<?=$page?>" title="Back"><?=$page?></a>
	        <?endforeach?>
	        <a class="next" href="<?=$link?>p=<?=min($this->getPageNum() + 1, $this->getPages())?>"
                title="Back">&nbsp;</a>
	        <a class="end" href="<?=$link?>p=<?=$this->getPages()?>"
                title="End">&nbsp;</a>
        </div>
        <?php
    }
}
