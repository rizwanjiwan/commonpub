<?php

use rizwanjiwan\common\classes\exceptions\MultipleFieldValidationException;
use rizwanjiwan\common\classes\FieldContainer;
use rizwanjiwan\common\classes\NameableContainer;
use rizwanjiwan\common\interfaces\FieldGenerator;
use rizwanjiwan\common\traits\SearchTrait;
use rizwanjiwan\common\traits\ValidatableTrait;
use rizwanjiwan\common\web\AbstractController;
use rizwanjiwan\common\web\dto\SearchQueryDto;
use rizwanjiwan\common\web\dto\SearchResultDto;
use rizwanjiwan\common\web\fields\AbstractField;
use rizwanjiwan\common\web\fields\SelectField;
use rizwanjiwan\common\web\fields\TextField;
use rizwanjiwan\common\web\Request;
use rizwanjiwan\common\web\validators\Validator;

class ItemController extends AbstractController implements FieldGenerator
{
    use SearchTrait, ValidatableTrait;

    const ITEM_NAME_KEY='name';
    const ITEM_DESCRIPTION_KEY='description';

    public function getFilters(): NameableContainer
    {
        return NameableContainer::create();
    }

    /**
     * render add  page
     * @param Request $request
     * @return void
     */
    public function add(Request $request):void
    {
        $request->respondView('additem',
            [
            ]
        );
    }
    /**
     * Process add requests
     * @param Request $request
     * @return void
     */
    public function addPost(Request $request):void
    {
        try{
            //do pre-validation stuff...
            $fields=$this->validatePost(array(self::ITEM_NAME_KEY,self::ITEM_DESCRIPTION_KEY));
            //save after validation
        }
        catch (MultipleFieldValidationException $e) {//validation failed
         //...
        }catch (Exception $e) {//other error
          //...
        }
    }
    protected function getSearchResults(SearchQueryDto $dto, bool $paginate): SearchResultDto
    {
        //build the search results from the search query and bool tells you if you should paginate.
        //...

        return new SearchResultDto();
    }

    protected function getSearchDisplayFields(): FieldContainer
    {
        //fill in the list of field names that should be shown on the list header
        return new FieldContainer();
    }

    /**
     * @return SelectField[] of filters to you want to get back in $dto for getSearchResults())
     */
    protected function getSearchDisplayFilters(): array
    {
        //... build array of SelectField
        return array();
    }

    /**
     * @return string the name of the view to render the list/search view
     */
    protected function getSearchView(): string
    {
        return 'view';
    }

    /**
     * @param AbstractField $field
     * @return Validator[]
     */
    protected function getValidator(AbstractField $field): array
    {
        //Fill array with validators for the provided field
        return array();
    }

    protected function getFieldGenerator(): FieldGenerator
    {
        return $this;
    }

    public function toField(string $key, ?string $val): AbstractField
    {
        //This works fine unless you want a specific type of field for different types of fields or a better friendly name
        return new TextField($key,$this->calculateFriendlyName($key),$val);
    }
}