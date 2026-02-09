<?php

namespace App\Services\Ai;

use Psr\Http\Message\StreamInterface;

class StreamParser
{
    /**
     * Read a line from a PSR-7 stream.
     */
    public static function readLine(StreamInterface $stream): ?string
    {
        $buffer = '';
        while (!$stream->eof()) {
            $char = $stream->read(1);
            if ($char === "\n") {
                return $buffer;
            }
            $buffer .= $char;
        }
        return $buffer === '' ? null : $buffer;
    }
}
