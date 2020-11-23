<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
class MartinsBanner extends Module{

    public function __construct()
    {
        $this->name = 'martinsbanner';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Martins Abiodun';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Martins Banner');
        $this->description = $this->l('Test Project For Prestashop Banner');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('MARTINSBANNER_URL')) {
            $this->warning = $this->l('No name provided');
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
    
        return parent::install() &&
            $this->registerHook('displayHome') &&
            $this->registerHook('actionFrontControllerSetMedia') &&
            Configuration::updateValue('MARTINSBANNER_URL', 'my friend');
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !Configuration::deleteByName('MYMODULE_NAME')
        ) {
            return false;
        }

        return true;
    }


    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit'.$this->name)) {
            $myModuleName = strval(Tools::getValue('MARTINSBANNER_URL'));

            if (
                !$myModuleName ||
                empty($myModuleName) ||
                !Validate::isGenericName($myModuleName)
            ) {
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            } else {
                Configuration::updateValue('MARTINSBANNER_URL', $myModuleName);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        return $output.$this->displayForm();
    }



    public function displayForm()
    {
        // Get default language
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('Banner Settings'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Banner Url'),
                    'name' => 'MARTINSBANNER_URL',
                    'size' => 20,
                    'required' => true
                ]
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            ]
        ];

        // Load current value
        $helper->fields_value['MARTINSBANNER_URL'] = Tools::getValue('MARTINSBANNER_URL', Configuration::get('MARTINSBANNER_URL'));

        return $helper->generateForm($fieldsForm);
    }


    public function hookDisplayHome($params)
    {
        $this->context->smarty->assign([
            'martins_banner_url' => Configuration::get('MARTINSBANNER_URL'),
            'martins_banner_link' => $this->context->link->getModuleLink('martinsbanner', 'display')
        ]);

        return $this->display(__FILE__, '/views/templates/hook/martinsbanner.tpl');
    }

}