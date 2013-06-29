<?php
/** Copyright (C) Fusonic GmbH (http://www.fusonic.net) 2013, All rights reserved. */

use Fusonic\Linq\Linq;

class LinqTest extends PHPUnit_Framework_TestCase
{
    const ExceptionName_UnexpectedValue = "UnexpectedValueException";
    const ExceptionName_InvalidArgument = "InvalidArgumentException";
    const ExceptionName_OutOfRange = "OutOfRangeException";
    const ExceptionName_Runtime = "RuntimeException";

    /** Returns the only element of a sequence that satisfies a specified condition, and throws an exception if more than one such element exists.
     */
    public function testSingle_TestBehaviour()
    {
        // more than one
        $items = array(1, 2);
        $this->assertException(function() use ($items) {
            Linq::from($items)->single();
        });

        // no matching elements
        $items = array();
        $this->assertException(function() use ($items) {
            Linq::from($items)->single();
        });

        // OK
        $items = array(77);
        $this->assertSame(77, Linq::from($items)->single());

        // With closure

        // more than one
        $items = array(1, 2);
        $this->assertException(function() use ($items) {
            Linq::from($items)->single(function($x) { return true; });
        });

        // no matching elements
        // because of false closure
        $this->assertException(function() use ($items) {
            Linq::from($items)->single(function($x) { return false; });
        });

        // because of empty array
        $items = array();
        $this->assertException(function() use ($items) {
            Linq::from($items)->single(function($x) { return true; });
        });

        // OK
        $items = array(77);
        $this->assertSame(77, Linq::from($items)->single(function($x) { return true; }));
    }

    public function testCount_ReturnsCorrectAmounts()
    {
        $items = array(1, 2);
        $this->assertEquals(2, Linq::from($items)->count());

        $items = array(1, 2);
        $this->assertEquals(1, Linq::from($items)->where(function($x) {
            return $x == 2;
        })->count());

        $items = array(1, 2);
        $this->assertEquals(0, Linq::from($items)->where(function($x) {
            return false;
        })->count());

        $items = array();
        $this->assertEquals(0, Linq::from($items)->count());
    }

    /** Returns the only element of a sequence that satisfies a specified condition or a default value if no such element exists;
     * this method throws an exception if more than one element satisfies the condition.
     */
    public function testSingleOrNull_TestBehaviour()
    {
        // more than one
        $items = array(1, 2);
        $this->assertException(function() use ($items) {
            Linq::from($items)->singleOrNull();
        });

        // no matching elements
        $items = array();
        $this->assertNull(Linq::from($items)->singleOrNull());

        // OK
        $items = array(77);
        $this->assertSame(77, Linq::from($items)->singleOrNull());

        // With closure

        // more than one
        $items = array(1, 2);
        $this->assertException(function() use ($items) {
            Linq::from($items)->singleOrNull(function($x) { return true; });
        });

        // no matching elements
        // because of false closure
        $this->assertNull(Linq::from($items)->singleOrNull(function($x) { return false; }));

        // because of empty array
        $items = array();
        $this->assertNull(Linq::from($items)->singleOrNull());

        // OK
        $items = array(77);
        $this->assertSame(77, Linq::from($items)->singleOrNull(function($x) { return true; }));
    }

    /** Returns the first element in a sequence that satisfies a specified condition.
     *
     *  Exceptions:
     *  No element satisfies the condition in predicate.
        -or-
        The source sequence is empty.
     */
    public function testFirst_TestBehaviour()
    {
        $a = new stdClass();
        $a->value = "a";

        $b1 = new stdClass();
        $b1->value = "b";

        $b2 = new stdClass();
        $b2->value = "b";

        $c = new stdClass();
        $c->value = "c";

        // more than one
        $items = array($a, $b1, $b2, $c);
        $this->assertSame($a, Linq::from($items)->first());

        // no matching elements
        $items = array();
        $this->assertException(function() use ($items) {
            Linq::from($items)->first();
        });

        $items = array($a);
        $this->assertSame($a, Linq::from($items)->first());

        // #### With closures ###

        // more than one
        $items = array($a, $b1, $b2, $c);
        $this->assertSame($b1, Linq::from($items)->first(function($x) { return $x->value == "b"; }));

        // no matching elements
        // because of false closure
        $this->assertException(function() use ($items) {
            Linq::from($items)->first(function($x) { return false; });
        });

        // because of empty array
        $items = array();
        $this->assertException(function() use ($items) {
            Linq::from($items)->first(function($x) { return true; });
        });

        // OK
        $items = array($a);
        $this->assertSame($a, Linq::from($items)->first(function($x) { return true; }));
    }

    /**
     * Returns the first element of a sequence, or a default value if the sequence contains no elements.
     */
    public function testFirstOrNull_DoesReturnTheFirstElement_OrNull_DoesNotThrowExceptions()
    {
        $a = new stdClass();
        $a->value = "a";

        $b1 = new stdClass();
        $b1->value = "b";

        $b2 = new stdClass();
        $b2->value = "b";

        $c = new stdClass();
        $c->value = "c";

        $items = array($a, $b1, $b2, $c);
        $this->assertSame($a, Linq::from($items)->firstOrNull());

        $items = array();
        $this->assertNull(Linq::from($items)->firstOrNull());

        // #### With closures ###

        $items = array($a, $b1, $b2, $c);
        $this->assertSame($b1, Linq::from($items)->firstOrNull(function($x) { return $x->value == "b"; }));

        $items = array($a, $b1, $b2, $c);
        $this->assertSame($c, Linq::from($items)->firstOrNull(function($x) { return $x->value == "c"; }));

        $items = array();
        $this->assertNull(Linq::from($items)->firstOrNull(function($x) { return true; }));
    }


    public function testLast_DoesReturnTheLastElement_OrThrowsExceptions()
    {
        $a = new stdClass();
        $a->value = "a";

        $b1 = new stdClass();
        $b1->value = "b";

        $b2 = new stdClass();
        $b2->value = "b";

        $c = new stdClass();
        $c->value = "c";

        // more than one
        $items = array($a, $b1, $b2, $c);
        $last = Linq::from($items)->last();
        $this->assertSame($c, $last);

        // no matching elements
        $items = array();
        $this->assertException(function() use ($items) {
            Linq::from($items)->last();
        });

        $items = array($a);
        $this->assertSame($a, Linq::from($items)->last());

        // #### With closures ###

        // more than one
        $items = array($a, $b1, $b2, $c);
        $this->assertSame($b2, Linq::from($items)->last(function($x) { return $x->value == "b"; }));

        // no matching elements
        // because of false closure
        $this->assertException(function() use ($items) {
            Linq::from($items)->last(function($x) { return false; });
        });

        // because of empty array
        $items = array();
        $this->assertException(function() use ($items) {
            Linq::from($items)->last(function($x) { return true; });
        });

        // OK
        $items = array($a);
        $this->assertSame($a, Linq::from($items)->last(function($x) { return true; }));
    }

    public function testLastOrDefault_DoesReturnTheLastElement_OrNull_DoesNotThrowExceptions()
    {
        $a = new stdClass();
        $a->value = "a";

        $b1 = new stdClass();
        $b1->value = "b";

        $b2 = new stdClass();
        $b2->value = "b";

        $c = new stdClass();
        $c->value = "c";

        $items = array($a, $b1, $b2, $c);
        $this->assertSame($c, Linq::from($items)->lastOrNull());

        $items = array();
        $this->assertNull(Linq::from($items)->lastOrNull());

        // #### With closures ###

        $items = array($a, $b1, $b2, $c);
        $this->assertSame($c, Linq::from($items)->lastOrNull(function($x) { return true; }));

        $items = array($a, $b1, $b2, $c);
        $this->assertSame($b2, Linq::from($items)->lastOrNull(function($x) { return $x->value == "b"; }));

        $items = array();
        $this->assertNull(Linq::from($items)->lastOrNull(function($x) { return true; }));
    }

    public function testWhere_ReturnsOnlyValuesMatching()
    {
        $a = new stdClass();
        $a->value = "a";

        $b1 = new stdClass();
        $b1->value = "b";

        $b2 = new stdClass();
        $b2->value = "b";

        $c = new stdClass();
        $c->value = "c";

        $items = array($a, $b1, $b2, $c);
        $matching = Linq::from($items)->where(function ($v) { return false; });
        $this->assertTrue($matching instanceof Linq);

        $matching = $matching->toArray();

        $this->assertEquals(0, count($matching));

        $matching = Linq::from($items)->where(function ($v) { return true; })->toArray();
        $this->assertEquals(4, count($matching));
        $this->assertTrue(in_array($a, (array)$matching));
        $this->assertTrue(in_array($b1, (array)$matching));
        $this->assertTrue(in_array($b2, (array)$matching));
        $this->assertTrue(in_array($c, (array)$matching));

        $matching = Linq::from($items)->where(function ($v) { return $v->value == "b"; })->toArray();
        $this->assertEquals(2, count($matching));
        $this->assertFalse(in_array($a, (array)$matching));
        $this->assertTrue(in_array($b1, (array)$matching));
        $this->assertTrue(in_array($b2, (array)$matching));
        $this->assertFalse(in_array($c, (array)$matching));
    }

    public function testWhere_ThrowsExceptionIfPredicateDoesNotReturnABoolean()
    {
        $this->assertException(function()
        {
            $items = array("1", "2", "3");
            $matching = Linq::from($items)->where(function ($v) { return "NOT A BOOLEAN"; });
            $matching->toArray();
        }, self::ExceptionName_UnexpectedValue);
    }

    public function testWhere_DoesLazyEvaluation()
    {
        $eval = false;
        $items = array("1", "2", "3");
        $matching = Linq::from($items)->where(function ($v) use(&$eval)
        {
            $eval = true;
            return true;
        });

        $this->assertFalse($eval, "SelectMany did execute before iterating!");
        $matching->toArray();
        $this->assertTrue($eval);
    }

    public function testWhere_EmptySequence_ReturnsEmptySequence()
    {
        $items = array();
        $matching = Linq::from($items)->where(function ($v)
        {
            return true;
        });

        $this->assertEquals(0, $matching->count());
        $array = $matching->toArray();
        $this->assertEquals(0, count($array));
    }

    public function testSkipWithTake_Combined_SkipAndTakeValuesByAmount()
    {
        $items = array("a", "b", "c", "d", "e", "f");
        $matching = Linq::from($items)->skip(2)->take(0);
        $this->assertEquals(0, $matching->count());

        $matching = Linq::from($items)->skip(0)->take(0);
        $this->assertEquals(0, $matching->count());

        $matching = Linq::from($items)->skip(0)->take(2);
        $this->assertEquals(2, $matching->count());
        $array = $matching->toArray();
        $this->assertEquals("a", $array[0]);
        $this->assertEquals("b", $array[1]);

        $matching = Linq::from($items)->skip(2)->take(2);
        $this->assertEquals(2, $matching->count());
        $array = $matching->toArray();
        $this->assertEquals("c", $array[0]);
        $this->assertEquals("d", $array[1]);

        $matching = Linq::from($items)->skip(4)->take(99);
        $this->assertEquals(2, $matching->count());
        $array = $matching->toArray();
        $this->assertEquals("e", $array[0]);
        $this->assertEquals("f", $array[1]);
    }

    public function testTakeSkip_Combined_TakeAndSkipValuesByAmount()
    {
        $items = array("a", "b", "c", "d", "e", "f");
        $matching = Linq::from($items)->take(0)->skip(0);
        $this->assertEquals(0, $matching->count());

        $matching = Linq::from($items)->take(2)->skip(2);
        $this->assertEquals(0, $matching->count());

        $matching = Linq::from($items)->take(2)->skip(0);
        $this->assertEquals(2, $matching->count());
        $array = $matching->toArray();
        $this->assertEquals("a", $array[0]);
        $this->assertEquals("b", $array[1]);
    }

    public function testSkip_SkipValuesByAmount()
    {
        $items = array("a", "b", "c", "d", "e", "f");
        $matching = Linq::from($items)->skip(2);
        $this->assertTrue($matching instanceof Linq);

        $this->assertEquals(4, $matching->count());
        $matching = $matching->toArray();

        $this->assertTrue(in_array("c", $matching));
        $this->assertTrue(in_array("d", $matching));
        $this->assertTrue(in_array("e", $matching));
        $this->assertTrue(in_array("f", $matching));

        $items = array("a", "b", "c", "d", "e", "f");

        $matching = Linq::from($items)->skip(0);
        $this->assertEquals(6, $matching->count());
        $array = $matching->toArray();
        $this->assertEquals("a", $array[0]);
        $this->assertEquals("b", $array[1]);
        $this->assertEquals("c", $array[2]);
        $this->assertEquals("d", $array[3]);
        $this->assertEquals("e", $array[4]);
        $this->assertEquals("f", $array[5]);

        $matching = Linq::from($items)->skip(5);
        $this->assertEquals(1, $matching->count());
        $array = $matching->toArray();
        $this->assertEquals("f", $array[0]);

        $matching = Linq::from($items)->skip(6);
        $this->assertEquals(0, $matching->count());

        $matching = Linq::from($items)->skip(7);
        $this->assertEquals(0, $matching->count());

        // Test against empty sequence:

        $matching = Linq::from(array())->skip(0);
        $this->assertEquals(0, $matching->count());

        $matching = Linq::from(array())->skip(6);
        $this->assertEquals(0, $matching->count());
    }

    public function testTake_TakeValuesByAmount()
    {
        $items = array("a", "b", "c", "d", "e", "f");
        $matching = Linq::from($items)->take(4);
        $this->assertTrue($matching instanceof Linq);

        $this->assertEquals(4, $matching->count());
        $matching = $matching->toArray();
        $this->assertTrue(in_array("a", $matching));
        $this->assertTrue(in_array("b", $matching));
        $this->assertTrue(in_array("c", $matching));
        $this->assertTrue(in_array("d", $matching));

        $matching = Linq::from($items)->take(0);
        $this->assertEquals(0, $matching->count());
    }

    public function testAll_WorksCorrectly()
    {
        // All must always return true on empty sequences:
        $items = array();
        $all = Linq::from($items)->all(function($v) { return true; });
        $this->assertTrue($all);

        $all = Linq::from($items)->all(function($v) { return false; });
        $this->assertTrue($all);

        // Test with values:
        $items = array("a", "b");
        $all = Linq::from($items)->all(function($v) { return $v == "a"; });
        $this->assertFalse($all);

        $all = Linq::from($items)->all(function($v) { return $v == "a" || $v == "b"; });
        $this->assertTrue($all);
    }

    public function testAny_WithFunc_CorrectResults()
    {
        // Any must always return false on empty sequences:
        $items = array();
        $any = Linq::from($items)->any(function($v) { return true; });
        $this->assertFalse($any);

        $any = Linq::from($items)->any(function($v) { return false; });
        $this->assertFalse($any);

        // Test with values:
        $items = array("a", "b");
        $any = Linq::from($items)->any(function($v) { return $v == "not existing"; });
        $this->assertFalse($any);

        $any = Linq::from($items)->any(function($v) { return $v == "a"; });
        $this->assertTrue($any);
    }

    public function testAny_WithoutFunc_CorrectResults()
    {
        $items = array();
        $this->assertFalse(Linq::from($items)->any());

        $items = array("a");
        $this->assertTrue(Linq::from($items)->any());

        $items = array("a", "b", "c");
        $this->assertTrue(Linq::from($items)->any());
    }

    public function testAverage_throwsExceptionIfClosureReturnsNotNumericValue()
    {
        $this->assertException(function() {
           $items = array(2, new stdClass());
            Linq::from($items)->average();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function() {
            $items = array(2, "no numeric value");
            Linq::from($items)->average();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function() {
            $cls = new stdClass();
            $cls->value = "no numeric value";
            $items = array($cls);
            Linq::from($items)->average(function($x) { return $x->value; });
        }, self::ExceptionName_UnexpectedValue);
    }

    public function testAverage_CalculatesCorrectAverage()
    {
        $items = array(2, 4, 6);
        $avg = Linq::from($items)->average();
        $this->assertEquals(4, $avg);

        $items = array(2.5, 2.5);
        $avg = Linq::from($items)->average();
        $this->assertEquals(2.5, $avg);

        $items = array(2, "4", "6");
        $avg = Linq::from($items)->average();
        $this->assertEquals(4, $avg);

        $items = array(2, 4, 6);
        $avg = Linq::from($items)->average(function($v) { return 1; });
        $this->assertEquals(1, $avg);

        $items = array(2.5, 2.5);
        $avg = Linq::from($items)->average(function($v) { return $v; });
        $this->assertEquals(2.5, $avg);

        $a = new stdClass();
        $a->value = 2;

        $b = new stdClass();
        $b->value = "4";

        $c = new stdClass();
        $c->value = "6";

        $items = array($a, $b, $c);
        $avg = Linq::from($items)->average(function($v) { return $v->value; });
        $this->assertEquals(4, $avg);
    }

    public function testOrderBy_NumericValues_WithSelector_ReturnsOrderedObjects()
    {
        $a = new stdClass(); $a->value = 77;
        $b = new stdClass(); $b->value = 10;
        $c = new stdClass(); $c->value = 20;
        $items = array($a, $b, $c);

        $ascending = Linq::from($items)->orderBy(function($x)
        {
            return $x->value;
        });
        $this->assertEquals(3, $ascending->count());
        $items = $ascending->toArray();

        $this->assertSame($b, $items[0]);
        $this->assertSame($c, $items[1]);
        $this->assertSame($a, $items[2]);

        $a = new stdClass(); $a->value = 77;
        $b = new stdClass(); $b->value = 10.44;
        $c = new stdClass(); $c->value = 20;
        $d = new stdClass(); $d->value = 20;
        $items = array($a, $b, $c, $d);

        $ascending = Linq::from($items)->orderBy(function($x) { return $x->value; });
        $this->assertEquals(4, $ascending->count());
        $items = $ascending->toArray();

        $this->assertSame($b, $items[0]);

        // It is not predictable in which order objects with the same order key are ordered, both positions are valid:
        $pos1 = $items[1];
        $pos2 = $items[2];
        $this->assertTrue($pos1 === $d || $pos1 === $c);
        $this->assertTrue($pos1 === $d || $pos1 === $c);
        $this->assertNotsame($pos1, $pos2);

        $this->assertSame($a, $items[3]);
    }

    public function testOrderBy_NumericValues_ReturnsCorrectOrders()
    {
        $items = array(77, 10, 20);
        $ascending = Linq::from($items)->orderBy(function($x) { return $x; });
        $this->assertTrue($ascending instanceof Linq);

        $ascending = $ascending->toArray();

        $this->assertEquals(10, $ascending[0]);
        $this->assertEquals(20, $ascending[1]);
        $this->assertEquals(77, $ascending[2]);

        // Verify that original collection is unmodified:
        $this->assertEquals(77, $items[0]);
        $this->assertEquals(10, $items[1]);
        $this->assertEquals(20, $items[2]);

        $items = array(12.33, 8.21, 11.3, 8.21, 33);
        $ascending = Linq::from($items)->orderBy(function($x) { return $x; });

        $ascending = $ascending->toArray();
        $this->assertEquals(8.21, $ascending[0]);
        $this->assertEquals(8.21, $ascending[1]);
        $this->assertEquals(11.3, $ascending[2]);
        $this->assertEquals(12.33, $ascending[3]);
        $this->assertEquals(33, $ascending[4]);
    }

    public function testOrderBy_StringValues_ReturnsCorrectOrders()
    {
        $items = array("e", "a", "c");
        $ascending = Linq::from($items)->orderBy(function($x) { return $x; });
        $this->assertTrue($ascending instanceof Linq);

        $ascending = $ascending->toArray();
        $this->assertEquals("a", $ascending[0]);
        $this->assertEquals("c", $ascending[1]);
        $this->assertEquals("e", $ascending[2]);

        // Verify that original collection is unmodified:
        $this->assertEquals("e", $items[0]);
        $this->assertEquals("a", $items[1]);
        $this->assertEquals("c", $items[2]);
    }

    public function testOrderBy_DateTimeValues_ReturnsCorrectOrders()
    {
        $items = array(new DateTime("27.10.2011"), new DateTime("03.04.2012"), new DateTime("01.01.2005"));
        $ascending = Linq::from($items)->orderBy(function($x) { return $x; });
        $this->assertTrue($ascending instanceof Linq);
        $ascending = $ascending->toArray();

        $this->assertEquals(new DateTime("01.01.2005"), $ascending[0]);
        $this->assertEquals(new DateTime("27.10.2011"), $ascending[1]);
        $this->assertEquals(new DateTime("03.04.2012"), $ascending[2]);

        // Verify that original collection is unmodified:
        $this->assertEquals(new DateTime("27.10.2011"), $items[0]);
        $this->assertEquals(new DateTime("03.04.2012"), $items[1]);
        $this->assertEquals(new DateTime("01.01.2005"), $items[2]);
    }

    public function testOrderByDescending_NumericValues_ReturnsCorrectOrders()
    {
        $items = array(77, 10, 20);
        $desc = Linq::from($items)->orderByDescending(function($x) { return $x; });
        $this->assertTrue($desc instanceof Linq);
        $desc = $desc->toArray();

        $this->assertEquals(77, $desc[0]);
        $this->assertEquals(20, $desc[1]);
        $this->assertEquals(10, $desc[2]);

        // Verify that original collection is unmodified:
        $this->assertEquals(77, $items[0]);
        $this->assertEquals(10, $items[1]);
        $this->assertEquals(20, $items[2]);

        $items = array(12.33, 8.21, 11.3, 8.21, 33);
        $desc = Linq::from($items)->orderByDescending(function($x) { return $x; });
        $desc = $desc->toArray();
        $this->assertEquals(33, $desc[0]);
        $this->assertEquals(12.33, $desc[1]);
        $this->assertEquals(11.3, $desc[2]);
        $this->assertEquals(8.21, $desc[3]);
        $this->assertEquals(8.21, $desc[4]);
    }

    public function testOrderByDescending_NumericValues_WithSelector_ReturnsOrderedObjects()
    {
        $a = new stdClass(); $a->value = 77;
        $b = new stdClass(); $b->value = 10;
        $c = new stdClass(); $c->value = 20;
        $items = array($a, $b, $c);

        $ascending = Linq::from($items)->orderByDescending(function($x) { return $x->value; });
        $this->assertEquals(3, $ascending->count());
        $items = $ascending->toArray();

        $this->assertSame($a, $items[0]);
        $this->assertSame($c, $items[1]);
        $this->assertSame($b, $items[2]);

        $a = new stdClass(); $a->value = 77;
        $b = new stdClass(); $b->value = 10.44;
        $c = new stdClass(); $c->value = 20;
        $d = new stdClass(); $d->value = 20;
        $items = array($a, $b, $c, $d);

        $ascending = Linq::from($items)->orderByDescending(function($x) { return $x->value; });
        $this->assertEquals(4, $ascending->count());
        $items = $ascending->toArray();

        $this->assertSame($a, $items[0]);
        $this->assertSame($c, $items[1]);

        // It is not predictable in which order objects with the same order key are ordered, both positions are valid:
        $pos1 = $items[2];
        $pos2 = $items[3];
        $this->assertTrue($pos1 === $d || $pos1 === $c);
        $this->assertTrue($pos1 === $d || $pos1 === $c);
        $this->assertNotsame($pos1, $pos2);
    }

    public function testOrderByDescending_DateTimeValues_ReturnsCorrectOrders()
    {
        $items = array(new DateTime("27.10.2011"), new DateTime("03.04.2012"), new DateTime("01.01.2005"));
        $desc = Linq::from($items)->orderByDescending(function($x) { return $x; });
        $this->assertTrue($desc instanceof Linq);
        $desc = $desc->toArray();

        $this->assertEquals(new DateTime("03.04.2012"), $desc[0]);
        $this->assertEquals(new DateTime("27.10.2011"), $desc[1]);
        $this->assertEquals(new DateTime("01.01.2005"), $desc[2]);

        // Verify that original collection is unmodified:
        $this->assertEquals(new DateTime("27.10.2011"), $items[0]);
        $this->assertEquals(new DateTime("03.04.2012"), $items[1]);
        $this->assertEquals(new DateTime("01.01.2005"), $items[2]);
    }

    public function testOrderBy_Descending_StringValues_ReturnsCorrectOrders()
    {
        $items = array("e", "a", "c");
        $desc = Linq::from($items)->orderByDescending(function($x) { return $x; });
        $this->assertTrue($desc instanceof Linq);
        $desc = $desc->toArray();

        $this->assertEquals("e", $desc[0]);
        $this->assertEquals("c", $desc[1]);
        $this->assertEquals("a", $desc[2]);

        // Verify that original collection is unmodified:
        $this->assertEquals("e", $items[0]);
        $this->assertEquals("a", $items[1]);
        $this->assertEquals("c", $items[2]);
    }

    public function testOrderBy_DoesMakeLazyEvalution()
    {
        $items = array("e", "a", "c");
        $eval = false;
        $linq = Linq::from($items)->orderByDescending(function($x) use(&$eval)
        {
            $eval = true;
            return $x;
        });

        $this->assertFalse($eval, "OrderBy did execute before iterating!");
        $result = $linq->toArray();
        $this->assertTrue($eval);
    }

    public function testGroupBy()
    {
        $a1 = new stdClass(); $a1->id = 1; $a1->value = "a";
        $a2 = new stdClass(); $a2->id = 2; $a2->value = "a";
        $b1 = new stdClass(); $b1->id = 3; $b1->value = "b";

        $items = array ($a1, $a2, $b1);
        $grouped = Linq::from($items)->groupBy(function($x) {
            return $x->value;
        });

        $this->assertTrue($grouped instanceof Linq);

        $this->assertEquals(2, $grouped->count());
        $aGroup = $grouped->elementAt(0);
        $this->assertTrue($aGroup instanceof Fusonic\Linq\GroupedLinq);

        $this->assertEquals("a", $aGroup->groupKey());
        $this->assertEquals(2, $aGroup->count());
        $this->assertSame($a1, $aGroup->elementAt(0));
        $this->assertSame($a2, $aGroup->elementAt(1));

        $bGroup = $grouped->elementAt(1);
        $this->assertEquals("b", $bGroup->groupKey());
        $this->assertEquals(1, $bGroup->count());
        $this->assertSame($b1, $bGroup->elementAt(0));
    }

    public function testElementAt_ReturnsElementAtPositionOrThrowsException()
    {
        $items = array ("a", "b", "c");
        $this->assertEquals("a", Linq::from($items)->elementAt(0));
        $this->assertEquals("b", Linq::from($items)->elementAt(1));
        $this->assertEquals("c", Linq::from($items)->elementAt(2));

        $items = array("a" => "aValue", "b" => "bValue");
        $this->assertEquals("aValue", Linq::from($items)->elementAt(0));
        $this->assertEquals("bValue", Linq::from($items)->elementAt(1));

        $this->assertException(function() {
            $items = array();
            Linq::from($items)->elementAt(0);
        }, self::ExceptionName_OutOfRange);

        $this->assertException(function() {
            $items = array();
            Linq::from($items)->elementAt(1);
        }, self::ExceptionName_OutOfRange);

        $this->assertException(function() {
            $items = array();
            Linq::from($items)->elementAt(-1);
        }, self::ExceptionName_OutOfRange);

        $this->assertException(function() {
            $items = array("a");
            Linq::from($items)->elementAt(1);
        }, self::ExceptionName_OutOfRange);

        $this->assertException(function() {
            $items = array("a", "b");
            Linq::from($items)->elementAt(2);
        }, self::ExceptionName_OutOfRange);

        $this->assertException(function() {
            $items = array("a", "b");
            Linq::from($items)->elementAt(-1);
        }, self::ExceptionName_OutOfRange);

        $this->assertException(function() {
            $items = array("a" => "value", "b" => "bValue");
            Linq::from($items)->elementAt(2);
        }, self::ExceptionName_OutOfRange);
    }

    public function testElementAtOrNull_ReturnsElementAtPositionOrNull()
    {
        $items = array ("a", "b", "c");
        $this->assertEquals("a", Linq::from($items)->elementAtOrNull(0));
        $this->assertEquals("b", Linq::from($items)->elementAtOrNull(1));
        $this->assertEquals("c", Linq::from($items)->elementAtOrNull(2));

        $this->assertNull(Linq::from($items)->elementAtOrNull(3));
        $this->assertNull(Linq::from($items)->elementAtOrNull(4));
        $this->assertNull(Linq::from($items)->elementAtOrNull(-1));

        $items = array();
        $this->assertNull(Linq::from($items)->elementAtOrNull(3));
        $this->assertNull(Linq::from($items)->elementAtOrNull(4));
        $this->assertNull(Linq::from($items)->elementAtOrNull(-1));

        $items = array("a" => "value", "b" => "bValue");
        $this->assertEquals("value", Linq::from($items)->elementAtOrNull(0));
        $this->assertNull(Linq::from($items)->elementAtOrNull(2));
    }

    public function testSelect_ReturnsProjectedSequence()
    {
        $a1 = new stdClass(); $a1->value = "a1";
        $a2 = new stdClass(); $a2->value = "a2";
        $a3 = new stdClass(); $a3->value = "a3";
        $a4 = new stdClass(); $a4->value = "a4";

        // more than one
        $items = array($a1, $a2, $a3, $a4);

        $projected = Linq::from($items)->select(function($v){
           return $v->value;
        });

        $this->assertTrue($projected instanceof Linq);
        $this->assertEquals(4, $projected->count());

        $projected = $projected->toArray();
        $this->assertEquals("a1", $projected[0]);
        $this->assertEquals("a2", $projected[1]);
        $this->assertEquals("a3", $projected[2]);
        $this->assertEquals("a4", $projected[3]);

        $items = array();

        $projected = Linq::from($items)->select(function($v){
            return $v->value;
        });

        $this->assertEquals(0, $projected->count());
    }

    public function testSelectMany_throwsExceptionIfElementIsNotIterable()
    {
        $a1 = new stdClass(); $a1->value = "a1";
        $items = array($a1);

        $this->assertException(function() use($items) {
            Linq::from($items)->selectMany(function($v) {
                return $v->value;
            })->toArray();

        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function() use($items) {
            Linq::from($items)->selectMany(function($v) {
                return null;
            })->toArray();
        }, self::ExceptionName_UnexpectedValue);
    }

    public function testSelectMany_ReturnsFlattenedSequence()
    {
        $a1 = new stdClass(); $a1->value = array("a", "b");
        $a2 = new stdClass(); $a2->value = array("c", "d");
        $items = array($a1, $a2);

        $linq = Linq::from($items)->selectMany(function($x)
        {
            return $x->value;
        });

        $this->assertTrue($linq instanceof Linq);

        $this->assertEquals(4, $linq->count());

        $array = $linq->toArray();
        $this->assertEquals("a", $array[0]);
        $this->assertEquals("b", $array[1]);
        $this->assertEquals("c", $array[2]);
        $this->assertEquals("d", $array[3]);

        // Try once again to see if rewinding the iterator works:
        $this->assertEquals(4, $linq->count());

        $array = $linq->toArray();
        $this->assertEquals("a", $array[0]);
        $this->assertEquals("b", $array[1]);
        $this->assertEquals("c", $array[2]);
        $this->assertEquals("d", $array[3]);
    }

    public function testSelectMany_EmptySequence_ReturnsEmptySequence()
    {
        $linq = Linq::from(array())->selectMany(function($x)
        {
            return $x->value;
        });

        $this->assertEquals(0, $linq->count());
        $array = $linq->toArray();
        $this->assertEquals(0, count($array));

        // Try once again to see if rewinding the iterator works:
        $this->assertEquals(0, $linq->count());
        $array = $linq->toArray();
        $this->assertEquals(0, count($array));
    }

    public function testSelectMany_DoesLazyEvaluation()
    {
        $a1 = new stdClass(); $a1->value = array("a", "b");
        $a2 = new stdClass(); $a2->value = array("c", "d");
        $items = array($a1, $a2);

        $eval = false;
        $flattened = Linq::from($items)->selectMany(function($x) use(&$eval)
        {
            $eval = true;
            return $x->value;
        });

        $this->assertFalse($eval, "SelectMany did execute before iterating!");
        $result = $flattened->toArray();
        $this->assertTrue($eval);
    }

    public function testConcat_ReturnsConcatenatedElements()
    {
        $first = array("a", "b");
        $second = array("c", "d");

        $all = Linq::from($first)->concat($second);
        $this->assertTrue($all instanceof Linq);

        $this->assertEquals(4, $all->count());

        $all = $all->toArray();
        $this->assertEquals("a", $all[0]);
        $this->assertEquals("b", $all[1]);
        $this->assertEquals("c", $all[2]);
        $this->assertEquals("d", $all[3]);
    }

    public function testConcat_ThrowsArgumentExceptionIfNoTraversableArgument()
    {
        $this->assertException(function() {
                $input = array();
                $linq = Linq::from($input);
                $linq->concat(null);
            },self::ExceptionName_InvalidArgument);

        $this->assertException(function() {
            $input = array();
            $second = new stdClass();
            Linq::from($input)->concat($second);
        },self::ExceptionName_InvalidArgument);
    }

    public function testIntersect_ReturnsIntersectedElements()
    {
        $first = array("a", "b", "c", "d");
        $second = array("b", "c");

        $linq = Linq::from($first)->intersect($second);
        $this->assertEquals(2, $linq->count());

        $array = $linq->toArray();
        $this->assertEquals("b", $array[0]);
        $this->assertEquals("c", $array[1]);
    }

    public function testIntersect_ThrowsArgumentExceptionIfSecondSequenceIsNotTraversable()
    {
        $this->assertException(function() {
            $input = array();
            $linq = Linq::from($input);
            $linq->intersect(null);
        },self::ExceptionName_InvalidArgument);

        $this->assertException(function() {
            $input = array();
            $linq = Linq::from($input);
            $linq->intersect("Not a sequence");
        },self::ExceptionName_InvalidArgument);
    }

    public function testIntersect_EmptySequence_ReturnsEmptySequence()
    {
        $first = array("a", "b", "c", "d");
        $second = array();

        $linq = Linq::from($first)->intersect($second);
        $this->assertEquals(0, $linq->count());
        $array = $linq->toArray();
        $this->assertEquals(0, count($array));
    }

    public function testDiff_ReturnsDifferentElements()
    {
        $first = array("a", "b", "c", "d");
        $second = array("b", "c");

        $linq = Linq::from($first)->diff($second);
        $this->assertEquals(2, $linq->count());

        $array = $linq->toArray();
        $this->assertEquals("a", $array[0]);
        $this->assertEquals("d", $array[1]);
    }

    public function testDiff_ThrowsArgumentExceptionIfSecondSequenceIsNotTraversable()
    {
        $this->assertException(function() {
            $input = array();
            $linq = Linq::from($input);
            $linq->diff(null);
        },self::ExceptionName_InvalidArgument);

        $this->assertException(function() {
            $input = array();
            $linq = Linq::from($input);
            $linq->diff("Not a sequence");
        },self::ExceptionName_InvalidArgument);
    }

    public function testDiff_EmptySequence_ReturnsAllElementsFromFirst()
    {
        $first = array("a", "b", "c", "d");
        $second = array();

        $linq = Linq::from($first)->diff($second);
        $this->assertEquals(4, $linq->count());

        $array = $linq->toArray();
        $this->assertEquals("a", $array[0]);
        $this->assertEquals("b", $array[1]);
        $this->assertEquals("c", $array[2]);
        $this->assertEquals("d", $array[3]);
    }

    public function testDistinct_ReturnsDistinctElements()
    {
        $items = array("a", "b", "a", "b");

        $distinct = Linq::from($items)->distinct();
        $this->assertTrue($distinct instanceof Linq);

        $this->assertEquals(2, $distinct->count());
        $distinct = $distinct->toArray();
        $this->assertEquals("a", $distinct[0]);
        $this->assertEquals("b", $distinct[1]);

        $a1 = new stdClass(); $a1->id = 1; $a1->value = "a";
        $a2 = new stdClass(); $a2->id = 2; $a2->value = "a";
        $b1 = new stdClass(); $b1->id = 3; $b1->value = "b";

        $items = array($a1, $a2, $b1);
        $distinct = Linq::from($items)->distinct(function($v) { return $v->value; });
        $this->assertEquals(2, $distinct->count());
    }

    public function testDistinct_DoesLazyEvaluation()
    {
        $eval = false;
        $a1 = new stdClass(); $a1->id = 1; $a1->value = "a";
        $a2 = new stdClass(); $a2->id = 2; $a2->value = "a";

        $items = array($a1, $a2);
        $distinct = Linq::from($items)->distinct(function($v) use(&$eval)
        {
            $eval = true;
            return $v->value;
        });

        $this->assertFalse($eval, "SelectMany did execute before iterating!");
        $distinct->toArray();
        $this->assertTrue($eval);
    }

    public function testMin_ReturnsMinValueFromNumerics()
    {
        $items = array(88, 77, 12, 112);
        $min = Linq::from($items)->min();
        $this->assertEquals(12, $min);

        $items = array(13);
        $min = Linq::from($items)->min();
        $this->assertEquals(13, $min);

        $items = array(0);
        $min = Linq::from($items)->min();
        $this->assertEquals(0, $min);

        $items = array(-12);
        $min = Linq::from($items)->min();
        $this->assertEquals(-12, $min);

        $items = array(-12, 0, 100, -33);
        $min = Linq::from($items)->min();
        $this->assertEquals(-33, $min);
    }

    public function testMin_ReturnsMinValueFromStrings()
    {
        $items = array("c", "a", "b", "d");
        $min = Linq::from($items)->min();
        $this->assertEquals("a", $min);

        $items = array("a");
        $min = Linq::from($items)->min();
        $this->assertEquals("a", $min);
    }

    public function testMin_ThrowsExceptionIfSequenceIsEmpty()
    {
        $this->assertException(function()
        {
            $data = array();
            $min = Linq::from($data)->min();
        });
    }

    public function testMin_ThrowsExceptionIfSequenceContainsNoneNumericValuesOrStrings()
    {
        $this->assertException(function()
        {
            $data = array(null);
            $max = Linq::from($data)->min();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function()
        {
            $data = array(new stdClass());
            $min = Linq::from($data)->min();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function()
        {
            $data = array("string value", 1, new stdClass());
            $min = Linq::from($data)->min();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function()
        {
            $a = new stdClass(); $a->nonNumeric = new stdClass();
            $data = array($a);
            $min = Linq::from($data)->min(function($x)
            {
                return $x->nonNumeric;
            });
        }, self::ExceptionName_UnexpectedValue);
    }

    public function testMax_ReturnsMaxValueFromNumerics()
    {
        $items = array(88, 77, 12, 112);
        $max = Linq::from($items)->max();
        $this->assertEquals(112, $max);

        $items = array(13);
        $max = Linq::from($items)->max();
        $this->assertEquals(13, $max);

        $items = array(0);
        $max = Linq::from($items)->max();
        $this->assertEquals(0, $max);

        $items = array(-12);
        $max = Linq::from($items)->max();
        $this->assertEquals(-12, $max);

        $items = array(-12, 0, 100, -33);
        $max = Linq::from($items)->max();
        $this->assertEquals(100, $max);
    }

    public function testSum_ThrowsExceptionIfSequenceContainsNoneNumericValues()
    {
        $this->assertException(function()
        {
            $data = array(null);
            $max = Linq::from($data)->sum();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function()
        {
            $data = array(new stdClass());
            $min = Linq::from($data)->sum();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function()
        {
            $data = array("string value", 1, new stdClass());
            $min = Linq::from($data)->sum();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function()
        {
            $a = new stdClass(); $a->value = 100; $a->nonNumeric = "asdf";
            $b = new stdClass(); $b-> value = 133;  $a->nonNumeric = "asdf";

            $data = array($a, $b);
            $sum = Linq::from($data)->sum(function($x) {
                return $x->nonNumeric;
            });
        }, self::ExceptionName_UnexpectedValue);
    }

    public function testSum_GetSumOfValues()
    {
        $data = array();
        $sum = Linq::from($data)->sum();
        $this->assertEquals(0, $sum);

        $data = array(4, 9, 100.77);
        $sum = Linq::from($data)->sum();
        $this->assertEquals(113.77, $sum);

        $data = array(12, -12);
        $sum = Linq::from($data)->sum();
        $this->assertEquals(0, $sum);

        $data = array(12, -24);
        $sum = Linq::from($data)->sum();
        $this->assertEquals(-12, $sum);

        $a = new stdClass(); $a->value = 100;
        $b = new stdClass(); $b-> value = 133;

        $data = array($a, $b);
        $sum = Linq::from($data)->sum(function($x) {
            return $x->value;
        });

        $this->assertEquals(233, $sum);
    }

    public function testMax_ReturnsMaxValueFromStrings()
    {
        $items = array("c", "a", "b", "d");
        $max = Linq::from($items)->max();
        $this->assertEquals("d", $max);

        $items = array("a");
        $max = Linq::from($items)->max();
        $this->assertEquals("a", $max);
    }

    public function testMax_ThrowsExceptionIfSequenceIsEmpty()
    {
        $this->assertException(function()
        {
            $data = array();
            $max = Linq::from($data)->max();
        });
    }

    public function testMax_ThrowsExceptionIfSequenceContainsNoneNumericValuesOrStrings()
    {
        $this->assertException(function()
        {
            $data = array(new stdClass());
            $max = Linq::from($data)->max();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function()
        {
            $data = array(null);
            $max = Linq::from($data)->max();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function()
        {
            $data = array("string value", 1, new stdClass());
            $max = Linq::from($data)->max();
        }, self::ExceptionName_UnexpectedValue);

        $this->assertException(function()
        {
            $a = new stdClass(); $a->nonNumeric = new stdClass();
            $data = array($a);
            $min = Linq::from($data)->max(function($x)
            {
                return $x->nonNumeric;
            });
        }, self::ExceptionName_UnexpectedValue);
    }

    public function testEach_PerformsActionOnEachElement()
    {
        $items = array("a", "b", "c");
        $looped = array();
        Linq::from($items)
            ->each(function($x) use(&$looped)
            {
               $looped[] = $x;
            });

        $this->assertEquals(3, count($looped));
        $this->assertEquals("a", $looped[0]);
        $this->assertEquals("b", $looped[1]);
        $this->assertEquals("c", $looped[2]);
    }

    public function testEach_ReturnsOriginalLinqSequence()
    {
        $linq = Linq::from(array(1,2,3,4))
            ->skip(2)->take(1);

        $linqAfterEach = $linq->each(function($x) {});
        $this->assertSame($linq, $linqAfterEach);
    }

    private function assertException($closure, $expected = self::ExceptionName_Runtime)
    {
        try
        {
            $closure();
        }
        catch(Exception $ex)
        {
            $exName = get_class($ex);

            if($exName != $expected)
            {
                $this->fail("Wrong exception raised. Expected: '" . $expected . "' Actual: '" . get_class($ex) . "'. Message: " . $ex->getMessage());
            }
            return;
        }

        $this->fail($expected .' has not been raised.');
    }
}
