<?php
/**
 *Copyright (c) 2019 Revers.io
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author revers.io
 * @copyright Copyright (c) permanent, Revers.io
 * @license   Revers.io
 * @see       /LICENSE
 */

namespace ReversIO\Install;

use Language;
use ReversIO\Config\Config;
use ReversIO\Services\Orders\OrderListBuilder;
use ReversIO\Services\Versions\Versions;
use ReversIO;
use Tab;

class Installer
{
    /**
     * @var ReversIO
     */
    private $module;

    /** @var DatabaseInstall */
    private $databaseInstall;

    private $moduleConfiguration;

    public function __construct(
        ReversIO $module,
        DatabaseInstall $databaseInstall,
        array $moduleConfiguration
    ) {
        $this->module = $module;
        $this->databaseInstall = $databaseInstall;
        $this->moduleConfiguration = $moduleConfiguration;
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        if (!$this->registerHooks()) {
            return false;
        }

        if (!$this->registerConfiguration()) {
            return false;
        }

        if (!$this->databaseInstall->createDatabaseTables()) {
            return false;
        }

        if (!$this->databaseInstall->insertDefaultOrdersStatus()) {
            return false;
        }

        if (!$this->installModuleTabs()) {
            return false;
        }

        return true;
    }

    /**
     * Registers Module Hooks.
     *
     * @return bool
     */
    private function registerHooks()
    {
        $hooks = $this->moduleConfiguration['hooks'];

        if (empty($hooks)) {
            return true;
        }

        foreach ($hooks as $hook) {
            if (!$this->module->registerHook($hook)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Registers Module Configuration.
     *
     * @return bool
     */
    private function registerConfiguration()
    {
        $configuration = $this->moduleConfiguration['configuration'];

        if (empty($configuration)) {
            return true;
        }

        foreach ($configuration as $configName => $value) {
            if (!\Configuration::updateValue($configName, $value)) {
                return false;
            }
        }

        $now = new \DateTime();

        \Configuration::updateValue(
            ReversIO\Config\Config::ORDER_DATE_FROM,
            date('Y-m-d', strtotime($now->format('Y-m-d'). ' - 15 days'))
        );
        \Configuration::updateValue(ReversIO\Config\Config::ORDER_DATE_TO, $now->format('Y-m-d'));

        \Configuration::updateValue(Config::ORDERS_STATUS, json_encode(array(4)));

        return true;
    }

    private function installModuleTabs()
    {
        $tabs = $this->module->getTabs();

        foreach ($tabs as $tab) {
            if (Tab::getIdFromClassName($tab['class_name'])) {
                continue;
            }

            if (!$this->installTabs($tab['class_name'], $tab['parent'], $tab['name'])) {
                return false;
            }
        }
        return true;
    }

    private function installTabs($className, $parent, $name)
    {
        $idParent = is_int($parent) ?$parent : Tab::getIdFromClassName($parent);

        $moduleTab = new Tab();
        $moduleTab->class_name = $className;
        $moduleTab->id_parent = $idParent;
        $moduleTab->module = $this->module->name;

        $languages = Language::getLanguages(true);
        foreach ($languages as $language) {
            $moduleTab->name[$language['id_lang']] = $name;
        }

        if (!$moduleTab->save()) {
            return false;
        }

        return true;
    }
}
