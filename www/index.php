<?php

use enum\Rate;
use document\CurrencyAccount;
use reference\CurrencyRates;

require_once /** @lang text */ '/www/reference/CurrencyRates.php';
require_once /** @lang text */ '/www/document/CurrencyAccount.php';
require_once /** @lang text */ '/www/enum/Rate.php';

session_start();

echo '<pre>';
echo "Устанавливаем курс по умолчанию<br>";
echo "--------------------------------<br>";
echo '<p>';

// Устанавливаем базовый курс
$currency_rate = new CurrencyRates();

// EUR / RUB = 80
$currency_rate->set_exchange_rate(Rate::EUR, Rate::RUB, 80);
// USD / RUB = 70
$currency_rate->set_exchange_rate(Rate::USD, Rate::RUB, 70);
// EUR / USD = 1
$currency_rate->set_exchange_rate(Rate::EUR, Rate::USD, 1);


// Задание #1
$user_id = '1f18ed4c-c036-4f5c-ad41-5785623f73a4'; // Идентификатор пользователя
$currency_account = new CurrencyAccount();

// Открыть новый счет
$currency_account->create_account($user_id);
// Добавить валюту (RUB)
$currency_account->add_currency($user_id, Rate::RUB);
// Добавить валюту (EUR)
$currency_account->add_currency($user_id, Rate::EUR);
// Добавить валюту (USD)
$currency_account->add_currency($user_id, Rate::USD);
// Устанавливаем основную валюту (RUB)
$currency_account->set_main_currency($user_id, Rate::RUB);
// Список поддерживаемых валют
$currency_account->get_supported_currency($user_id);
// Пополнить баланс (RUB(1000))
$currency_account->up_balance($user_id, Rate::RUB, 1000);
// Пополнить баланс (EUR(50))
$currency_account->up_balance($user_id, Rate::EUR, 50);
// Пополнить баланс (USD(50))
$currency_account->up_balance($user_id, Rate::USD, 50);

// Задание #2

// Получить баланс (в основной валюте)
$currency_account->get_balance_own_rate($user_id);
// Получить баланс USD
$currency_account->get_balance_own_rate($user_id, Rate::USD);
// Получить баланс EUR
$currency_account->get_balance_own_rate($user_id, Rate::EUR);

// Задание #3

// Пополнить баланс RUB(1000)
$currency_account->up_balance($user_id, Rate::RUB, 1000);
// Пополнить баласн EUR(50)
$currency_account->up_balance($user_id, Rate::EUR, 50);
// Пополнить баласн USD(10)
$currency_account->up_balance($user_id, Rate::USD, 10);

// Задание #4
// Устанавливаем новый курс валют
// EUR / RUB = 150
$currency_rate->set_exchange_rate(Rate::EUR, Rate::RUB, 150);
// USD / RUB = 100
$currency_rate->set_exchange_rate(Rate::USD, Rate::RUB, 100);

// Задание #5
// Получить баланс (в основной валюте)
$currency_account->get_balance_own_rate($user_id);

// Задание #6
// Установить основную валюту EUR
$currency_account->set_main_currency($user_id, Rate::EUR);
// Получить баланс (в выбранной валюте)
$currency_account->get_rate_balance($user_id, Rate::EUR);

// Задание #7
// Сконвертировать 1000 RUB в УГК
$currency_account->convert_currency($user_id, Rate::RUB, 1000, Rate::EUR);
// Получить баланс (в выбранной валюте)
$currency_account->get_rate_balance($user_id, Rate::EUR);

// Задание #8
// Банк устанавливает курс EUR / RUB = 120
$currency_rate->set_exchange_rate(Rate::EUR, Rate::RUB, 120);

// Задание #9
// Клиент проверяет, что баланс в EUR не изменился
$currency_account->get_rate_balance($user_id, Rate::EUR);

// Задание #10
// Устанавливаем основную валюту (RUB)
$currency_account->set_main_currency($user_id, Rate::RUB);
// Отключаем поддержку валюты EUR
$currency_account->remove_currency($user_id, Rate::EUR);
// Отключаем поддержку валюты USD
$currency_account->remove_currency($user_id, Rate::USD);
// Список поддерживаемых валют
$currency_account->get_supported_currency($user_id);
// Получить баланс (в выбранной валюте)
$currency_account->get_rate_balance($user_id, Rate::RUB);

echo '</p>';
echo '<pre>';
print_r($_SESSION);
echo '<pre>';
session_destroy();
