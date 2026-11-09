<?php

namespace Tests\Unit;

use App\Models\BarSale;
use PHPUnit\Framework\TestCase;

class BarSalePrintNotationTest extends TestCase
{
    public function test_print_package_notation_both_initial_and_added(): void
    {
        $sale = new BarSale([
            'initial_packages' => 4,
            'added_packages' => 5,
            'night_packages' => 0,
        ]);

        $this->assertEquals('4+(5)', $sale->getPrintPackageNotation());
    }

    public function test_print_package_notation_only_added(): void
    {
        $sale = new BarSale([
            'initial_packages' => 0,
            'added_packages' => 3,
            'night_packages' => 0,
        ]);

        $this->assertEquals('(3)', $sale->getPrintPackageNotation());
    }

    public function test_print_package_notation_only_initial(): void
    {
        $sale = new BarSale([
            'initial_packages' => 1,
            'added_packages' => 0,
            'night_packages' => 0,
        ]);

        $this->assertEquals('1', $sale->getPrintPackageNotation());
    }

    public function test_print_package_notation_with_night_replenishment(): void
    {
        $sale = new BarSale([
            'initial_packages' => 1,
            'added_packages' => 1,
            'night_packages' => 1,
        ]);

        $this->assertEquals('1+(1) 1', $sale->getPrintPackageNotation());
    }

    public function test_print_package_notation_empty(): void
    {
        $sale = new BarSale([
            'initial_packages' => 0,
            'added_packages' => 0,
            'night_packages' => 0,
        ]);

        $this->assertEquals('', $sale->getPrintPackageNotation());
    }

    public function test_print_unit_notation_both_initial_and_added(): void
    {
        $sale = new BarSale([
            'initial_units' => 2,
            'added_units' => 12,
            'night_units' => 0,
        ]);

        $this->assertEquals('2+(12)', $sale->getPrintUnitNotation());
    }

    public function test_print_unit_notation_only_added(): void
    {
        $sale = new BarSale([
            'initial_units' => 0,
            'added_units' => 2,
            'night_units' => 0,
        ]);

        $this->assertEquals('(2)', $sale->getPrintUnitNotation());
    }

    public function test_print_unit_notation_only_initial(): void
    {
        $sale = new BarSale([
            'initial_units' => 5,
            'added_units' => 0,
            'night_units' => 0,
        ]);

        $this->assertEquals('5', $sale->getPrintUnitNotation());
    }
}

