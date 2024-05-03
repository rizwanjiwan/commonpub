<?php

namespace rizwanjiwan\common\web\dto;

use rizwanjiwan\common\web\SearchKeys;

class SearchResultDto implements DataTransferObject
{
    //rows in our response
    private array $rows=array();
    private ?array $pages;
    private ?int $currentPage;

    /**
     * Params are only for pagination
     * @param ?int $pageNumber the current page number
     * @param ?int[] $pageLinks page numbers around this page number to offer to the user
     */
    public function __construct(?array $pageLinks=null,?int $pageNumber=null)
    {
        $this->currentPage=$pageNumber;
        $this->pages=$pageLinks;
    }

    /**
     * add a row to the search result
     * @param array $row in the format[key=>value,key=>value,...]
     * @return void
     */
    public function addRow(array $row):void
    {
        array_push($this->rows,$row);
    }

    /**
     * Get all stored rows in the format[key=>value,key=>value,...]
     * @return array
     */
    public function getRows():array
    {
        return $this->rows;
    }

    public function jsonSerialize(): array
    {
        return array(
            SearchKeys::PAGE_NUM=>$this->currentPage,
            SearchKeys::PAGES=>$this->pages,
            SearchKeys::ROWS => $this->rows);

    }
}