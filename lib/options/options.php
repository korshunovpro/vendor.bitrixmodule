<?php

namespace Vendor\Bitrixmodule\Options;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Request;
use Bitrix\Main\Result;

/**
 * Класс для работы с настройками.
 *
 * Class Options.
 */
class Options
{
    /** @var string ID модуля */
    protected $moduleId;
    /** @var ?Request Объект запроса */
    protected $request;
    /** @var OptionsConfig Класс объявления настроек */
    protected $optionsConfig;
    /** @var string Префикс для настроек */
    protected $prefix;

    /**
     * Options constructor.
     *
     * @param string        $moduleId       ID модуля
     * @param OptionsConfig $optionsConfig  Массив объявления настроек
     * @param ?Request      $request        Объект запроса
     */
    public function __construct(string $moduleId, OptionsConfig $optionsConfig, ?Request $request = null)
    {
        $this->optionsConfig = $optionsConfig;
        $this->moduleId = $moduleId;
        $this->request = $request;
        $this->prefix = $this->createPrefix();
    }

    /**
     * Массив опций для вывода в табах.
     */
    public function getOptions(bool $prepared = true): array
    {
        return $prepared
            ? $this->prepareOptions($this->optionsConfig::getOptions())
            : $this->optionsConfig::getOptions();
    }

    /**
     * Сохранение настроек из табов по переданному Request.
     *
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    public function save(): Result
    {
        $result = new Result();
        if (!$this->request) {
            $result->addError(new Error('Request object not defined via constructor'));

            return $result;
        }

        foreach ($this->getOptions(false) as $tab) {
            foreach ($tab['OPTIONS'] as $optionName => $option) {
                if (!$option || !is_array($option) || !empty($option['note'])) {
                    continue;
                }

                Option::set(
                    $this->moduleId,
                    $this->prefix . $optionName,
                    $this->prepareValue($this->request->getPost($this->prefix . $optionName))
                );
            }
        }

        return $result;
    }

    /**
     * Обработка занчения перед сохранением в b_option.
     *
     * @param mixed $value Значение
     *
     * @return string
     */
    protected function prepareValue($value): string
    {
        return is_array($value) ? implode(',', $value) : $value;
    }

    /**
     * Префикс для настроек.
     *
     * @return string
     */
    protected function createPrefix(): string
    {
        return str_replace(
            '.',
            '_',
            $this->moduleId
        ) . '_';
    }

    /**
     * Добавляет ключ подмассива первым элементов в этот подмассив, добавляет префикс.
     * Элемент array[0] используется как имя опции.
     *
     * @param array $tabs Массив табов опций
     *
     * @return array
     */
    protected function prepareOptions(array $tabs): array
    {
        $result = [];
        foreach ($tabs as $tabKey => $tab) {
            $tab['DIV'] = $this->prefix . $tabKey;
            $result[$tabKey] = $tab;

            $options = [];
            foreach ($tab['OPTIONS'] as $optKey => $option) {
                array_unshift($option, $this->prefix . $optKey);
                if (is_array($option) && isset($option['note'])) {
                    $options[$optKey] = $option;
                } elseif (is_array($option)) {
                    $options[$optKey] = array_values($option);
                } else {
                    $options[$optKey] = $option;
                }
            }

            $result[$tabKey]['OPTIONS'] = array_values($options);
        }

        return array_values($result);
    }
}
