<?php

namespace EuF\ContaoSurveySendmail\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use EuF\ContaoSurveySendmail\ContaoSurveySendmailBundle;


class Plugin implements BundlePluginInterface {

    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(ContaoSurveySendmailBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }

}