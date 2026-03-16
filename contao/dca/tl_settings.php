<?php

declare(strict_types=1);

$GLOBALS['TL_DCA']['tl_settings']['palettes']['default']
    .= ';{mstudio_news_notification_legend},mstudio_news_notification_sender,mstudio_news_notification_recipient';

$GLOBALS['TL_DCA']['tl_settings']['fields']['mstudio_news_notification_sender'] = [
    'inputType' => 'text',
    'eval'      => ['rgxp' => 'email', 'maxlength' => 255, 'tl_class' => 'w50'],
    'sql'       => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['mstudio_news_notification_recipient'] = [
    'inputType' => 'text',
    'eval'      => ['rgxp' => 'email', 'maxlength' => 255, 'tl_class' => 'w50'],
    'sql'       => "varchar(255) NOT NULL default ''",
];
