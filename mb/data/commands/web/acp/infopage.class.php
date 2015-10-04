<?php

namespace MB\Command\Web;

//Imports
use MB\Core\MB;
use PCC\Core\PCC;
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Util\FileUtil;

/**
 * Zeigt eine Liste mit allen Benutzern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class InfoPage extends PageCommand {

    protected $requiredPremission = 'mb.acp.menu';

    protected $template = 'acpinfo.html';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'acpinfo', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();

        //Headline
        $tpl->assign('apps', MB::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', MB::getStyle());
        $tpl->assign('user', MB::getVisitor());

        //SHC Version
        $tpl->assign('rwfVersion', RWF::VERSION);
        $tpl->assign('mbVersion', MB::VERSION);
        $tpl->assign('size', FileUtil::formatBytesBinary(FileUtil::getDirectorySize(PATH_BASE, true)));

        //Versionen
        $tpl->assign('php', PHP_VERSION);

        //Schreibrechte
        $tpl->assign('cache', str_replace(PATH_BASE, '', PATH_RWF_CACHE));
        $tpl->assign('writeCache', is_writeable(PATH_RWF_CACHE));
        $tpl->assign('rwfLog', str_replace(PATH_BASE, '', PATH_RWF_LOG));
        $tpl->assign('writeRwfLog', is_writeable(PATH_RWF_LOG));
        $tpl->assign('rwfStorage', str_replace(PATH_BASE, '', PATH_RWF_STORAGE));
        $tpl->assign('writeRwfStorage', is_writeable(PATH_RWF_STORAGE));
        $tpl->assign('mbLog', str_replace(PATH_BASE, '', PATH_MB_LOG));
        $tpl->assign('writeMbLog', is_writeable(PATH_MB_LOG));
        $tpl->assign('mbStorage', str_replace(PATH_BASE, '', PATH_MB_STORAGE));
        $tpl->assign('writeMbStorage', is_writeable(PATH_MB_STORAGE));
    }

}