<?php
require_once 'PromotionCheck.php';

if (!empty($_POST))
{
try                            {
    $pdo = new PDO('mysql:host=localhost;dbname=qrooto', 'admin', 'admin',array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                               } 
catch (PDOException $e)        {
echo 'Невозможно установить соединение с базой данных';
                               }


$ar = json_decode($_POST['name'], true);
$checkid = $ar['id'];
$checkid = (int) $checkid;
$checkdate = str_replace('T', ' ',$ar['receiveDate']);
$checkdate = str_replace('Z', '',$checkdate);
$goodsarray = $ar['content']['items'];


function calculationCashback($promotion_id, $check_id, $name, $price, $amount,$pdo)
{
$cashback_amount_type0  = 'SELECT cashback_amount_type,cashback_amount FROM promotion WHERE id = :promotion_id LIMIT 1';
$cashback_amount_type1 = $pdo->prepare($cashback_amount_type0);
$cashback_amount_type1->execute(array(':promotion_id' => $promotion_id));
$cashback_amount_type1->bindcolumn(1,$cashback_amount_type);
$cashback_amount_type1->bindcolumn(2,$cashback_amount);
$cashback_amount_type1->fetch(PDO::FETCH_BOUND);
$cashback_amount = (double) $cashback_amount;
$given_cashback = 0;
//если тип кешбека = 1, начисляется процент от стоимости всего количества товара, если тип 2 - начисляется фикcированная сумма по каждой единице товара
switch($cashback_amount_type)
{
case 1:
$given_cashback = $price * $amount * $cashback_amount;
break;
case 2:
$given_cashback = $cashback_amount * $amount;
break;
}
$given_cashback = round($given_cashback,2);
$final_price = $price * $amount;
$final_price = round($final_price,2);
$calculationcashback = 'INSERT INTO given_cashback (promotion_id, check_id,goods_name, price,cashback,date) VALUES (:promotion_id,:check_id,:goods_name,:price,:cashback,now())';
$calculationcashback1 = $pdo->prepare($calculationcashback);
$calculationcashback1->execute(array(':promotion_id' => $promotion_id,':check_id' => $check_id,':goods_name' => $name,':price' => $final_price,':cashback' => $given_cashback));
echo "Чек $check_id, товар \"$name\" (кол-во $amount), общая стоимость $final_price, начисленный кешбек $given_cashback".'<br>';
}  

$promotion_array = 'SELECT id FROM promotion WHERE date_from <= :checkdate and date_to >= :checkdate';
$promotion_array1 = $pdo->prepare($promotion_array);
$promotion_array1->execute(array(':checkdate' => $checkdate));
$promotion_array2 = $promotion_array1->fetchAll(PDO::FETCH_COLUMN);

//$reg = '/\b(хлеб)\b(?!\.)/iu';
//$reg1 = '/\b(творог|творожок)\b(?!\.)/iu';
//$reg3 = '/\b(агуша)\b(?!\.)/iu';

foreach($promotion_array2 as $promotion_id)
{
 foreach($goodsarray as $goodsname)
 {
$promotion_key_words_array = 'SELECT key_word_regular_expression FROM promotion_key_words where promotion_id = :promotion_id';
$promotion_key_words_array1 = $pdo->prepare($promotion_key_words_array);
$promotion_key_words_array1->execute(array(':promotion_id' => $promotion_id));
$promotion_key_words_array2 = $promotion_key_words_array1->fetchAll(PDO::FETCH_COLUMN);
$takes_part_in_promotion = '';
    foreach($promotion_key_words_array2 as $key_words)
     {
     if(preg_match($key_words, $goodsname['name'])){$takes_part_in_promotion = true;}
     else
     {$takes_part_in_promotion = false;
     break;} 
     }

if($takes_part_in_promotion)
{
$is_single_in_check = 'SELECT is_single_in_check FROM promotion where id = :promotion_id LIMIT 1';
$is_single_in_check1 = $pdo->prepare($is_single_in_check);
$is_single_in_check1->execute(array(':promotion_id' => $promotion_id));
$is_single_in_check2 = $is_single_in_check1->fetchAll(PDO::FETCH_COLUMN);
$is_single_in_check2 = $is_single_in_check2[0];

if($is_single_in_check2)
  {
  calculationCashback($promotion_id, $checkid, $goodsname['name'], $goodsname['price']*0.01, 1,$pdo);
  break;
  }
else
  {
  calculationCashback($promotion_id, $checkid, $goodsname['name'], $goodsname['price']*0.01, $goodsname['quantity'],$pdo);
  }
}
 }
}
}
else
{echo 'пусто';}
