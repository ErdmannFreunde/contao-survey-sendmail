<?php

// Paletten anpassen: Alias Feld hinzufügen
foreach ($GLOBALS['TL_DCA']['tl_survey_question']['palettes'] as $k => $palette) {
  if (!is_array($palette) && strpos($palette, "title")!==false) {
    $GLOBALS['TL_DCA']['tl_survey_question']['palettes'][$k] = str_replace (
      '{title_legend},title,',
      '{title_legend},title,alias,',
      $GLOBALS['TL_DCA']['tl_survey_question']['palettes'][$k]
    );
  }
}

// Alias Feld für Fragen
$GLOBALS['TL_DCA']['tl_survey_question']['fields']['alias'] = array(
  'label'                   => &$GLOBALS['TL_LANG']['tl_survey_question']['alias'],
  'exclude'                 => true,
  'search'                  => true,
  'inputType'               => 'text',
  'eval'                    => array('rgxp'=>'alias', 'unique'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
  'save_callback' => array
  (
    array('EuF\ContaoSurveySendmail\Backend\BackendHelper', 'generateQuestionAlias')
  ),
  'sql'                     => "varchar(128) COLLATE utf8_bin NOT NULL default ''"
);
