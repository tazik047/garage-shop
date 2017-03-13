<?php
/*
@author Dmitriy Kubarev
@link   http://www.simpleopencart.com
@link   http://www.opencart.com/index.php?route=extension/extension/info&extension_id=4811
*/ 

class SimpleController extends Controller {
    static $_childContent = '';
    private $_opencartVersion = 0;

    public function __construct($registry) {
        $opencartVersion = explode('.', VERSION);
        $this->_opencartVersion = floatval($opencartVersion[0].$opencartVersion[1].$opencartVersion[2].'.'.(isset($opencartVersion[3]) ? $opencartVersion[3] : 0));
        
        parent::__construct($registry);
    }

    public function renderPage($template, $templateData, $childrens = array()) {

        if ($this->_opencartVersion < 200) {
            $this->data = $templateData;

            if (!empty($childrens)) {
                $this->children = $childrens;
            }

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/'.$template)) {
                $this->template = $this->config->get('config_template') . '/template/'.$template;
            } else {
                $this->template = 'default/template/'.$template;
            }

            return $this->render();
        } else {
            foreach ($childrens as $child) {
                $templateData[substr($child, strpos($child, '/') + 1)] = $this->load->controller($child);
            }

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/'.$template)) {
                return $this->load->view($this->config->get('config_template') . '/template/'.$template, $templateData);
            } elseif (file_exists(DIR_TEMPLATE . $template)){
                return $this->load->view($template, $templateData);
            } else {
                return $this->load->view($template, $templateData);
            }
        }
    }

    public function setOutputContent($content) {
        $this->response->setOutput($content);

        self::$_childContent = $content;
        
        return $content;
    }

    public function getChildController($action, $params = array()) {
        self::$_childContent = '';
        $returnContent = '';

        if ($this->_opencartVersion < 200) {
            $returnContent = $this->getChild($action, $params);
        } else {
            $returnContent = $this->load->controller($action, $params);
        }

        if ($returnContent && !self::$_childContent) {
            self::$_childContent = $returnContent;
        }

        $tmp = self::$_childContent;
        self::$_childContent = '';

        return $tmp;
    }
}