<?php

/**
 * @package   survey_ce_sendmail
 * @author    Sebastian Buck
 * @license   LGPL
 * @copyright 2016 Erdmann & Freunde
 */

// Callback zum Auslesen der Survey-Fragen und erstellen der Tokens
$GLOBALS['TL_DCA']['tl_nc_language']['config']['onload_callback'][] = array('EuF\BackendHelper', 'addTokensToNC');
