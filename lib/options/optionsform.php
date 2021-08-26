<?php

namespace Vendor\Bitrixmodule\Options;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Request;
use Vendor\Bitrixmodule\Options\Field\Field;

/**
 * Класс для работы с настройками, выводит форму настроек.
 *
 * Class OptionsForm.
 */
class OptionsForm
{
    /**
     * Форма нстроек в админке.
     *
     * @param string $moduleId             ID модуля
     * @param OptionsConfig $optionsConfig Конфиг настроек
     * @param Request $request             Объект запроса
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    public static function execute(string $moduleId, OptionsConfig $optionsConfig, Request $request)
    {
        global $APPLICATION;
        global $USER;

        Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/options.php');

        // check access
        if ($APPLICATION::GetGroupRight($moduleId) < 'S') {
            $APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
        }

        $options = new Options($moduleId, $optionsConfig, $request);

        /** Save options. */
        if ($request->isPost() && $request['Update'] && check_bitrix_sessid()) {
            $options->save();
        }

        $aTabs = $options->getOptions();

        // Tabs
        $aTabs[] = [
            'DIV' => 'editRights',
            'TAB' => Loc::getMessage('MAIN_TAB_RIGHTS'),
            'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_RIGHTS'),
        ];

        /**
         * Options HTML form, any html or JS code.
         */
        $tabControl = new \CAdminTabControl('tabControl', $aTabs);
        $tabControl->Begin();
        $params = [
            'mid' => htmlspecialcharsbx($request->get('mid')),
            'lang' => htmlspecialcharsbx($request->get('lang')),
        ];
        ?>
        <form name="<?php echo str_replace('.', '_', $moduleId); ?>_settings"
              method="POST"
              action="<?=$APPLICATION->GetCurPage(); ?>?<?=http_build_query($params); ?>">
            <?php
            echo bitrix_sessid_post();
            foreach ($aTabs as $aTab) {
                if ('editRights' === $aTab['DIV'] && $USER->IsAdmin()) {
                    $tabControl->BeginNextTab();
                    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/admin/group_rights.php';
                } elseif (!empty($aTab['OPTIONS'])) {
                    $tabControl->BeginNextTab();
                    foreach ($aTab['OPTIONS'] as $key => $option) {
                        if (!empty($option[3]['custom'])
                            && $option[3]['custom'] === 'Y'
                            && !empty($option[3]['class']) && class_exists($option[3]['class'])
                        ) {
                            unset($aTab['OPTIONS'][$key]);
                            /** @var Field $class */
                            $class = $option[3]['class'];
                            $class::render(
                                 'user',
                                [1007],
                                str_replace('.', '_', $moduleId),
                                []
                            );
                        }
                    }
                    __AdmSettingsDrawList($moduleId, $aTab['OPTIONS']);
                }
            }
            // buttons block
            $tabControl->Buttons();
            ?>
            <input type='submit' name='Update' value='<?=Loc::getMessage('MAIN_SAVE'); ?>'>
            <input type='reset' name='reset' value='<?=Loc::getMessage('MAIN_RESET'); ?>'>
        </form>
        <?php $tabControl->End();
    }
}
