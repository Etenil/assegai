<?php

require_once('../lib/loader.php');
require('../lib/modules/paginator/paginator.php');

class PaginatorTest extends \centrifuge\Test
{
    protected $pages;

    function init()
    {
        $this->pages = array(
            'foo',
            'bar',
            'baz',
            'thing',
            'stuff',
            );
    }

    public function testInit()
    {
        $p = new Module_Paginator(new PaginatorArrayProvider($this->pages));
        $this->equals(1, $p->getPageNum());
        $this->equals(10, $p->getPageLength());
    }

    public function testPages()
    {
        $p = new Module_Paginator(new PaginatorArrayProvider($this->pages));
        $this->equals(ceil(count($this->pages) / $p->getPageLength()), $p->getPages());
        $p->setPageLength(2);
        $this->equals(ceil(count($this->pages) / $p->getPageLength()), $p->getPages());
    }

    public function testPageChange()
    {
        $p = new Module_Paginator(new PaginatorArrayProvider($this->pages));
        $p->setPageLength(3);
        $p->setPage(2);
        $this->equals(2, $p->getPageNum());
        $p->setPage(1);
        $this->equals(1, $p->getPageNum());
        $p->setPage(50);
        $this->equals($p->getPages(), $p->getPageNum());
    }

    public function testPage()
    {
        $p = new Module_Paginator(new PaginatorArrayProvider(range(1, 50)));
        $p->setPageLength(5);
        $this->equals(range(1, 5), $p->getCurrentPage());
        $p->setPage(2);
        $this->equals(range(6, 10), $p->getCurrentPage());
        $this->equals(range(11, 15), $p->getPage(3));
    }

    public function testPageList()
    {
        $p = new Module_Paginator(new PaginatorArrayProvider(range(1, 50)));
        $p->setPageLength(1);
        $this->equals(range(1,10), $p->getPagesList());
        $p->setPage(25);
        $this->equals(range(21,30), $p->getPagesList());
        $p->setPage(50);
        $this->equals(range(41,50), $p->getPagesList());
    }
}
