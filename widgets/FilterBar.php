<?php

namespace panix\mod\queue\widgets;

use yii\base\Widget;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\helpers\Html;

/**
 * Class FilterBar
 *
 */
class FilterBar extends Widget
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        ob_start();
        ob_implicit_flush(false);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        BootstrapPluginAsset::register($this->view);

        $this->view->registerCss(
            <<<CSS
            #queue-filter-bar {
                margin-bottom: 20px;
            }
            #queue-filter-bar.affix {
                position: inherit;
            }
            
            @media (min-width: 1200px) {
                #queue-filter-bar.affix {
                    position: fixed;
                    top: 60px;
                    width: 262px;
                }
            }
CSS
        );
        return Html::tag('div', ob_get_clean(), [
            'id' => 'queue-filter-bar',
        ]);
    }
}
