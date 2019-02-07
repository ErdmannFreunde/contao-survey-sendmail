<?php

namespace EuF\ContaoSurveySendmail\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use EuF\ContaoSurveySendmail\ContaoSurveySendmailBundle;
use Hschottm\SurveyBundle\Survey;

class Plugin implements BundlePluginInterface {

    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(ContaoSurveySendmailBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class])
                ->setLoadAfter([Survey::class])
                ->setLoadAfter(['notification_center'])
            ,
        ];
    }

}