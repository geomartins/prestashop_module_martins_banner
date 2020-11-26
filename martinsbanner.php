<?php
if (!defined('_PS_VERSION_')) {
    exit;
}


use Language;
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
    
        include_once($this->local_path.'sql/install.php');
        return parent::install() &&
            $this->registerHook('displayHome') &&
            // $this->registerHook('actionFrontControllerSetMedia') &&
            Configuration::updateValue('MARTINSBANNER_URL', 'my friend') && $this->installTab();
    }

    public function uninstall()
    {
        include_once($this->local_path.'sql/uninstall.php');
        if (!parent::uninstall() ||
            !Configuration::deleteByName('MYMODULE_NAME') || !$this->uninstallTab()
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
        //return 'Shit is real';

        $this->context->smarty->assign([
            'martins_banner_url' => Configuration::get('MARTINSBANNER_URL'),
            'martins_banner_link' => $this->context->link->getModuleLink('martinsbanner', 'display')
        ]);

        return $this->display(__FILE__, '/views/templates/hook/martinsbanner.tpl');
    }


    // public function hookHeader(){  //[Head Section]
    //     $this->context->controller->addCSS(array(
    //         $this->_path.'views/css/martinsbanner.css'
    //     ));   
    //     $this->context->controller->addJS(array(
    //         $this->_path.'views/js/martinsbanner.js'
    //     ));   
    // }

    public function hookHeader(){  //[Head Section]

        Media::addJsDef(array(
            'mb_ajax' => $this->_path.'/ajax.php',
        ));
        $this->context->controller->addCSS(array(
            $this->_path.'views/css/martinsbanner.css'
        ));   
        $this->context->controller->addJS(array(
            $this->_path.'views/js/martinsbanner.js'
        ));   
    }


    // public function hookActionFrontControllerSetMedia()
    // {



    //     $this->context->controller->registerStylesheet(
    //         'martinsbanner-style',
    //         $this->_path.'views/css/martinsbanner.css',
    //         [
    //             'media' => 'all',
    //             'priority' => 1000,
    //         ]
    //     );

    //     $this->context->controller->registerJavascript(
    //         'martinsbanner-javascript',
    //         $this->_path.'views/js/martinsbanner.js',
    //         [
    //             'position' => 'bottom',
    //             'priority' => 1000,
    //         ]
    //     );
    // }



    private function installTab()
    {
        $tabId = (int) Tab::getIdFromClassName('AdminBanner');
        if (!$tabId) {
            $tabId = null;
        }

        $tab = new Tab($tabId);
        $tab->active = 1;
        $tab->class_name = 'AdminBanner';
        // Only since 1.7.7, you can define a route name
        //$tab->route_name = 'admin_my_symfony_routing';
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Banner Demo');
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('ShopParameters');
        $tab->module = $this->name;

        return $tab->save();
    }

    private function uninstallTab()
    {
        $tabId = (int) Tab::getIdFromClassName('AdminBanner');
        if (!$tabId) {
            return true;
        }

        $tab = new Tab($tabId);

        return $tab->delete();
    }


    public function getFormConfirm($email, $telephone){
        $html = '<ol>';
        $html .= '<li>'.$email.'</li>';
        $html .= '<li>'.$telephone.'</li>';
        $html .='</ol>';


        return $html;

    }

}