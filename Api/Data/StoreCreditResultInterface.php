<?php

namespace Vbdev\StoreCredit\Api\Data;

/**
 * Interface returned in case of incorrect params passed to efficient store credit API.
 * @api
 */
interface StoreCreditResultInterface
{
    /**#@+
     * Constants
     */
    const MESSAGE = 'message';
    const PARAMETERS = 'parameters';
    /**#@-*/

    /**
     * Get error message, that contains description of error occurred during store credit request.
     *
     * @return string
     */
    public function getMessage();

    /**
     * Set error message, that contains description of error occurred during store credit request.
     *
     * @param string $message
     * @return $this
     */
    public function setMessage($message);

    /**
     * Get parameters, that could be displayed in error message placeholders.
     *
     * @return string[]
     */
    public function getParameters();

    /**
     * Set parameters, that could be displayed in error message placeholders.
     *
     * @param string[] $parameters
     * @return $this
     */
    public function setParameters(array $parameters);
}
