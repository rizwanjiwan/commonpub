<?php
/**
 * CSRF protection filter on AJAX requests
 */
namespace rizwanjiwan\common\web\filters;


use rizwanjiwan\common\classes\Csrf;
use rizwanjiwan\common\traits\NameableTrait;
use rizwanjiwan\common\web\Filter;
use rizwanjiwan\common\web\Request;
use stdClass;

class CsrfAjaxFilter implements Filter
{
    use NameableTrait;

    /**
     * @var stdClass
     */
    private mixed $obj;

    /**
     * CsrfAjaxFilter constructor.
     * @param $obj stdClass|null the json decoded data from the ajax call or null if you haven't decoded it.
     */
    public function __construct(?stdClass $obj=null)
    {
        if($obj===null)
        {
            $input = trim(file_get_contents('php://input'));
            $obj = json_decode($input);
        }
        $this->obj=$obj;

    }

    public function filter(Request $request)
    {
        if($this->pass()===false)
            exit(0);
    }

    /**
     * Is the CSRF token present and valid?
     * @return bool true if it's present and valid
     */
    public function pass():bool
    {
        $key=Csrf::CSRF_TOKEN_KEY;
        if(property_exists($this->obj,$key))
        {
            $csrf=new Csrf();
            if($csrf->isValid($this->obj->$key))
            {
                return true;
            }
        }
        return false;
    }
}