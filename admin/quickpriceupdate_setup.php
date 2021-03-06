<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\file		admin/quickpriceupdate.php
 * 	\ingroup	quickpriceupdate
 * 	\brief		This file is an example module setup page
 * 				Put some comments here
 */
// Dolibarr environment

$res = @include("../../main.inc.php"); // From htdocs directory
if (! $res) {
    $res = @include("../../../main.inc.php"); // From "custom" directory
}

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once '../lib/quickpriceupdate.lib.php';

// Translations
$langs->load("quickpriceupdate@quickpriceupdate");

// Access control
if (! $user->admin) {
    accessforbidden();
}

// Parameters
$action = GETPOST('action', 'alpha');

/*
 * Actions
 */
if (preg_match('/set_(.*)/',$action,$reg))
{
	$code=$reg[1];
	if (dolibarr_set_const($db, $code, GETPOST($code), 'chaine', 0, '', $conf->entity) > 0)
	{
		header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
	else
	{
		dol_print_error($db);
	}
}
	
if (preg_match('/del_(.*)/',$action,$reg))
{
	$code=$reg[1];
	if (dolibarr_del_const($db, $code, 0) > 0)
	{
		Header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
	else
	{
		dol_print_error($db);
	}
}

if ($action == 'priceupdate')
{
	_priceUpdateDolibarr($db, $conf, $langs);
}

/*
 * View
 */
$page_name = "quickPriceUpdateSetup";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">'
    . $langs->trans("BackToModuleList") . '</a>';
print_fiche_titre($langs->trans($page_name), $linkback);

// Configuration header
$head = quickpriceupdateAdminPrepareHead();
dol_fiche_head(
    $head,
    'settings',
    $langs->trans("Module104045Name"),
    0,
    "quickpriceupdate@quickpriceupdate"
);

// Setup page goes here
$form=new Form($db);

$var=false;
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameters").'</td>'."\n";
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="center" width="100">'.$langs->trans("Value").'</td>'."\n";


// Example with a yes / no select
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->transnoentitiesnoconv("quickpriceupdate_allowservice").'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="300">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_QUICKPRICEUPDATE_ALLOW_SERVICE">';
print $form->selectyesno("QUICKPRICEUPDATE_ALLOW_SERVICE",$conf->global->QUICKPRICEUPDATE_ALLOW_SERVICE,1);
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td></tr>';

print '</table>';

print '<br /><hr /><br />';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="action" value="priceupdate" />';
print '<table class="border" width="100%">';

print '<tr>';
print '<td class="fieldrequired" width="25%">'.$langs->transnoentitiesnoconv("quickpriceupdate_category").'</td>';
print '<td>'.select_all_categories($form).'</td>';
print '</tr>';

print '<tr>';
print '<td class="fieldrequired" width="25%">'.$langs->transnoentitiesnoconv("quickpriceupdate_date").'</td>';
print '<td>'.$form->select_date('', 'tms',1, 1, 1, "", 1, 0, 1).'</td>';
print '</tr>';

print '<tr>';
print '<td class="fieldrequired" width="25%">'.$langs->transnoentities('quickpriceupdate_percentage', '%').'</td>';
print '<td><input type="text" name="percentage" value="" size="5" />&nbsp;%</td>';
print '</tr>';

print '</table>';

print '<div class="tabsAction"><input type="submit" value="Modifier les prix" class="button"></div>';

print '</form>';

llxFooter();

$db->close();