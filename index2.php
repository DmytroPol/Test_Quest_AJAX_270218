<?php

/**
 * Предистория:
 * Хотел сделать качество по максимуму с учетом дедлайна, время работы над заданием:21,54 - 00,48 (3 часа)
 * ==================================================
 * Весь бэк построил так:
 * 1) Данных приходят на сервер через AJAX запрос
 * 2) Скрипт обрабатывает его, и мы получаем нужное пользователю действие
 * 3) Используя функцию doTable мы создаем таблицу, и возвращаем её код
 * 4) стандартный echo ответ
 *
 * ==================================================
 */


/**
 * @param $var
 * ======================================
 * Ф-я для отладки кода
 * ======================================
 */
function d($var)
{
    echo "<pre>";
    print_r($var);
    die;
}

/**
 *====================================================
 * Ф-я для построения таблицы
 * ====================================================
 * @param array $columnName ==> ручками пишем нужное название столбцов таблицы
 * @param array $dataArray ==> данные для столбцов (будут доставатся с SQL запроса)
 * @return string
 */
function doTable(array $columnName,array $dataArray)
{
    $table="<table class='table table-hover table_response' border='2'><thead><tr>";
    // шапка для результирующей таблицы
    foreach ($columnName as $th)
      {
          $table.="<th>{$th}</th>";
      }
      $table.="</tr></thead><tbody>";

    // тело таблицы
    foreach ($dataArray as $items )
    {
        $table.="<tr>";
        foreach ($items as $item)
        {
            $table.="<td>{$item}</td>";
        }
        $table.="</tr>";
    }

    return $table.=" </tbody></table>";
}

//1) подключаем БД + после у меня полезли ????, пришлось на месте прибить кодировку
$db=new mysqli('localhost','root','','yii2basic');
$db->set_charset('utf8');

// 2) готовим сам запрос и переменную $result для ответа
$request=$_POST['type'];
$result='';
// Перечень кейсов:
// 1) Вывести таблицу с товарами
// 2) Вывести № заказа, название товара
// 3) Номер заказа, имя товара, цена, количество, имя оператора который
// за которым числится заказ ГДЕ количество товара >2 И id оператора 10 ИЛИ 12
// 4) Имя товара, количество товара, и сумма (price) по каждому товару (сгруппировать)
switch ($request)
{
    case 1:
        $products=$db->query("SELECT * FROM `offers`")->fetch_all(MYSQLI_ASSOC);
        $result=doTable(['ID товара','Имя товара'],$products);
        break;
    case 2:
        // ID заказа с инпута к заданию
        $orderId=$_POST['order'];
        $order=$db->query('SELECT requests.id, offers.name
        FROM requests
        LEFT JOIN offers
        ON requests.offer_id=offers.id
        WHERE requests.id='.$orderId)->fetch_all(MYSQLI_ASSOC);
        $result=(!is_null($order) && !empty($order))?doTable(['№','Название товара'],$order):"<h1>Заказ №{$orderId} не найден </h1>";
        break;
    case 3:
        $orderList=$db->query("SELECT requests.id, offers.name,requests.price,requests.count,operators.fio
        FROM requests
        LEFT JOIN offers
        ON requests.offer_id=offers.id
        LEFT JOIN operators
        ON requests.operator_id=operators.id
        WHERE requests.count>2 AND (requests.operator_id=10 OR requests.operator_id=12);")->fetch_all(MYSQLI_ASSOC);
        $result=doTable(['№ заказа','Имя товара','Цена товара','Стоимость товара','ФИО менеджера'],$orderList);
        break;
    case 4:
        $itemOffers=$db->query("
        SELECT  offers.name AS товар, SUM(requests.price*requests.count) AS 'общая сумма по товару'
        FROM requests
        LEFT JOIN offers
        ON requests.offer_id=offers.id
        GROUP BY offers.name;")->fetch_all(MYSQLI_ASSOC);
        $result=doTable(['Наименование товара','Общая сумма по товару'],$itemOffers);
}

echo $result;






























?>