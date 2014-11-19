<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Util\FileUtil;
use SHC\Core\SHC;

/**
 * Zeigt eine Liste mit allen Benutzern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class InfoAjax extends AjaxCommand {

    protected $premission = 'shc.acp.menu';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('acpinfo', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();

        //SHC Version
        $tpl->assign('rwfVersion', RWF::VERSION);
        $tpl->assign('shcVersion', SHC::VERSION);
        $tpl->assign('size', FileUtil::formatBytesBinary(FileUtil::getDirectorySize(PATH_BASE, true)));

        //Versionen
        $tpl->assign('php', PHP_VERSION);
        $data = array();
        $match = array();
        @exec('gpio -v', $data);
        if(isset($data[0]) && preg_match('#gpio\s+version:\s+(.*)#i', $data[0], $match)) {

            $tpl->assign('wiringPi', trim($match[1]));
        } else {

            $tpl->assign('wiringPi', RWF::getLanguage()->get('acp.acpinfo.box.version.wiringPi.unknown'));
        }
        //Mobil Detect einbinden
        require_once(PATH_RWF_CLASSES . 'external/mobile_detect/Mobile_Detect.php');
        $tpl->assign('mobileDetect', \Mobile_Detect::VERSION);

        //Schreibrechte
        $tpl->assign('cache', str_replace(PATH_BASE, '', PATH_RWF_CACHE));
        $tpl->assign('writeCache', is_writeable(PATH_RWF_CACHE));
        $tpl->assign('rwfLog', str_replace(PATH_BASE, '', PATH_RWF_LOG));
        $tpl->assign('writeRwfLog', is_writeable(PATH_RWF_LOG));
        $tpl->assign('rwfStorage', str_replace(PATH_BASE, '', PATH_RWF_STORAGE));
        $tpl->assign('writeRwfStorage', is_writeable(PATH_RWF_STORAGE));
        $tpl->assign('shcLog', str_replace(PATH_BASE, '', PATH_SHC_LOG));
        $tpl->assign('writeShcLog', is_writeable(PATH_SHC_LOG));
        $tpl->assign('shcStorage', str_replace(PATH_BASE, '', PATH_SHC_STORAGE));
        $tpl->assign('writeShcStorage', is_writeable(PATH_RWF_STORAGE));

        $this->data = $tpl->fetchString('acpinfo.html');
    }

}