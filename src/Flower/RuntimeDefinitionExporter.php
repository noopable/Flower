<?php
namespace Flower;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\Di\Definition\RuntimeDefinition;
/**
 * Description of RuntimeDefinitionExporter
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RuntimeDefinitionExporter extends RuntimeDefinition {
    public function export()
    {
        return var_export($this->classes);
    }
}
