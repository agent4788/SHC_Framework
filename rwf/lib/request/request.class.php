<?php

namespace RWF\Request;

//Imports
use RWF\Util\DataTypeUtil;

/**
 * Schnittstelle fuer Anfragen an die Anwendung
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Request {

    /**
     * HTTP POST
     *
     * @var string
     */
    const POST = 'post';

    /**
     * HTTP GET
     *
     * @var string
     */
    const GET = 'get';

    /**
     * HTTP SERVER
     *
     * @var string
     */
    const SERVER = 'server';

    /**
     * HTTP FILE
     *
     * @var string
     */
    const FILE = 'file';

    /**
     * ENV
     *
     * @var string
     */
    const ENV = 'env';

    /**
     * ALLE HTTP Variablen
     *
     * @var string
     */
    const REQUEST = 'request';

    /**
     * prueft ob ein Parameter vorhanden ist
     * 
     * @param  String  $name   Name des Parameters
     * @param  String  $method Datenquelle
     * @return Boolean
     */
    public function issetParam($name, $method = self::GET);

    /**
     * gibt den Wert eines Parameters zurueck
     * 
     * @param  String  $name     Name des Parameters
     * @param  String  $method   Datenquelle
     * @param  Integer $dataType Erwarteter Datentyp
     * @return Mixed
     */
    public function getParam($name, $method = self::GET, $dataType = DataTypeUtil::PLAIN);

    /**
     * gibt eine Liste mit allen Parametern zurueck
     * 
     * @param  String $method Datenquelle
     * @return Array
     */
    public function listParamNames($method = 'all');
}