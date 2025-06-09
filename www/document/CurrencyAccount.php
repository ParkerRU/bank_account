<?php

namespace document;

// мультивалютный счет
class CurrencyAccount
{
    // Открыть новый счет
    public function create_account(string $user_id): void
    {
        if (isset($_SESSION['CurrencyAccount'][$user_id])) {
            print_r('Счет ' . $user_id . ' уже открыт !!!' . PHP_EOL);
            return;
        }
        $_SESSION['CurrencyAccount'][$user_id] = ['status' => 'OPEN'];
    }

    // Добавить валюту
    public function add_currency(string $user_id, array $rate): void
    {
        if (isset($_SESSION['CurrencyAccount'][$user_id]['currency'][$rate['name']])) {
            print_r('Для счета ' . $user_id . ' валюта ' . $rate['name'] . ' уже добавлена !!!' . PHP_EOL);
            return;
        }
        $_SESSION['CurrencyAccount'][$user_id]['currency'][$rate['name']] = [
            'value'     => 0,
            'is_own'    => 'false',
        ];
    }

    // удалить валюту
    public function remove_currency(string $user_id, array $rate): void
    {
        $own_currency = $this->get_own_currency($user_id); // получаем основную валюту
        $balance = $this->get_rate_balance($user_id, $rate); // получаем баланс по валюте закрытия
        $this->convert_currency($user_id, $rate, $balance, ['name' => $own_currency]); // конвертируем закрываемую валюту в основную
        unset($_SESSION['CurrencyAccount'][$user_id]['currency'][$rate['name']] ); // удаляем валюту закрытия
    }

    // проверить, что счет существует и открыт
    private function check_currency_account(string $user_id): void
    {
        if (!isset($_SESSION['CurrencyAccount'][$user_id]) ||
            $_SESSION['CurrencyAccount'][$user_id]['status'] != 'OPEN')
        {
            print_r('Счет пользователя ' . $user_id . ' не найден, либо был закрыт !!!' . PHP_EOL);
        }
    }

    // Установить основную валюту
    public function set_main_currency(string $user_id, array $rate): void
    {
        $this->check_currency_account($user_id);

        foreach ($_SESSION['CurrencyAccount'][$user_id]['currency'] as $key => $value) {
            if ($key == $rate['name']) {
                $_SESSION['CurrencyAccount'][$user_id]['currency'][$key]['is_own'] = 'true';
            } else {
                $_SESSION['CurrencyAccount'][$user_id]['currency'][$key]['is_own'] = 'false';
            }
        }
    }

    // Получить список поддерживаемых валют
    public function get_supported_currency(string $user_id): void
    {
        $supported_currency = [];
        foreach ($_SESSION['CurrencyAccount'][$user_id]['currency'] as $key => $value) {
            $supported_currency[] = $key;
        }

        $_SESSION['CurrencyAccount'][$user_id]['supported_currency'] = $supported_currency;
    }

    // Проверить, что выбранная валюта добавлена к счету
    private function check_currency_was_added(string $user_id, array $rate): void
    {
        $this->check_currency_account($user_id);
        if (!isset($_SESSION['CurrencyAccount'][$user_id]['currency'][$rate['name']])) {
            print_r('Для пользователя ' . $user_id . ' валюта ' . $rate['name'] . ' не добавлена' . PHP_EOL);
        }
    }

    // Пополнить баланс
    public function up_balance(string $user_id, array $rate, float $value): void
    {
        $this->check_currency_was_added($user_id, $rate);
        $_SESSION['CurrencyAccount'][$user_id]['currency'][$rate['name']]['value'] += $value;
    }

    // Списать с баланса
    public function down_balance(string $user_id, array $rate, float $value): void
    {
        $this->check_currency_was_added($user_id, $rate);
        $balance = $_SESSION['CurrencyAccount'][$user_id]['currency'][$rate['name']]['value'];

        if ($balance >= $value) {
            $_SESSION['CurrencyAccount'][$user_id]['currency'][$rate['name']]['value'] -= $value;
        } else {
            print_r('Нельзя списать ' . $value . $rate['name'] . PHP_EOL);
            print_r('Ваш баланс составляет ' . $balance . $rate['name'] . PHP_EOL);
        }
    }

    // Получить сконвертированный баланс по валюте
    private function get_balance(string $rate1, float $value, string $rate2): float
    {
        $balance = 0;
        $currency_pair = $rate1 . '/' . $rate2;
        $currency_pair_rev = $rate2 . '/' . $rate1;

        if (isset($_SESSION['CurrencyRates'][$currency_pair])) {
            $balance = $value * $_SESSION['CurrencyRates'][$currency_pair];
        }

        if (isset($_SESSION['CurrencyRates'][$currency_pair_rev])) {
            $balance = $value / $_SESSION['CurrencyRates'][$currency_pair_rev];
        }

        return $balance;
    }

    // получить основную валюту
    private function get_own_currency(string $user_id): string
    {
        $own_currency = '';
        foreach ($_SESSION['CurrencyAccount'][$user_id]['currency'] as $key => $value) {
            if ($value['is_own'] == 'true') {
                $own_currency = $key;
            }
        }

        print_r('Основная валюта - ' . $own_currency . PHP_EOL);
        return $own_currency;
    }

    // Получить суммарный баланс в основной валюте
    public function get_balance_own_rate(string $user_id, array $rate = []): void
    {
        $balance = 0;

        if ($rate == []) {
            $own_currency = $this->get_own_currency($user_id);
        } else {
            $own_currency = $rate['name'];
        }

        foreach ($_SESSION['CurrencyAccount'][$user_id]['currency'] as $key => $value) {
            if ($own_currency == $key) {
                $balance += $value['value'];
            } else {
                $balance += $this->get_balance($key, $value['value'], $own_currency);
            }
        }

        print_r('Ваш баланс - '. $balance . $own_currency . PHP_EOL);
    }

    // получить баланс по конкретной валюте
    public function get_rate_balance(string $user_id, array $rate): mixed
    {
        $balance = $_SESSION['CurrencyAccount'][$user_id]['currency'][$rate['name']]['value'];
        print_r('Баланс по выбранной валюте - ' . $balance . $rate['name'] . PHP_EOL);

        return $balance;
    }

    // сконвертировать одну валюту в другую
    public function convert_currency(string $user_id, array $rate1, float $value, array $rate2): void
    {
        $this->down_balance($user_id, $rate1, $value);
        $balance = $this->get_balance($rate1['name'], $value, $rate2['name']);
        $this->up_balance($user_id, $rate2, $balance);

        print_r('Сконвертировано ' . $value . $rate1['name'] . ' в '. $balance . $rate2['name'] . PHP_EOL);
    }
}
