<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class PointsCalculator
{
    public const SETTING_KEY = 'points.formula';
    public const DEFAULT_FORMULA = 'floor(kilos * 10)';

    public function calculate(float $kilos): int
    {
        $formula = $this->getFormula();

        $this->assertFormulaIsSafe($formula, true);

        $expression = preg_replace('/kilos/i', sprintf('(%s)', $kilos), $formula);

        $php = 'return ' . $expression . ';';

        try {
            $result = eval($php);
        } catch (\Throwable $e) {
            return 0;
        }

        return max(0, (int) $result);
    }

    public function updateFormula(string $formula): void
    {
        $this->assertFormulaIsSafe($formula, true);

        $testExpression = preg_replace('/kilos/i', '(1)', $formula);

        try {
            eval('return ' . $testExpression . ';');
        } catch (\ParseError $e) {
            throw new InvalidArgumentException('La fórmula no es válida.');
        }

        Setting::updateOrCreate(
            ['key' => self::SETTING_KEY],
            ['value' => $formula]
        );

        Cache::forget(self::SETTING_KEY);
    }

    public function getFormula(): string
    {
        return Cache::rememberForever(self::SETTING_KEY, function () {
            return optional(Setting::where('key', self::SETTING_KEY)->first())->value
                ?? self::DEFAULT_FORMULA;
        });
    }

    private function assertFormulaIsSafe(string $formula, bool $throw = false): void
    {
        if (!preg_match('/kilos/i', $formula)) {
            if ($throw) {
                throw new InvalidArgumentException('La fórmula debe incluir la variable kilos.');
            }
        }

        $clean = strtolower(preg_replace('/\s+/', '', $formula));
        $allowedWords = ['kilos', 'floor', 'ceil', 'round', 'min', 'max'];

        foreach ($allowedWords as $word) {
            $clean = str_replace($word, '', $clean);
        }

        if (!preg_match('/^[0-9+\-*\/().,]*$/', $clean)) {
            if ($throw) {
                throw new InvalidArgumentException('La fórmula contiene caracteres no permitidos.');
            }
        }
    }
}
