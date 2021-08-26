<?php

namespace Vendor\Bitrixmodule\Options\Field;

/**
 * Поле выбора пользователя.
 */
class UserField extends Field
{
    /**
     * {@inheritdoc}
     * @param string $name      Название поля
     * @param mixed  $value     Значение
     * @param string $formName  Название формы
     * @param array  $arOptions Данные поля
     * @return string
     */
    public static function render(
        string $name,
        $value,
        string $formName,
        array $arOptions = []
    ): string {
        $arOptions['MULTIPLE'] = $arOptions['MULTIPLE'] ?? 'N';
        $arOptions['MANDATORY'] = $arOptions['MANDATORY'] ?? 'N';

        // Массив поля выбора сотрудника
        $arData = [
            'USER_TYPE_ID' => 'employee',
            'CLASS_NAME' => '\\CUserTypeEmployee',
            'DESCRIPTION' => '',
            'BASE_TYPE' => 'enum',
            'EDIT_CALLBACK' => [
                0 => '\\CUserTypeEmployeeDisplay',
                1 => 'getPublicEdit',
            ],
            'VIEW_CALLBACK' => [
                0 => '\\CUserTypeEmployeeDisplay',
                1 => 'getPublicView',
            ],
            'ENTITY_ID' => $formName,
            'FIELD_NAME' => $name,
            'SORT' => 100,
            'MULTIPLE' => $arOptions['MULTIPLE'],
            'MANDATORY' => $arOptions['MANDATORY'],
            'EDIT_IN_LIST' => 'Y',
            'EDIT_FORM_LABEL' => $formName,
            'VALUE' => $value,
            'USER_TYPE' => 'employee',
            'SETTINGS' => [],
        ];

        return (new \CUserTypeManager())->GetPublicEdit($arData, []);
    }
}