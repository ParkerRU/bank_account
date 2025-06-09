<?php

namespace reference;

// курс обмена
class CurrencyRates
{
    // Установить курс обмена валюты
    public function set_exchange_rate(array $first_rate, array $second_rate, float $value): void
    {
        $key = $first_rate['name'] . '/' . $second_rate['name'];
        $_SESSION['CurrencyRates'][$key] = $value;
    }
}
