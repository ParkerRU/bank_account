<?php

namespace reference;

use enum\Rate;

// курс обмена
class CurrencyRates
{
    // тип валюты №1
    public Rate $currency_type1;

    // тип валюты №2
    public Rate $currency_type2;

    // значение обмена валюты 1
    private const CURRENCY_VALUE1 = 1;

    // значение обмена валюты 2
    public float $currency_value2;

    // Установить курс обмена валюты
    public function set_exchange_rate(array $first_rate, array $second_rate, float $value): void
    {
        $key = $first_rate['name'] . '/' . $second_rate['name'];
        $_SESSION['CurrencyRates'][$key] = $value;
    }
}
