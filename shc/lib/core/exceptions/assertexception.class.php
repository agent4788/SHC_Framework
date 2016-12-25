<?php

namespace SHC\Core\Exception;

/**
 * Ausnahme die einen ungültigen Zustand markiert
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.2.0-0
 */

class AssertException extends \Exception {

    /**
     * AssertException constructor.
     */
    public function __construct($message = "", $code = 0, \Exception $previous = null) {

        parent::__construct($message, $code, $previous);
    }
}