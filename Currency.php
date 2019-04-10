<?
//методы данного класса реализованны через хранение комбинаций 2х валют в формате ISO, 
//класс имеет возможность делать обратную конверсию через высчитанный искусственный обратный курс, 
//в классе можно сделать загрузку настоящего обратного курса, тоесть например конвертация валюты 2 в валюту 1



class Currency
{
    var $direction; //направление обмена в формате ISO валюты1 ISO валюты2
    var $directionBack; //направление обмена в обратном формате ISO валюты2 ISO валюты1
    var $rate; //курс
    var $rateBack; //обратный курс, высчитывается в конструкторе
    var $resultOfRequestToCash; //результат запроса из кеша
    var $resultOfRequestToBD; //результат запроса из BD
    var $resultOfRequestToXML; //результат запроса из XML
    public function convert($money) //конвертация валюты
    {
        return bcmul($this->rate, $money, 2); //умножение bcmul    
        //return     $this->rate*$money; // обычное умножение
    }
    
    public function convertBack($money) //обратная конвертация валюты
    {
        return bcmul($this->rateBack, $money, 2); //умножение bcmul        
        //return     $this->rateBack*$money; // обычное умножение
    }
    
    function __construct($cur1, $cur2)
    {
        $this->direction     = $cur1 . $cur2;
        $this->directionBack = $cur2 . $cur1;
        //функции получения или установки кеша (заглушки)
        function getCurrencyCash($direction)      {return 64;}
        function getCurrencyBD($direction)        {return 65;}
        function getCurrencyXML($direction)       {return 66;}
        function setCurrencyCash($direction, $value)   {}
        function setCurrencyBD($direction, $value)  {}
		
        $this->resultOfRequestToCash = getCurrencyCash($this->direction);
        if ($this->resultOfRequestToCash) {
            $this->rate = $this->resultOfRequestToCash;
            //    echo getCurrencyCash();
        } else {
            
            $this->resultOfRequestToBD = getCurrencyBD($this->direction);
            
            if ($this->resultOfRequestToBD) //если результат из БД получен
                {
                $this->rate = $this->resultOfRequestToBD;
                setCurrencyCash($this->direction, $this->rate); //устанавливаем значение кеша
            } else {
                $this->resultOfRequestToXML = getCurrencyXML($this->direction);
                $this->rate                 = $this->resultOfRequestToXML;
                setCurrencyCash($this->direction, $this->rate); //устанавливаем значение кеша
                setCurrencyBD($this->direction, $this->rate); //устанавливаем значение базы данных
                
            }
            
        }
        
        
        
        $this->rateBack = bcdiv(1, $this->rate, 6);
    }
}
//примеры работы
$currencyUsdRub = new Currency('usd', 'rub');
$total          = 20;
echo "Конвертируем $total usd в rub:\n";
echo $currencyUsdRub->convert($total);
$totalBack = 70;
echo "\nКонвертируем $totalBack rub в usd:\n";
echo $currencyUsdRub->convertBack($totalBack);
//или можем использовать так
$total = 10;
$rate  = $currencyUsdRub->rate;
echo "\nПример работы с полученным курсом через переменную rate, $total rub в usd:\n";
echo bcmul($total, $rate, 3);
//$money2=$currencyRuUs->getCurrency2();
?> 
