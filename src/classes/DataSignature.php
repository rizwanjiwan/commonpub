<?php


namespace rizwanjiwan\common\classes;

/**
 * Wraps some functions for data signing using a secret
 * Class DataSignature
 */
class DataSignature
{

    /**
     * DataSigningHelper constructor.
     * @param string $data the data you want to use in these operations
     * @param string $secret the secret used in signing
     */
    public function __construct(private string $data, private string $secret)
    {
    }

    public function getSignature():string
    {
        return hash_hmac('sha256', $this->data, $this->secret, true);
    }

    public function isValid(string $signature):bool
    {
        return hash_equals($this->getSignature(),$signature);
    }

}