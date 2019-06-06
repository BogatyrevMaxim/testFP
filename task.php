<?php

require_once 'ResultDto.php';

function parseYandexSms($text)
{
    $purse = null;
    $code = null;
    $sum = null;

    // Из документации https://kassa.yandex.ru/tech/payout/wallet.html
    // Номер кошелька пользователя в Яндекс.Деньгах, например 4100175017397. Длина — от 11 до 20 цифр
    // Но в перспективе со временем "максимально возможная длина счета 26 цифр"
    // Так как пока что только рубли, то
    // (1|3) - 41001 - рубли, 41003 - демо рубли
    if (preg_match('/[^\d](?P<purse>4100(1|3)\d{6,21})/ui', $text, $matches)) {
        $purse = $matches['purse'];
        $text = str_replace($purse, '', $text);
    }

    // Максимальная сумма перевода не оговорена, потому помечтаем, что она может быть хорошей :)
    // Сумма может быть без копеек, разделитель может быть , или .
    // после суммы часто идет слово или символ валюты, поэтому проверяем наличие букв после суммы без пробела и с пробелом
    // \p{Sc} - символ валюты - можно убрать, так как в смс он пока что маловероятен.
    // И так как платежи пока что только в рублях, то можно жестко вбить проверку символа рубля или слова рубля или буквы "р"
    if (preg_match('/\s(?P<sum>\d{1,9}([,\.]\d{2})?)[[:blank:]]?[[:alpha:]\p{Sc}]/imuU', $text, $matches)) {
        $sum = $matches['sum'];
        $text = str_replace($sum, '', $text);
        $sum = (float)str_replace(',', '.', $sum);
    }

    if (preg_match('/(?P<code>\d{4,5})\W/imuU', $text, $matches)) {
        $code = $matches['code'];
        // сейчас лишнее, но потом может кто то добавит что то ниже,
        // и не заметит этого, и остатки кода могут помешать
        $text = str_replace($code, '', $text);
    }

    return new ResultDto($purse, $code, $sum);
}

/**
 * Вывод для консоли
 * @param string $text
 * @param ResultDto $resultDto
 */
function render(string $text, ResultDto $resultDto) {
    echo 'Текст смс', PHP_EOL, PHP_EOL;
    echo $text, PHP_EOL, PHP_EOL;
    echo sprintf('Code: %s, Sum: %.2f, Purse: %d', $resultDto->getCode(), $resultDto->getSum(), $resultDto->getPurse());
    echo PHP_EOL, '----------------------', PHP_EOL;
}

$text = 'Пароль: 8192
Спишется 123,62р.
Перевод на счет 4100175017397';

render($text, parseYandexSms($text));

$text = 'Пароль: 2688
Спишется 0,21р.
Перевод на счет 410017534347355';
render($text, parseYandexSms($text));

$text = 'Пароль: 5425
Спишется 1р.
Перевод на счет 4100175017355';

render($text, parseYandexSms($text));

// сумма как код подтврждения
$text = 'Пароль: 3273
Спишется 1000р.
Перевод на счет 4100175017355';
render($text, parseYandexSms($text));

$text = 'Пароль: 5476. Спишется 1000 рублей.
Перевод на счет 4100175017311';

render($text, parseYandexSms($text));

$text = 'Спишется 1000.55 рублей. Пароль: 5476, 
Перевод на счет 4100175017311. не подтверждайте...';

render($text, parseYandexSms($text));

$text = 'Пароль: 8192
Спишется 123.62 руб.
Перевод на счет 4100175017397';

render($text, parseYandexSms($text));

$text = 'Никому не говорите пароль! Его спрашивают только мошенники.
Пароль: 28565
Перевод на счет 410017534347355
Вы потратите 9045,23р.';

render($text, parseYandexSms($text));

$text = 'Никому не говорите пароль! Его спрашивают только мошенники.
Пароль: 48336
Перевод на счет 410017534347355
Вы потратите 6000р.';

render($text, parseYandexSms($text));

/*
 * Далее балавство со знаками валют
 */

// тест знака $ - что маловероятно, так как
$text = 'Пароль: 3273
Спишется 20 $.
Перевод на счет 4100175017355';
render($text, parseYandexSms($text));

// тест знака рубля ₽
$text = 'Пароль: 3273
Спишется 205,55₽.
Перевод на счет 4100175017355';
render($text, parseYandexSms($text));