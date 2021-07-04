<?php
/*
try                            {
    $pdo = new PDO('mysql:host=localhost;dbname=qrooto', 'admin', 'admin');
                               } 
catch (PDOException $e)        {
echo 'Невозможно установить соединение с базой данных';
                               }
 */                              
class PromotionCheck
{

public $checkid;
public $checkdate;
public $goodsarray;
public function __construct($checkid,$checkdate,array $goodsarray, PDO $pdo)
{
$this->checkid = $checkid;
$this->checkdate = $checkdate;
$this->goodsarray = $goodsarray;
$this->pdo = $pdo;
}



//$is_promotion_member = 1;

                               
     /*                          

public $select = 'SELECT id FROM promotion WHERE date_from <= ? and date_to >= ?';


public $read = $pdo->prepare($select);

$read->execute([$checkdate]);
$read1 = $read->fetchAll();
print_r($read1);
                            

public function calculateCashback()
foreach()
{
 if( == 0)
 {
 $is_promotion_member = 0;
 }
 else
 {
 
 }
}
*/
}
