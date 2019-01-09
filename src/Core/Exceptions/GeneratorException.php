<?php

namespace ModelGenerator\Core\Exceptions;


use ModelGenerator\Core\Generator;

class GeneratorException extends \Exception
{

    public function __construct($message, $logError = true, $logBackTrace = true)
    {
        parent::__construct($message);

        if ($logBackTrace) {
            // TODO: Add Logger..
            //Generator::app()->logger->err($message . "\\n" . $this->getTraceAsString());
        } elseif ($logError) {
            //Generator::app()->logger->err($this->message);
        }
    }
}