<?php namespace IDesigning\PostProxy\FormWidgets;

use Backend\Classes\FormWidgetBase;

/**
 * ServicesWidget Form Widget
 */
class ServicesWidget extends FormWidgetBase
{

    /**
     * {@inheritDoc}
     */
    protected $defaultAlias = 'idesigning_postproxy_services_widget';

    /**
     * {@inheritDoc}
     */
    public function init()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $this->prepareVars();

        return $this->makePartial('serviceswidget');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $this->vars[ 'name' ] = $this->formField->getName();
        $this->vars[ 'value' ] = $this->getLoadValue();
        $this->vars[ 'model' ] = $this->model;
        $formMethod = 'loadCustomForm';
        if (isset($this->formField->config[ 'formAttribute' ])) {
            $formMethod = $this->formField->config[ 'formAttribute' ];
        }
        $form = $this->model->$formMethod();

        $arrayName = $this->formField->getName();

        $config = $this->makeConfig($form);
        $config->model = $this->model;
        $config->data = $this->model->getAttribute($this->formField->config[ 'dataAttribute' ]);
        $config->arrayName = $arrayName;

        $this->vars[ 'widget' ] = $this->makeWidget('Backend\Widgets\Form', $config);
        $this->vars[ 'widget' ]->bindToController();

    }


    /**
     * {@inheritDoc}
     */
    public function loadAssets()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getSaveValue($value)
    {
        return $value;
    }

}
