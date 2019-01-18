<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Group\CommandHandler;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Group\Command\SaveDefaultGroupsCommand;
use PrestaShop\PrestaShop\Core\Domain\Group\ValueObject\GroupConfig;

/**
 * Saves Default Groups configuration into `ps_configuration` table
 */
final class SaveDefaultGroupsHandler implements SaveDefaultGroupsHandlerInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SaveDefaultGroupsCommand $command)
    {
        $this->configuration->set(
            GroupConfig::DEFAULT_VISITORS_GROUP_ID,
            $command->getDefaultVisitorsGroupId()->getValue()
        );

        $this->configuration->set(
            GroupConfig::DEFAULT_GUESTS_GROUP_ID,
            $command->getDefaultGuestsGroupId()->getValue()
        );

        $this->configuration->set(
            GroupConfig::DEFAULT_CUSTOMERS_GROUP_ID,
            $command->getDefaultCustomersGroupId()->getValue()
        );
    }
}
