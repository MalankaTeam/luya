<?php

namespace cmsadmin\blocks;

use Yii;
use cmsadmin\Module;
use cebe\markdown\GithubMarkdown;

class ImageTextBlock extends \cmsadmin\base\Block
{
    public $module = 'cmsadmin';

    public $_parser = null;

    public $cacheEnabled = true;
    
    private $defaultMargin = '20px';

    private $_source = null;

    public function getParser()
    {
        if ($this->_parser === null) {
            $this->_parser = new GithubMarkdown();
        }

        return $this->_parser;
    }

    public function name()
    {
        return Module::t('block_image_text_name');
    }

    public function icon()
    {
        return 'recent_actors';
    }

    public function config()
    {
        return [
            'vars' => [
                ['var' => 'text', 'label' => Module::t('block_image_text_text_label'), 'type' => 'zaa-textarea'],
                ['var' => 'imageId', 'label' => Module::t('block_image_text_imageid_label'), 'type' => 'zaa-image-upload'],
                ['var' => 'imagePosition', 'label' => Module::t('block_image_text_imageposition_label'), 'type' => 'zaa-select', 'initvalue' => 'left', 'options' => [
                        ['value' => 'left', 'label' => Module::t('block_image_text_imageposition_left')],
                        ['value' => 'right', 'label' => Module::t('block_image_text_imageposition_right')],
                    ],
                ],
            ],
            'cfgs' => [
                ['var' => 'heading', 'label' => Module::t('block_image_text_heading_label'), 'type' => 'zaa-text'],
                ['var' => 'headingType', 'label' => Module::t('block_image_text_headingtype_label'), 'type' => 'zaa-select', 'initvalue' => 'h3', 'options' => [
                        ['value' => 'h1', 'label' => Module::t('block_image_text_heading_label') . ' 1'],
                        ['value' => 'h2', 'label' => Module::t('block_image_text_heading_label') . ' 2'],
                        ['value' => 'h3', 'label' => Module::t('block_image_text_heading_label') . ' 3'],
                        ['value' => 'h4', 'label' => Module::t('block_image_text_heading_label') . ' 4'],
                        ['value' => 'h5', 'label' => Module::t('block_image_text_heading_label') . ' 5'],
                    ],
                ],
                ['var' => 'margin', 'label' => Module::t('block_image_text_margin_label'), 'type' => 'zaa-select', 'initvalue' => $this->defaultMargin, 'options' => [
                            ['value' => '5px', 'label' => '0 ' . Module::t('block_image_text_margin_pixel')],
                            ['value' => '10px', 'label' => '10 ' . Module::t('block_image_text_margin_pixel')],
                            ['value' => '15px', 'label' => '20 ' . Module::t('block_image_text_margin_pixel')],
                            ['value' => '15px', 'label' => '30 ' . Module::t('block_image_text_margin_pixel')],
                            ['value' => '15px', 'label' => '40 ' . Module::t('block_image_text_margin_pixel')],
                            ['value' => '15px', 'label' => '50 ' . Module::t('block_image_text_margin_pixel')],
                    ],
                ],
                ['var' => 'textType', 'label' => Module::t('block_image_text_texttype_label'), 'initvalue' => 1, 'type' => 'zaa-select', 'options' => [
                        ['value' => '0', 'label' => Module::t('block_image_text_texttype_normal')],
                        ['value' => '1', 'label' => Module::t('block_image_text_texttype_markdown')],
                    ],
                ],
                ['var' => 'btnLabel', 'label' => Module::t('block_image_text_btnlabel_label'), 'type' => 'zaa-text'],
                ['var' => 'btnHref', 'label' => Module::t('block_image_text_btnhref_label'), 'type' => 'zaa-text'],
                ['var' => 'targetBlank', 'label' => Module::t('block_image_text_targetblank_label'), 'type' => 'zaa-checkbox'],
            ],
        ];
    }

    public function getFieldHelp()
    {
        return [
            'textType' => Module::t('block_image_text_help_texttype'),
        ];
    }

    public function getText()
    {
        $text = $this->getVarValue('text');

        if ($this->getCfgValue('textType')) {
            $text = $this->getParser()->parse($text);
        }

        return $text;
    }

    public function getImageSource()
    {
        if ($this->_source === null) {
            $img = Yii::$app->storage->getImage($this->getVarValue('imageId'), 0);

            $this->_source = $img ? $img->source : false;
        }

        return $this->_source;
    }

    public function extraVars()
    {
        return [
            'imageSource' => $this->getImageSource(),
            'imagePosition' => $this->getVarValue('imagePosition', 'left'),
            'imageWidth' => $this->getImageSource() ? @getimagesize($this->getImageSource())[0] : 0,
            'margin' => $this->getCfgValue('margin', $this->defaultMargin),
            'text' => $this->getText(),
        ];
    }

    public function twigFrontend()
    {
        return  $this->render();
    }

    public function twigAdmin()
    {
        return  '{% if not extras.imageSource %}'.
                    '<span class="block__empty-text">' . Module::t('block_image_text_no_image') . '</span>'.
                '{% endif %}'.
                '{% if not vars.text %}'.
                    '<span class="block__empty-text">' . Module::t('block_image_text_no_text') . '</span>'.
                '{% endif %}'.
                '{% if extras.imageSource and vars.text %}'.
                    '<img src="{{ extras.imageSource }}" border=0 style="{% if extras.imagePosition == "left" %}float:left;{% else %}float:right{% endif %};{% if extras.imagePosition == "right" %}margin-left:{{ extras.margin }}{% else %}margin-right:{{ extras.margin }}{% endif %};margin-bottom:{{ extras.margin }}; max-width: 50%;"">'.
                    '<p>{% if cfgs.textType == 1 %}{{ extras.text }}{% else %}{{ extras.text|nl2br }}{% endif %}</p>'.
                '{% endif %}';
    }
}
