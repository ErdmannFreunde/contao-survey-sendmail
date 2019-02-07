<?php

use EuF\Model\SurveyQuestionsModel;
use EuF\Model\SurveyResultsModel;

/**
 * @package   survey_ce_sendmail
 * @author    Sebastian Buck
 * @license   LGPL
 * @copyright 2016 Erdmann & Freunde
 */

// Hook registrieren
$GLOBALS['TL_HOOKS']['surveyFinished'][] = array('EuF\SurveyFinishedHook', 'surveyFinished');

// Models registrieren
//$GLOBALS['TL_MODELS'][\EuF\Model\SurveyQuestionsModel::getTable()]      = 'SurveyQuestionsModel';
//$GLOBALS['TL_MODELS'][\EuF\Model\SurveyResultsModel::getTable()]      = 'SurveyResultsModel';

// Eigener Benachrichtigungstyp für NotificationCenter
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['survey_ce'] = array
(
   // Type
   'survey_finished'   => array
    (
      'email_subject' => array
      (
        // wird durch BackendHelper erweitert
      ),
      'email_text' => array
      (
        // wird durch BackendHelper erweitert
      ),
      'email_html' => array
      (
        // wird durch BackendHelper erweitert
      )
   )
);
