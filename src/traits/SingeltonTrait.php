<?php


namespace rizwanjiwan\common\traits;


trait SingeltonTrait
{
    protected static $singelton=null;

    /**
     * Get the instance
     * @return self
     */
    public static function singleton()
    {
        if(self::$singelton===null)
            self::$singelton = new static();
        return self::$singelton;
    }

    protected function __construct()
    {
    }//block other ways other than singleton to create derivatives of this class


    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized (which would create a second instance of it)
     */
    private function __wakeup()
    {
    }
}