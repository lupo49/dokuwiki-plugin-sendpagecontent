<?php
/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Matthias Schulte <dokuwiki@lupo49.de>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once(DOKU_PLUGIN.'action.php');

class action_plugin_sendpagecontent extends DokuWiki_Action_Plugin{

    function register(&$controller) {
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'handle_act_preprocess', array());
        $controller->register_hook('TPL_ACT_UNKNOWN', 'BEFORE', $this, 'handle_tpl_act_unknown');
    }

    /**
     * Handles sendpagecontent action
     */
    function handle_act_preprocess(&$event, $param) {
        global $ID;
        global $INFO;
        global $conf;
       
        if ($event->data == 'sendpagecontent') {
            // we can handle it -> prevent others
            $event->preventDefault();
            $event->stopPropagation();
           
            // fetch raw wiki code
            $raw = rawWiki($ID);
            $err = mail_send('HIER E-MAIL-ADRESSE EINTRAGEN', 'Automatic Mail from DokuWiki at ...', $raw, $this->getConf('mailfrom'));
           
            if($err) {
                msg('Mail sent.');
            } else {
                msg('Mail failed');
            }
           
            // We are done. Output normal page content.
            $event->data = 'show';
           
            return true;
        }
        return true;
    }
   
    function handle_tpl_act_unknown(&$event, $param) {
        global $ID;
        if($event->data != 'sendpagecontent') return false;
        $event->preventDefault();
        $event->stopPropagation();       
    }
}
