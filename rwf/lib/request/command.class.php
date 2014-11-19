<?php

namespace RWF\Request;

/**
 * Schnittstelle fuer ein Kommando
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Command {

    /**
     * erzeugt die Seite
     * 
     * @param Request  $request  Anfrageobjekt
     * @param Response $response Antwortobjekt
     */
    public function execute(Request $request, Response $response);
}
