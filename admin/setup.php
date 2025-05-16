<?php

$res = @include("../../main.inc.php");
if (!$res) {
	$res = @include("../../../main.inc.php");
}

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

global $user, $langs, $db, $conf;

$langs->load("admin");
$langs->load("gitConnector@gitConnector");

$form = new Form($db);

$action = GETPOST('action', 'alpha');
$label = GETPOST('label', 'alpha');

if (str_starts_with($action, 'set_')) {
	$key = substr($action, 4);
	$action = "setValue";
	$value = GETPOST($key);
	if (is_array($value)) {
		$value = implode(",", $value);
	}
} else {
	$value = GETPOST('value', 'alpha');
}

$error = 0;

if ($action === 'setValue') {
	if (stristr($key, 'token') !== false) {
		$value = dolEncrypt($value);
	}
	$res = dolibarr_set_const($db, $key, $value);

	if (!$res > 0) {
		$error++;
	}

	$message = $error ? '<p class="error">'.$langs->trans('Error').'</p>' : '<p class="ok">'.$langs->trans('SetupSaved').'</p>';
}

llxHeader('',$langs->trans('GIT_SETUP_PAGE'), '', '', 0, 0);
$backLink = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">'.$langs->trans('BackToModuleList').'</a>';
print load_fiche_titre($langs->trans('GIT_SETUP_PAGE'), $backLink, 'fa-code-branch');

print dol_get_fiche_head([], 'config', $langs->trans('GIT_SETUP_PAGE'), -2);

$parametersList = [
	"GitHub" => [
		"GIT_GITHUB_TOKEN",
		"GIT_GITHUB_BASE_API_URL",
		"GIT_GITHUB_DEFAULT_OWNER"
	],
	"GitLab" => [
		"GIT_GITLAB_TOKEN",
		"GIT_GITLAB_BASE_API_URL",
		"GIT_GITLAB_DEFAULT_OWNER"
	]
];
foreach ($parametersList as $section => $parameters) {
	?>
	<table class="centpercent notopnoleftnoright table-fiche-title">
		<tr class="titre">
			<td class="nobordernopadding valignmiddle col-title"><?= $section ?></td>
		</tr>
	</table>
	<table class="noborder" width="100%">
		<tr class="liste_titre">
			<td><?= $langs->trans("Parameter") ?></td>
			<td colspan="2"><?= $langs->trans("Value") ?></td>
		</tr>
	<?php
		foreach ($parameters as $parameter) {
			$parameterValue = getDolGlobalString($parameter);
			$isToken = (stristr($parameter, 'token') !== false);
			?>
			<tr class="oddeven">
			<td><label for="<?= $parameter ?>"><?= $langs->trans($parameter) ?></label></td>
			<td class="right">
				<form method="post" action="">
				<input type="hidden" name="action" value="set_<?= $parameter ?>">
				<input type="<?= $isToken ? 'password' : 'text' ?>" id="<?= $parameter ?>" class="flat" name="<?= $parameter ?>" value="<?= $parameterValue ?>">
				<input type="submit" class="button" value="<?= $langs->trans('Modify') ?>">
				</form>
			</td>
		</tr>
			<?php
		}
	?>
	</table>
	<?php
}
