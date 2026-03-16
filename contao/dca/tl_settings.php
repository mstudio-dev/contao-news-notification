<?php

declare(strict_types=1);

use Contao\CoreBundle\DataContainer\PaletteManipulator;

PaletteManipulator::create()
    ->addLegend('mstudio_news_notification_legend', 'chmod_legend', PaletteManipulator::POSITION_BEFORE)
    ->addField('mstudio_news_notification_sender', 'mstudio_news_notification_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('mstudio_news_notification_recipient', 'mstudio_news_notification_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_settings')
;

$GLOBALS['TL_DCA']['tl_settings']['fields']['mstudio_news_notification_sender'] = [
    'inputType' => 'text',
    'eval'      => ['rgxp' => 'email', 'maxlength' => 255, 'tl_class' => 'w50'],
    'sql'       => ['type' => 'string', 'length' => 255, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['mstudio_news_notification_recipient'] = [
    'inputType' => 'text',
    'eval'      => ['rgxp' => 'email', 'maxlength' => 255, 'tl_class' => 'w50', 'mandatory' => true],
    'sql'       => ['type' => 'string', 'length' => 255, 'default' => ''],
];
