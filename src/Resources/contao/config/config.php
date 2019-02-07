<?php

// Hook registrieren
$GLOBALS['TL_HOOKS']['surveyFinished'][] = array('EuF\ContaoSurveySendmail\Hooks\SurveyFinishedHook', 'surveyFinished');

// Models registrieren
$GLOBALS['TL_MODELS']['tl_survey_question'] = \EuF\ContaoSurveySendmail\Model\SurveyQuestionsModel::class;
$GLOBALS['TL_MODELS']['tl_survey_result'] = \EuF\ContaoSurveySendmail\Model\SurveyResultsModel::class;

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
