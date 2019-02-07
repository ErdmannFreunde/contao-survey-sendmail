<?php

/**
 * @package   survey_ce_sendmail
 * @author    Sebastian Buck
 * @license   LGPL
 * @copyright 2016 Erdmann & Freunde
 */

namespace EuF;

use Contao\Backend;
use Contao\DataContainer;
use Contao\StringUtil;
use Contao\Database;

class BackendHelper extends Backend {

  /**
   * Fragealiase und Survey-Namen auslesen und an NC übergeben
   */
  public function addTokensToNC() {

    // Auslesen der Aliase
    $objQuestions = Model\SurveyQuestionsModel::findAll();

    // allgemeine Umfrage-Token
    $arrQuestions[] = 'survey_name';
    $arrQuestions[] = 'survey_description';
    $arrQuestions[] = 'survey_introduction';
    $arrQuestions[] = 'survey_finalsubmission';

    // Frage- und Antworten-Tokens hinzufügen
		if($objQuestions !== null) {
			while ($objQuestions->next()) {
				$arrQuestions[] = 'survey_question_'.$objQuestions->alias;
				$arrQuestions[] = 'survey_result_'.$objQuestions->alias;
			}
		}

    // Übergabe
    $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['survey_ce']['survey_finished']['email_subject'] = array('survey_name');
    $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['survey_ce']['survey_finished']['email_text'] = $arrQuestions;
    $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['survey_ce']['survey_finished']['email_html'] = $arrQuestions;
  }


  /**
   * Auto-generate the alias if it has not been set yet
   *
   * @param mixed         $varValue
   * @param DataContainer $dc
   *
   * @return string
   *
   * @throws Exception
   */
  public function generateQuestionAlias($varValue, DataContainer $dc)
  {
    $autoAlias = false;

    // Generate alias if there is none
    if ($varValue == '')
    {
      $autoAlias = true;
      $varValue = StringUtil::generateAlias($dc->activeRecord->title);
    }    

    $objAlias = $this->Database->prepare("SELECT id FROM tl_survey_question WHERE alias=?")
                   ->execute($varValue);

    // Check whether the news alias exists
    if ($objAlias->numRows > 1 && !$autoAlias)
    {
      throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
    }

    // Add ID to alias
    if ($objAlias->numRows && $autoAlias)
    {
      $varValue .= '_' . $dc->id;
    }

    return $varValue;
  }


  public function getNotificationChoicesForSurvey() {
    $arrChoices = array();
    $objNotifications = Database::getInstance()->execute("SELECT id,title FROM tl_nc_notification WHERE type='survey_finished' ORDER BY title");

    while ($objNotifications->next()) {
        $arrChoices[$objNotifications->id] = $objNotifications->title;
    }

    return $arrChoices;
  }

}
