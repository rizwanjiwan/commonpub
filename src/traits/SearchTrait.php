<?php

namespace rizwanjiwan\common\traits;

use Exception;
use rizwanjiwan\common\classes\FieldContainer;
use rizwanjiwan\common\classes\LogManager;
use rizwanjiwan\common\web\dto\ErrorResultDto;
use rizwanjiwan\common\web\dto\SearchQueryDto;
use rizwanjiwan\common\web\dto\SearchResultDto;
use rizwanjiwan\common\web\dto\SequencedResultDto;
use rizwanjiwan\common\web\fields\SelectOption;
use rizwanjiwan\common\web\Request;

/**
 * Controllers should use this trait to gain search functionality
 */
trait SearchTrait
{
    /**
     * @return int Override to change the number of results per page
     */
    protected function getResultsPerPage():int
    {
        return 10;  //can't have constants so doing it this way
    }

    public function list(Request $request):void
    {
        $request->respondView($this->getSearchView(),
            [
                'fields'=>$this->getSearchDisplayFields(),
                'filters'=>$this->getSearchDisplayFilters()
            ]
        );
    }

    /**
     * Parses a SearchQueryDto and responds with a SearchResultDto (wrapped in a sequence)
     * @param Request $request
     * @return void
     */
    public function searchApi(Request $request):void
    {
        try{
            $query=new SearchQueryDto(true);
            $result=$this->getSearchResults($query,true);
            $request->respondJson(new SequencedResultDto($query->sequence,$result));
        }
        catch(Exception $e){
            $log=LogManager::createLogger('SearchControllerTrait');
            $log->error($e->getMessage()."-> ".$e->getTraceAsString());
            $request->respondJson(new ErrorResultDto($e->getMessage()));
        }

    }


    /**
     * The query to run
     * @param SearchQueryDto $dto the query to filter to or null for everything
     * @param bool $paginate true to paginate the results, false otherwise
     * @return SearchResultDto The Result
     */
    protected abstract function getSearchResults(SearchQueryDto $dto,bool $paginate):SearchResultDto;

    /**
     * Provide the fields that are displayed when searching
     * @return FieldContainer
     */
    protected abstract function getSearchDisplayFields():FieldContainer;

    /**
     * Provide the filters that are selectable when searching
     * @return SelectOption[] of select fields to allow filtering against
     */
    protected abstract function getSearchDisplayFilters():array;

    /**
     * @return string the view to render the search page/interface
     */
    protected abstract function getSearchView():string;

}