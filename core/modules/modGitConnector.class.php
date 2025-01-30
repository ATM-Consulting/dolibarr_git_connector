<?php

include_once DOL_DOCUMENT_ROOT . '/core/modules/DolibarrModules.class.php';

class modGitConnector extends DolibarrModules {
	public function __construct($db)
	{
		global $langs, $conf;
		$this->db = $db;

		$this->numero = 104135;
		$this->rights_class = 'gitConnector';
		$this->family = "interface";
		$this->module_position = '90';
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		$this->description = "Module pour intéragir avec les API GitHub et GitLab";
		$this->descriptionlong = "Module pour intéragir avec les API GitHub et GitLab";

		$this->editor_name = 'ATM Consulting';
		$this->editor_url = 'https://www.atm-consulting.fr';

		$this->version = "develop";
		$this->const_name = 'MAIN_MODULE_' . strtoupper($this->name);
		$this->picto = 'fa-code-branch';

		$this->module_parts = [
			'triggers' 		=> 0,
			'login' 		=> 0,
			'substitutions' => 0,
			'menus' 		=> 0,
			'tpl' 			=> 0,
			'barcode' 		=> 0,
			'models' 		=> 0,
			'printing' 		=> 0,
			'theme' 		=> 0,
			'css' 			=> [],
			'js' 			=> [],
			'hooks' 		=> [],
			'moduleforexternal' => 0,
		];

		$this->dirs = ["/gitConnector/temp"];
		$this->config_page_url = array("setup.php@gitConnector");

		$this->hidden = false;
		$this->depends = [];
		$this->requiredby = [];
		$this->conflictwith = [];

		$this->langfiles = ["gitConnector@gitConnector"];
		$this->phpmin = [8, 1];
		$this->need_dolibarr_version = [19, 0];
		$this->need_javascript_ajax = 0;

		$this->warnings_activation = [];
		$this->warnings_activation_ext = [];
		$this->const = [];

		if (!isModEnabled("gitConnector")) {
			$conf->mymodule = new stdClass();
			$conf->mymodule->enabled = 0;
		}

		$this->tabs = [];
		$this->dictionaries = [];
		$this->boxes = [];

		$this->cronjobs = [];
		$this->rights = [];
		$this->menu = [];
	}

	public function init($options = '') {
		$result = $this->_load_tables('/gitConnector/sql/');
		if ($result < 0) {
			return -1;
		}

		$this->remove($options);
		$sql = [];
		return $this->_init($sql, $options);
	}

	public function remove($options = '')
	{
		$sql = [];
		return $this->_remove($sql, $options);
	}
}
