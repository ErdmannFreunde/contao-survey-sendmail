<?php

// Paletten anpassen: Alias Feld hinzufügen
foreach ($GLOBALS['TL_DCA']['tl_survey']['palettes'] as $k => $palette) {
  if (!is_array($palette) && strpos($palette, "language")!==false) {
    $GLOBALS['TL_DCA']['tl_survey']['palettes'][$k] = str_replace (
      'description,language;',
      'description,language,notification;',
      $GLOBALS['TL_DCA']['tl_survey']['palettes'][$k]
    );
  }
}

// Feld für Notification
$GLOBALS['TL_DCA']['tl_survey']['fields']['notification'] = array (
  'label'                     => &$GLOBALS['TL_LANG']['tl_survey']['notification'],
  'exclude'                   => true,
  'inputType'                 => 'select',
  'options_callback'          => array('EuF\ContaoSurveySendmail\Backend\BackendHelper', 'getNotificationChoicesForSurvey'),
  'eval'                      => array('includeBlankOption'=>true, 'chosen'=>true, 'tl_class'=>'w50'),
  'sql'                       => "int(10) unsigned NOT NULL default '0'"
);
