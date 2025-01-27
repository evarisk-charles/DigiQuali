<?php
/* Copyright (C) 2022-2023 EVARISK <technique@evarisk.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    admin/control.php
 * \ingroup digiquali
 * \brief   DigiQuali control config page.
 */

// Load DigiQuali environment
if (file_exists('../digiquali.main.inc.php')) {
	require_once __DIR__ . '/../digiquali.main.inc.php';
} elseif (file_exists('../../digiquali.main.inc.php')) {
	require_once __DIR__ . '/../../digiquali.main.inc.php';
} else {
	die('Include of digiquali main fails');
}

// Libraries
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';

require_once __DIR__ . '/../lib/digiquali.lib.php';
require_once __DIR__ . '/../class/control.class.php';

// Global variables definitions
global $conf, $db, $langs, $user;

// Load translation files required by the page
saturne_load_langs(['admin']);

// Initialize view objects
$form = new Form($db);

// Get parameters
$action     = GETPOST('action', 'alpha');
$backtopage = GETPOST('backtopage', 'alpha');
$value      = GETPOST('value', 'alpha');
$attrname   = GETPOST('attrname', 'alpha');

// List of supported format type extrafield label
$tmptype2label = ExtraFields::$type2label;
$type2label    = [''];
foreach ($tmptype2label as $key => $val) {
	$type2label[$key] = $langs->transnoentitiesnoconv($val);
}

// Initialize objects
$object      = new Control($db);
$elementType = $object->element;
$objectType  = $object->element;
$elementtype = $moduleNameLowerCase . '_' . $objectType; // Must be the $table_element of the class that manage extrafield.

$error = 0; //Error counter

// Security check - Protection if external user
$permissiontoread = $user->rights->digiquali->adminpage->read;
saturne_check_access($permissiontoread);

/*
 * Actions
 */

//Extrafields actions
require DOL_DOCUMENT_ROOT . '/core/actions_extrafields.inc.php';

//Set numering modele for control object
if ($action == 'setmod') {
	$constforval = 'DIGIQUALI_' . strtoupper('control') . '_ADDON';
	dolibarr_set_const($db, $constforval, $value, 'chaine', 0, '', $conf->entity);
}

//Set numering modele for controldet object
if ($action == 'setmodControlDet') {
	$constforval = 'DIGIQUALI_' . strtoupper('controldet') . '_ADDON';
	dolibarr_set_const($db, $constforval, $value, 'chaine', 0, '', $conf->entity);
}

if ($action == 'update_control_reminder') {
    $reminderFrequency = GETPOST('control_reminder_frequency');
    $reminderType      = GETPOST('control_reminder_type');

    dolibarr_set_const($db, 'DIGIQUALI_CONTROL_REMINDER_FREQUENCY', $reminderFrequency, 'chaine', 0, '', $conf->entity);
    dolibarr_set_const($db, 'DIGIQUALI_CONTROL_REMINDER_TYPE', $reminderType, 'chaine', 0, '', $conf->entity);

    setEventMessage('SavedConfig');
}

if ($action == 'update_public_survey_title') {
	$publicSurveyTitle = GETPOST('public_survey_title');

	dolibarr_set_const($db, 'DIGIQUALI_PUBLIC_SURVEY_TITLE', $publicSurveyTitle, 'chaine', 0, '', $conf->entity);

	setEventMessage('SavedConfig');
}


/*
 * View
 */

$title   = $langs->trans('ModuleSetup', $moduleName);
$helpUrl = 'FR:Module_DigiQuali';

saturne_header(0,'', $title, $helpUrl);

// Subheader
$linkback = '<a href="' . ($backtopage ?: DOL_URL_ROOT . '/admin/modules.php?restore_lastsearch_values=1') . '">' . $langs->trans('BackToModuleList') . '</a>';
print load_fiche_titre($title, $linkback, 'title_setup');

// Configuration header
$head = digiquali_admin_prepare_head();
print dol_get_fiche_head($head, 'control', $title, -1, 'digiquali_color@digiquali');

/*
 *  Numbering module
 */

require __DIR__ . '/../../saturne/core/tpl/admin/object/object_numbering_module_view.tpl.php';

require __DIR__ . '/../../saturne/core/tpl/admin/object/object_const_view.tpl.php';

//Control data
print load_fiche_titre($langs->trans('ConfigData', $langs->transnoentities('ControlsMin')), '', '');

print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>' . $langs->trans('Name') . '</td>';
print '<td>' . $langs->trans('Description') . '</td>';
print '<td class="center">' . $langs->trans('Status') . '</td>';
print '</tr>';

//Display medias conf
print '<tr><td>';
print $langs->trans('DisplayMedias');
print '</td><td>';
print $langs->trans('DisplayMediasDescription');
print '</td>';

print '<td class="center">';
print ajax_constantonoff('DIGIQUALI_CONTROL_DISPLAY_MEDIAS');
print '</td>';
print '</tr>';

//Use large size media in gallery
print '<tr><td>';
print $langs->trans('UseLargeSizeMedia');
print '</td><td>';
print $langs->trans('UseLargeSizeMediaDescription');
print '</td>';

print '<td class="center">';
print ajax_constantonoff('DIGIQUALI_CONTROL_USE_LARGE_MEDIA_IN_GALLERY');
print '</td>';
print '</tr>';

//Lock control if DMD/DLUO outdated
print '<tr><td>';
print $langs->trans('LockControlOutdatedEquipment');
print '</td><td>';
print $langs->trans('LockControlOutdatedEquipmentDescription');
print '</td>';

print '<td class="center">';
print ajax_constantonoff('DIGIQUALI_LOCK_CONTROL_OUTDATED_EQUIPMENT');
print '</td>';
print '</tr>';
print '</table>';

$object = new ControlLine($db);

require __DIR__ . '/../../saturne/core/tpl/admin/object/object_numbering_module_view.tpl.php';

require __DIR__ . '/../../saturne/core/tpl/admin/object/object_const_view.tpl.php';

//Control data
print load_fiche_titre($langs->trans('ConfigData', $langs->transnoentities('ControlsMin')), '', '');

print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>' . $langs->trans('Name') . '</td>';
print '<td>' . $langs->trans('Description') . '</td>';
print '<td class="center">' . $langs->trans('Status') . '</td>';
print '</tr>';

//Display medias conf
print '<tr><td>';
print $langs->trans('DisplayMedias');
print '</td><td>';
print $langs->trans('DisplayMediasDescription');
print '</td>';

print '<td class="center">';
print ajax_constantonoff('DIGIQUALI_CONTROL_DISPLAY_MEDIAS');
print '</td>';
print '</tr>';

//Use large size media in gallery
print '<tr><td>';
print $langs->trans('UseLargeSizeMedia');
print '</td><td>';
print $langs->trans('UseLargeSizeMediaDescription');
print '</td>';

print '<td class="center">';
print ajax_constantonoff('DIGIQUALI_CONTROL_USE_LARGE_MEDIA_IN_GALLERY');
print '</td>';
print '</tr>';

// Auto-save action on question answer
print '<tr><td>';
print $langs->trans('AutoSaveActionQuestionAnswer');
print '</td><td>';
print $langs->trans('AutoSaveActionQuestionAnswerDescription');
print '</td>';

print '<td class="center">';
print ajax_constantonoff('DIGIQUALI_CONTROLDET_AUTO_SAVE_ACTION');
print '</td>';
print '</tr>';

print '<form method="POST" action="' . $_SERVER['PHP_SELF'] . '">';
print '<input type="hidden" name="token" value="' . newToken() . '">';
print '<input type="hidden" name="action" value="update_public_survey_title">';

print '<tr class="oddeven"><td>';
print $langs->trans('PublicSurveyTitle');
print '</td><td>';
print $langs->trans('PublicSurveyTitleDescription');
print '</td>';

print '<td class="center">';
print '<input type="text" name="public_survey_title" value="' . $conf->global->DIGIQUALI_PUBLIC_SURVEY_TITLE . '">';
print '</td></tr>';

print '<tr><td>';
print $langs->trans('EnablePublicControlHistory');
print '</td><td>';
print $langs->trans('EnablePublicControlHistoryDescription');
print '</td>';

print '<td class="center">';
print ajax_constantonoff('DIGIQUALI_ENABLE_PUBLIC_CONTROL_HISTORY');
print '</td>';
print '</tr>';

print '<tr><td>';
print $langs->trans('ShowQcFrequencyPublicInterface');
print '</td><td>';
print $langs->trans('ShowQcFrequencyPublicInterfaceDescription');
print '</td>';

print '<td class="center">';
print ajax_constantonoff('DIGIQUALI_SHOW_QC_FREQUENCY_PUBLIC_INTERFACE');
print '</td>';
print '</tr>';

print '<tr><td>';
print $langs->trans('ShowLastControlFirstOnPublicHistory');
print '</td><td>';
print $langs->trans('ShowLastControlFirstOnPublicHistoryDescription');
print '</td>';

print '<td class="center">';
print ajax_constantonoff('DIGIQUALI_SHOW_LAST_CONTROL_FIRST_ON_PUBLIC_HISTORY');
print '</td>';
print '</tr>';

print '</table>';

print '<div class="tabsAction"><input type="submit" class="butAction" name="save" value="' . $langs->trans('Save') . '"></div>';

print '</form>';

print load_fiche_titre($langs->trans('ControlReminder'), '', '');

print '<form method="POST" action="' . $_SERVER['PHP_SELF'] . '">';
print '<input type="hidden" name="token" value="' . newToken() . '">';
print '<input type="hidden" name="action" value="update_control_reminder">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>' . $langs->trans('Name') . '</td>';
print '<td>' . $langs->trans('Description') . '</td>';
print '<td class="center">' . $langs->trans('Value') . '</td>';
print '</tr>';

print '<tr class="oddeven"><td>';
print $langs->trans('ControlReminder');
print '</td><td>';
print $langs->trans('ControlReminderDescription');
print '</td>';

print '<td class="center">';
print ajax_constantonoff('DIGIQUALI_CONTROL_REMINDER_ENABLED');
print '</td></tr>';

print '<tr class="oddeven"><td>';
print $langs->trans('ControlReminderFrequency');
print '</td><td>';
print $langs->trans('ControlReminderFrequencyDescription');
print '</td>';

print '<td class="center">';
print '<input type="text" name="control_reminder_frequency" value="' . $conf->global->DIGIQUALI_CONTROL_REMINDER_FREQUENCY . '">';
print '</td></tr>';

print '<tr class="oddeven"><td>';
print $langs->trans('ControlReminderType');
print '</td><td>';
print $langs->trans('ControlReminderTypeDescription');
print '</td>';

print '<td class="center">';
$controlReminderType = ['browser' => 'Browser', 'email' => 'Email', 'sms' => 'SMS'];
print Form::selectarray('control_reminder_type', $controlReminderType, (!empty($conf->global->DIGIQUALI_CONTROL_REMINDER_TYPE) ? $conf->global->DIGIQUALI_CONTROL_REMINDER_TYPE : $controlReminderType[0]), 0, 0, 0, '', 1);
print '</td></tr>';

print '</table>';
print '<div class="tabsAction"><input type="submit" class="butAction" name="save" value="' . $langs->trans('Save') . '"></div>';
print '</form>';
print '</table>';

//Extrafields control management
print load_fiche_titre($langs->trans('ExtrafieldsControlManagement'), '', '');

require DOL_DOCUMENT_ROOT.'/core/tpl/admin_extrafields_view.tpl.php';

// Buttons
if ($action != 'create' && $action != 'edit') {
	print '<div class="tabsAction">';
	print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=create">'.$langs->trans('NewAttribute').'</a></div>';
	print '</div>';
}

// Creation of an optional field
if ($action == 'create') {
	print load_fiche_titre($langs->trans('NewAttribute'));
	require DOL_DOCUMENT_ROOT.'/core/tpl/admin_extrafields_add.tpl.php';
}

// Edition of an optional field
if ($action == 'edit' && !empty($attrname)) {
	print load_fiche_titre($langs->trans('FieldEdition', $attrname));
	require DOL_DOCUMENT_ROOT.'/core/tpl/admin_extrafields_edit.tpl.php';
}

// Page end
print dol_get_fiche_end();
llxFooter();
$db->close();

