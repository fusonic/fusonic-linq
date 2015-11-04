<?php

/*
 * This file is part of Fusonic-linq.
 * https://github.com/fusonic/fusonic-linq
 *
 * (c) Fusonic GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fusonic\Linq\Iterator;

use Iterator;
use ArrayIterator;
use Fusonic\Linq;
use Fusonic\Linq\Helper;

class OrderIterator implements Iterator
{
    private $iterator;
    private $orderedIterator;
    private $orderFuncs = array();

    public function __construct(Iterator $items, $orderKeyFunc, $direction)
    {
        $this->iterator = $items;
        $this->orderFuncs[] = array(
            'func' => $orderKeyFunc,
            'direction' => $direction
        );
    }

    public function current()
    {
        return $this->orderedIterator->current();
    }

    public function next()
    {
        $this->orderedIterator->next();
    }

    public function key()
    {
        return $this->orderedIterator->key();
    }

    public function valid()
    {
        return $this->orderedIterator->valid();
    }

    public function rewind()
    {
        if ($this->orderedIterator == null) {
            $this->orderItems();
        }
        $this->orderedIterator->rewind();
    }

    public function orderItems()
    {
        $itemIterator = $this->iterator;
        $itemIterator->rewind();
        if (!$itemIterator->valid()) {
            $this->orderedIterator = new ArrayIterator();
            return;
        }

        $this->orderedIterator = $this->iterator;

        // Ugly hack for PHP 5.3 as the calling context is not handled correctly in anonymous functions
        $self = $this;
        $orderFuncs = $this->orderFuncs;

        $this->orderedIterator->uasort(function($a, $b) use ($self, $orderFuncs) {
            $result = 0;
            foreach ($orderFuncs as $orderFunc) {
                $func = $orderFunc['func'];

                if ($orderFunc['direction'] === Helper\LinqHelper::LINQ_ORDER_ASC) {
                    $result = $self->compare($func($a), $func($b));
                } else {
                    $result = $self->compare($func($b), $func($a));
                }

                if ($result !== 0) {
                    break;
                }
            }

            return $result;
        });
    }

    public function compare($a, $b)
    {
        if(is_string($a) && is_string($b))
        {
            return strcasecmp($a, $b);
        }
        else
        {
            if($a == $b) return 0;
            return $a < $b ? -1 : 1;
        }
    }

    public function thenBy($func, $direction)
    {
        $this->orderFuncs[] = array(
            "func" => $func,
            "direction" => $direction,
        );
    }
}
