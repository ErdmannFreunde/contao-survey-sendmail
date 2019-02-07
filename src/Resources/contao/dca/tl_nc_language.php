<?php

// Callback zum Auslesen der Survey-Fragen und erstellen der Tokens
$GLOBALS['TL_DCA']['tl_nc_language']['config']['onload_callback'][] = array('EuF\ContaoSurveySendmail\Backend\BackendHelper', 'addTokensToNC');
