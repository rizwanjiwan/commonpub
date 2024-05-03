<?php

namespace rizwanjiwan\common\interfaces;

use rizwanjiwan\common\web\fields\AbstractField;

/**
 * Classes that can generate fields from user input
 */
interface FieldGenerator
{
    /**
     * Create a field from user input
     * @param string $key key of the field
     * @param string|null $val the value for the field
     * @return AbstractField
     */
    public function toField(string $key, ?string $val): AbstractField;
}