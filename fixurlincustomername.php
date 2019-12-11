<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Fixurlincustomername extends Module
{

    public function __construct()
    {
        $this->name = 'fixurlincustomername';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Łukasz Ryszkiewicz';
        $this->author_uri = 'https://ryszkiewicz.cloud';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array(
            'min' => '1.7', 
            'max' => '1.7.5.1'
        );
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Fix URL in customer name');
        $this->description = $this->l('Module for Prestashop 1.7 overriding isName() in Validate.php to prevent  web robots from registering accounts with URLs in the name.');
        $this->confirmUninstall = $this->l('Uninstall module?');
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return parent::install() && Configuration::updateValue('FIXURLINCUSTOMERNAME_OVERRIDE_ENABLED', false);
    }

    public function uninstall()
    {
        Configuration::deleteByName('FIXURLINCUSTOMERNAME_OVERRIDE_ENABLED');
        
        return parent::uninstall();
    }

    public function checkOverride()
    {
        return file_exists(_PS_ROOT_DIR_.'/override/classes/Validate.php');
    }

    public function getContent()
    {
        $output = '';
        
        if (Tools::isSubmit('submit_'.$this->name)) {    
            if ($this->postProcess())
                $output .= $this->displayConfirmation($this->l('Settings saved') );
            else 
                $output .= $this->displayWarning($this->l('Something went wrong! Check form values.'));             
        }
            
        $vars = array (
            $this->name . '_name' => $this->displayName,
            $this->name . '_version' => $this->version,
            $this->name . '_compliancy' => $this->ps_versions_compliancy['min'],
            
            $this->name.'_enabled' => Module::isEnabled('fixurlincustomername'),
            $this->name.'_override_enabled' =>  Configuration::get('FIXURLINCUSTOMERNAME_OVERRIDE_ENABLED'),
                    
            $this->name.'_short_desc'=> $this->description,
            $this->name.'_overrideok'=> $this->checkOverride(),
            $this->name.'_logo' => $this->getPathUri()."/logo.png",
        );
        
        $this->context->smarty->assign($vars);

        $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/header.tpl');
        $output .= $this->displayForm();
        $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/footer.tpl');

        return $output;
    }

    public function displayForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Module settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    # Switch override
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable override?'),
                        'name' => 'FIXURLINCUSTOMERNAME_OVERRIDE_ENABLED',
                        'is_bool' => true,
                        'desc' => $this->l('Set "Yes" to enable override. Override will not work if not enabled here even if file exists in /overrides directory .'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('On')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Off')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                )
            ),
        );

        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper = new HelperForm();
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->table = $this->table;
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit_'.$this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->fields_value['FIXURLINCUSTOMERNAME_OVERRIDE_ENABLED'] = Configuration::get('FIXURLINCUSTOMERNAME_OVERRIDE_ENABLED');
        
        return $helper->generateForm(array($fields_form));
    }

    protected function postProcess()
    {

        if (
            Configuration::updateValue(
                'FIXURLINCUSTOMERNAME_OVERRIDE_ENABLED', (bool)Tools::getValue('FIXURLINCUSTOMERNAME_OVERRIDE_ENABLED')
            )
        ) {

            return True;

        } else {

            return False;
        }
    }
}