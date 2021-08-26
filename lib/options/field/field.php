<?php

namespace Vendor\Bitrixmodule\Options\Field;

/**
 * Поле выбора пользователя.
 */
abstract class Field
{
    /**
     * Метод отрисовки.
     *
     * @param string $name      Название поля
     * @param mixed  $value     Значение
     * @param string $formName  Название формы
     * @param array  $arOptions Данные поля
     * @return string
     */
    abstract public static function render(
        string $name,
        $value,
        string $formName,
        array $arOptions = []
    ): string;
}