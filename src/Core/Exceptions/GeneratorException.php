<?php

namespace ModelGenerator\Core\Exceptions;


/**
 * Class GeneratorException
 *
 * @package ModelGenerator\Core\Exceptions
 */
class GeneratorException extends \Exception
{

    /**
     * GeneratorException constructor.
     *
     * @param      $message
     * @param bool $logError
     * @param bool $logBackTrace
     */
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