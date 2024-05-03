<?php

namespace rizwanjiwan\common\web\dto;

use rizwanjiwan\common\classes\exceptions\DtoException;
use rizwanjiwan\common\web\SearchKeys;

class SearchQueryDto
{

    public string $searchString;
    public ?string $sortField;
    public bool $sortDirectionAsc=true;
    public ?int $page;
    public array $filters=array();
    public ?int $sequence;


    /**
     * @throws DtoException on input error
     * @param bool $isJson true if the request is json, else, the request came in $_REQUEST
     */
    public function __construct(bool $isJson)
    {
        $obj=null;
        $input="";
        if($isJson){
            $input = trim(file_get_contents('php://input'));
            $obj=json_decode($input,true);
        }
        else{
            $obj=$_REQUEST;
        }

        if($obj===null){
            throw new DtoException('Invalid Json: '.$input);
        }
        if(array_key_exists(SearchKeys::SEARCH,$obj)===false){
            throw new DtoException('Missing search key');
        }
        if(array_key_exists(SearchKeys::SORT,$obj)===false){
            $this->sortField=null;
        }
        else{
            $this->sortField=$obj[SearchKeys::SORT];
        }
        if(array_key_exists(SearchKeys::SORT_DIRECTION,$obj)===false){
            $this->sortDirectionAsc=true;
        }
        else{
            $this->sortDirectionAsc= ($obj[SearchKeys::SORT_DIRECTION]==0);
        }
        if(array_key_exists(SearchKeys::SEARCH,$obj)===false){
            throw new DtoException('Missing search key');
        }
        if(array_key_exists(SearchKeys::PAGE_NUM,$obj)===false){
            $obj[SearchKeys::PAGE_NUM]=0; //start at the begining
        }
        if(array_key_exists(SearchKeys::SEQUENCE_NUM,$obj)===false){
            $obj[SearchKeys::SEQUENCE_NUM]=0; //start at the begining
        }
        //got this far, all is well
        $this->searchString=$obj[SearchKeys::SEARCH];
        $this->page=$obj[SearchKeys::PAGE_NUM];
        $this->sequence=$obj[SearchKeys::SEQUENCE_NUM];
        if(array_key_exists(SearchKeys::FILTERS,$obj)){//we have stuff to filter against
            $filters=$obj[SearchKeys::FILTERS];
            if($isJson===false){
                //need to decode
                $filters=json_decode($filters,true);
            }
            foreach($filters as $key=> $val){
                $this->filters[$key]=$val;
            }
        }
    }
}