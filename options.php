<?php
/**
 * Bitrix module skeleton
 *
 * @author Sergey Korshunov <sergey@korshunov.pro>
 * @license    https://opensource.org/licenses/MIT	MIT License
 */

\Bitrix\Main\Loader::includeModule('vendor.bitrixmodule');

use Vendor\Bitrixmodule\Module;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/options.php');
Loc::loadMessages(__FILE__);

$module_id = Module::getModuleId();

// check access
if ($APPLICATION::GetGroupRight(Module::getModuleId()) < 'S') {
    $APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}

$request = Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();

// options
$arAllOptions = [];

// heading
$arAllOptions['module'][] = Loc::getMessage('VENDOR_PREFIX_OPT_MAIN');

// note
$arAllOptions['module'][] = ['note' => Loc::getMessage('VENDOR_PREFIX_INFO')];

// option
$arAllOptions['module'][] = [
    '', // name
    '<p><b>' . Loc::getMessage('VENDOR_PREFIX_OPT_HTML_NAME') . '</b></p>', // option title
    '<p><b>' . Loc::getMessage('VENDOR_PREFIX_OPT_HTML_DESC') . '</b></p>', //
    ['statichtml']
];

// option
$arAllOptions['module'][] = [
    '',
    Loc::getMessage('VENDOR_PREFIX_OPT_TEXT_NAME'),
    Loc::getMessage('VENDOR_PREFIX_OPT_TEXT_DESC'),
    ['statictext']
];

// option
$arAllOptions['module'][] = [
    'option_3',
    Loc::getMessage('VENDOR_PREFIX_OPT_TEXTAREA_NAME'),
    '',
    ['textarea', 10, 50]
];

// option
$arAllOptions['module'][] = [
    'option_4',
    Loc::getMessage('VENDOR_PREFIX_OPT_INPUT_NAME'),
    '',
    ['text', 10]
];

// option
$arAllOptions['module'][] = Loc::getMessage('VENDOR_PREFIX_OPT_ADDITIONAL');

// option
$arAllOptions['module'][] = [
    'option_5',
    Loc::getMessage('VENDOR_PREFIX_OPT_MULTISELECT_NAME'),
    '', [
        'multiselectbox', [
            'var1' => 'var1',
            'var2' => 'var2',
            'var3' => 'var3',
            'var4' => 'var4'
        ]
    ]
];

// option
$arAllOptions['module'][] = [
    'option_6',
    Loc::getMessage('VENDOR_PREFIX_OPT_SELECT_NAME'),
    '', [
        'selectbox', [
            'var1' => 'var1',
            'var2' => 'var2',
            'var3' => 'var3',
            'var4' => 'var4'
        ]
    ]
];

// option
$arAllOptions['module'][] = [
    'option_7',
    Loc::getMessage('VENDOR_PREFIX_OPT_CHECKBOX_NAME'),
    'val1',
    ['checkbox']
];

// option
$arAllOptions['module'][] = [
    'option_8',
    Loc::getMessage('VENDOR_PREFIX_OPT_CHECKBOX_NAME'),
    'val2',
    ['checkbox']
];

// option
$arAllOptions['module'][] = [
    'option_9',
    Loc::getMessage('VENDOR_PREFIX_OPT_CHECKBOX_NAME'),
    'val3',
    ['checkbox']
];

// Tabs
$aTabs[] = [
    'DIV' => 'edit1',
    'TAB' => Loc::getMessage('VENDOR_PREFIX_OPT_MAIN'),
    'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_SET'),
    'OPTIONS' => $arAllOptions['module']
];

$aTabs[] = [
    'DIV' => 'editRights',
    'TAB' => Loc::getMessage('MAIN_TAB_RIGHTS'),
    'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_RIGHTS')
];

/**
 * Save options
 */
if ($request->isPost() && $request['Update'] && check_bitrix_sessid()) {
    foreach ($aTabs as $aTab) {
        foreach ($aTab['OPTIONS'] as $arOption) {
            if (!is_array($arOption) || $arOption['note']) {
                continue;
            }
            $optionName = $arOption[0];
            $optionValue = $request->getPost($optionName);
            Option::set(Module::getModuleId(), $optionName, is_array($optionValue) ? implode(',', $optionValue) : $optionValue);
        }
    }
}

/**
 * Options HTML form, any html or JS code
 */
$tabControl = new CAdminTabControl('tabControl', $aTabs);
$tabControl->Begin();
?>
    <form name='<?php echo str_replace('.', '_', Module::getModuleId()) ?>_settings' method='POST'
          action='<?php echo $APPLICATION->GetCurPage() ?>?mid=<?php echo htmlspecialcharsbx($request->get('mid')) ?>&lang=<?php echo htmlspecialcharsbx($request->get('lang')) ?>'>
        <?php
        echo bitrix_sessid_post();
        foreach ($aTabs as $aTab) {
            if (!empty($aTab['OPTIONS'])) {
                $tabControl->BeginNextTab();
                __AdmSettingsDrawList(Module::getModuleId(), $aTab['OPTIONS']);
            } elseif ($aTab['DIV'] === 'editRights' && $USER->IsAdmin()) {
                $tabControl->BeginNextTab();
                require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/admin/group_rights.php';
            }
        }
        // buttons block
        $tabControl->Buttons();
        ?>
        <input type='submit' name='Update' value='<? echo getMessage('MAIN_SAVE') ?>'>
        <input type='reset' name='reset' value='<? echo getMessage('MAIN_RESET') ?>'>
    </form>
<?php $tabControl->End();