<?php
class martinsbannerDisplayModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
    }
    
    public function initContent()
    {
        parent::initContent();
        $this->context->smarty->assign([
            'martins_banner_url' => Configuration::get('MARTINSBANNER_URL'),
            
        ]);
        $this->setTemplate('module:martinsbanner/views/templates/front/display.tpl');
    }


    public function postProcess(){
        
        if (Tools::isSubmit('martinsbanner_product')) {
            $myModuleName = strval(Tools::getValue('martinsbanner_product'));
            $this->context->smarty->assign( 'success', 1 );
            // var_dump($myModuleName); // var_dump($_POST);

            // die('I\'m here!!');
        }
    }
}