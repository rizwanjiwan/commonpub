<?php

namespace rizwanjiwan\common\web\dto;

use rizwanjiwan\common\web\SearchKeys;

class SequencedResultDto implements DataTransferObject
{
    private DataTransferObject $payload;
    private int $sequence;

    /**
     * @param int $sequence The sequence number
     * @param DataTransferObject $payload The payload
     */
    public function __construct(int $sequence,DataTransferObject $payload)
    {
        $this->sequence=$sequence;
        $this->payload=$payload;

    }
    public function jsonSerialize(): array
    {
        return array(
            SearchKeys::SEQUENCE_NUM=>$this->sequence,
            SearchKeys::PAYLOAD=>$this->payload
        );
    }
}