<?php

/**
 * @package   survey_ce_sendmail
 * @author    Sebastian Buck
 * @license   LGPL
 * @copyright 2016 Erdmann & Freunde
 */

namespace EuF;

use Contao\Controller;
use Contao\Input;
use Contao\System;
use NotificationCenter;



class SurveyFinishedHook extends Controller
{

  /*
   * Auslesen aller Fragen und Antworten, aufbauen der Antwort-Strings und übergabe an NotificationCenter, absenden der Nachrichten
   */
  public function surveyFinished($surveydata)
  {

    // Sprachdatei laden
    System::loadLanguageFile('tl_survey_question');

    // Auslesen der Frage-Aliase
    $objQuestions = Model\SurveyQuestionsModel::findAll();

    while ($objQuestions->next()) {
      $arrQuestions[$objQuestions->id]['id'] = $objQuestions->id;
      $arrQuestions[$objQuestions->id]['alias'] = $objQuestions->alias;
      $arrQuestions[$objQuestions->id]['qtype'] = $objQuestions->questiontype;
      $arrQuestions[$objQuestions->id]['title'] = $objQuestions->title;
      $arrQuestions[$objQuestions->id]['oesubtype'] = $objQuestions->openended_subtype;
      $arrQuestions[$objQuestions->id]['mcsubtype'] = $objQuestions->multiplechoice_subtype;
      $arrQuestions[$objQuestions->id]['matrixsubtype'] = $objQuestions->matrix_subtype;
      $arrQuestions[$objQuestions->id]['matrixcolumns'] = deserialize($objQuestions->matrixcolumns);
      $arrQuestions[$objQuestions->id]['matrixrows'] = deserialize($objQuestions->matrixrows);
      $arrQuestions[$objQuestions->id]['choices'] = deserialize($objQuestions->choices);
      $arrQuestions[$objQuestions->id]['sumchoices'] = deserialize($objQuestions->sumchoices);
      $arrQuestions[$objQuestions->id]['addother'] = $objQuestions->addother;
      $arrQuestions[$objQuestions->id]['othertitle'] = $objQuestions->othertitle;
      $arrQuestions[$objQuestions->id]['addneutral'] = $objQuestions->addneutralcolumn;
      $arrQuestions[$objQuestions->id]['neutralcolumn'] = $objQuestions->neutralcolumn;

      $arrTokens['survey_question_'.$objQuestions->alias] = $arrQuestions[$objQuestions->id]['title'];
      $arrTokens['survey_result_'.$objQuestions->alias] = '';
    }

    // Auslesen der Werte
    $objResults = Model\SurveyResultsModel::findBy('pin', Input::post('pin'));

    while ($objResults->next()) {

      // Fragetyp auslesen
      $strFragetyp = $arrQuestions[$objResults->qid]['qtype'];

      // Switch Fragetyp
      switch ($strFragetyp) {
        // openended
        case 'openended':
          $strValue = '';
          $strValue = $objResults->result;
          break;

        // multiplechoice
        case 'multiplechoice':
          // Array initialisieren
          $arrBuildString = array();

          // Array erzeugen
          $arrMCResults = deserialize($objResults->result);

          switch ($arrQuestions[$objResults->qid]['mcsubtype']) {
            case 'mc_multipleresponse':
              $strValue = '';
              // Werte auslesen und durch String ersetzen
              foreach ($arrMCResults[value] as $result) {
                $result = $result - 1; // Counter verringern, für Array-Zählung
                $arrBuildString[] = $arrQuestions[$objResults->qid]['choices'][$result]; // String der Option setzen
              }

              // Other hinzufügen
              if($arrQuestions[$objResults->qid]['addother']) {
                $intOtherPos = count($arrBuildString) - 1;
                $arrBuildString[$intOtherPos] = $arrQuestions[$objResults->qid]['othertitle'];
                $arrBuildString[$intOtherPos] .= $GLOBALS['TL_LANG']['MSC']['survey_results_devider'];
                $arrBuildString[$intOtherPos] .= $arrMCResults[other]; // String der Other-Option setzen
              }

              $strValue = implode(', ', $arrBuildString);
              break;

            case 'mc_singleresponse':
              $strValue = '';
              // Werte auslesen und durch String ersetzen
              $result = $arrMCResults[value] - 1; // Counter verringern, für Array-Zählung
              $arrBuildString[] = $arrQuestions[$objResults->qid]['choices'][$result]; // String der Option setzen

              if($arrQuestions[$objResults->qid]['addother']) {
                $intOtherPos = count($arrBuildString);
                $arrBuildString[$intOtherPos] = $arrQuestions[$objResults->qid]['othertitle'];
                $arrBuildString[$intOtherPos] .= $GLOBALS['TL_LANG']['MSC']['survey_results_devider'];
                $arrBuildString[$intOtherPos] .= $arrMCResults[other]; // String der Other-Option setzen
              }

              $strValue = implode(', ', $arrBuildString);
              break;

            case 'mc_dichotomous':
              $strValue = '';
              if ($arrMCResults[value] == 1) {
                $strValue = $GLOBALS['TL_LANG']['tl_survey_question']['yes'];
              }
              else {
                $strValue = $GLOBALS['TL_LANG']['tl_survey_question']['no'];
              }
              break;

            default:
              # code...
              break;
          }
          break;

        // matrix
        case 'matrix':
          // Array initialisieren
          $arrBuildString = array();

          // Array erzeugen
          $arrMatrixResults = deserialize($objResults->result);

          switch ($arrQuestions[$objResults->qid]['matrixsubtype']) {
            case 'matrix_singleresponse':
              $strValue = '';

              if($arrQuestions[$objResults->qid]['addneutral']) {
                // Matrix Cols erweitern
                $arrQuestions[$objResults->qid]['matrixcolumns'][] = $arrQuestions[$objResults->qid]['neutralcolumn'];
              }

              // Werte auslesen und durch String ersetzen
              foreach ($arrMatrixResults as $result) {
                $result = $result - 1; // Counter verringern, für Array-Zählung
                $arrBuildString[] = $arrQuestions[$objResults->qid]['matrixcolumns'][$result]; // String der Option setzen
              }

              // Zeilennamen auslesen und hinzufügen
              foreach ($arrQuestions[$objResults->qid]['matrixrows'] as $key => $row) {
                $arrBuildString[$key] = $row.$GLOBALS['TL_LANG']['MSC']['survey_results_devider'].$arrBuildString[$key];
              }

              $strValue = implode(', ', $arrBuildString);

              break;

            case 'matrix_multipleresponse':
              $strValue = '';

              if($arrQuestions[$objResults->qid]['addneutral']) {
                // Matrix Cols erweitern
                $arrQuestions[$objResults->qid]['matrixcolumns'][] = $arrQuestions[$objResults->qid]['neutralcolumn'];
              }

              // Werte auslesen und durch String ersetzen
              foreach ($arrMatrixResults as $key => $row) {
                $key = $key - 1; // Counter verringern, für Array-Zählung
                foreach ($row as $result) {
                  $result = $result - 1; // Counter verringern, für Array-Zählung
                  $arrBuildString[$arrQuestions[$objResults->qid]['matrixrows'][$key]][] = $arrQuestions[$objResults->qid]['matrixcolumns'][$result]; // String der Option setzen
                }
              }

              foreach ($arrBuildString as $row => $results) {
                $strValue .= $row . $GLOBALS['TL_LANG']['MSC']['survey_results_devider'];
                $strValue .= implode(', ', $results);
                $strValue .= ' - ';
              }
              $strValue = substr($strValue, 0, -3); // letztes Komma abschneiden
              break;

            default:
              break;
          }
          break;

        // constantsum
        case 'constantsum':
          // Array initialisieren
          $arrBuildString = array();

          // Array erzeugen
          $arrSumResults = deserialize($objResults->result);

          $strValue = '';

          // Werte auslesen und durch String ersetzen
          foreach ($arrSumResults as $key => $result) {
            $key = $key - 1; // Counter verringern, für Array-Zählung
            $arrBuildString[$arrQuestions[$objResults->qid]['sumchoices'][$key]] = $result; // String der Option setzen
          }

          foreach ($arrBuildString as $key => $value) {
            $strValue .= $key.$GLOBALS['TL_LANG']['MSC']['survey_results_devider'].$value.', ';
          }
          $strValue = substr($strValue, 0, -2); // letztes Komma abschneiden
          break;

        default:
          # code...
          break;
      }

      // Wert in Array schreiben
      $arrTokens['survey_result_'.$arrQuestions[$objResults->qid]['alias']] = $strValue;
    }

    // Survey_Infos hinzufügen
    $arrTokens['survey_name'] = $surveydata['title'];
    $arrTokens['survey_description'] = $surveydata['description'];
    $arrTokens['survey_introduction'] = $surveydata['introduction'];
    $arrTokens['survey_finalsubmission'] = $surveydata['finalsubmission'];


    // Übergabe an NC
    $intNotificationId = $surveydata['notification'];
    $strType = 'survey_finished';
    $strLanguage = 'de';

    // Senden der Benachrichtigung
    $objNotification = NotificationCenter\Model\Notification::findByPk($intNotificationId);
    if (null !== $objNotification) {
        $objNotification->send($arrTokens, $strLanguage); // Language is optional
    }
  }
}
