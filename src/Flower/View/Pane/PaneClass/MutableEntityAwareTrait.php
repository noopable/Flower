<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\PaneClass;

use ArrayAccess;
use Flower\View\Pane\Entity\ApplicatePaneInterface;

/**
 * Description of MutableEntityAwareTrait
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
trait MutableEntityAwareTrait
{
    protected $entity;

    public function setEntity($entity)
    {
        $mutableParams = $this->getOption('mutable_params');
        if (!empty($mutableParams)) {
            foreach ($mutableParams as $key) {
                if (is_object($entity)) {
                    $method = 'get' . lcfirst($key);
                    if (method_exists($entity, $method)) {
                        $this->$key = $entity->$method();
                    } elseif(isset($entity->$key) && !empty($entity->$key)) {
                        $this->$key = $entity->$key;
                    } elseif($entity instanceof ArrayAccess && isset($entity[$key])) {
                        $this->$key = $entity[$key];
                    }
                } elseif (is_array($entity) && isset($entity[$key])) {
                    $this->$key = $entity[$key];
                }
            }
        }

        $this->entity = $entity;

        if ($entity instanceof ApplicatePaneInterface) {
            $entity->apply($this);
        }
    }

    public function getEntity()
    {
        return $this->entity;
    }
}
