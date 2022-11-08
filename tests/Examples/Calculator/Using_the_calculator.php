<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\Examples\Calculator;

use PHPUnit\Framework\TestCase;

final class Using_the_calculator extends TestCase
{
    private Calculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = Calculator::make();
    }

    /** @test */
    function solving_1()
    {
        self::assertEquals('1', $this->calculator->solve('1'));
    }

    /** @test */
    function solving_1_plus_1()
    {
        self::assertEquals('2', $this->calculator->solve('1+1'));
    }

    /** @test */
    function solving_1_plus_2()
    {
        self::assertEquals('3', $this->calculator->solve('1 + 2'));
    }

    /** @test */
    function solving_1_plus_2_plus_3()
    {
        self::assertEquals('6', $this->calculator->solve('1 + 2+ 3'));
    }

    /** @test */
    function solving_3_plus_2_and_a_half()
    {
        self::assertEquals('5.5', $this->calculator->solve('3 + 2.5'));
    }

    /** @test */
    function solving_3_plus_negative_1()
    {
        self::assertEquals('2', $this->calculator->solve('3 + -1'));
    }

    /** @test */
    function solving_3_plus_positive_1()
    {
        self::assertEquals('4', $this->calculator->solve('3 + +1'));
    }

    /** @test */
    function producing_an_error_for_one_plus_missing()
    {
        self::assertEquals('error: unexpected +', $this->calculator->solve('1 + '));
    }

    /** @test */
    function solving_3_minus_1()
    {
        self::assertEquals('2', $this->calculator->solve('3 - 1'));
    }

    /** @test */
    function solving_5_minus_3_minus_1()
    {
        self::assertEquals('1', $this->calculator->solve('5 - 3 - 1'));
    }

    /** @test */
    function solving_9_minus_3_times_2()
    {
        self::assertEquals('3', $this->calculator->solve('9 - 3 * 2'));
    }

    /** @test */
    function solving_9_divided_by_3_plus_2()
    {
        self::assertEquals('5', $this->calculator->solve('9 / 3 + 2'));
    }

    /** @test */
    function solving_5_to_the_power_of_3()
    {
        self::assertEquals('125', $this->calculator->solve('5 ^ 3'));
    }

    /** @test */
    function solving_5_to_the_power_of_3_times_two()
    {
        self::assertEquals('250', $this->calculator->solve('5 ^ 3 * 2'));
    }
}
