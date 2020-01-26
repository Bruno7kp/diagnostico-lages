<?php


namespace App\Indicator;


class IndicatorType
{
    const INT_HIGHER = "int_h";
    const INT_LOWER = "int_l";
    const FLOAT_HIGHER = "float_h";
    const FLOAT_LOWER = "float_l";
    const PERCENT = "percent";
    const TEXT = "text";

    public static function get() {
        return [
            IndicatorType::INT_HIGHER => "Número inteiro",
            IndicatorType::INT_LOWER => "Número inteiro (quanto menor o índice, melhor)",
            IndicatorType::FLOAT_HIGHER => "Número decimal",
            IndicatorType::FLOAT_LOWER => "Número decimal (quanto menor o índice, melhor)",
            IndicatorType::PERCENT => "Porcentagem",
            IndicatorType::TEXT => "Texto",
        ];
    }
}
