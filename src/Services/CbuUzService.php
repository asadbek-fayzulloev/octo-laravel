<?php
namespace Asadbek\OctoLaravel\Services;

/**
 * Клиент для получения курсов валют с сайта ЦБ Узбекистана.
 */
class CbuUzService
{
    /**
     * Адрес сервера.
     *
     * @var string
     */
    protected $url = 'http://www.cbu.uz/ru/arkhiv-kursov-valyut/xml/';

    /**
     * Сообщение об ошибке.
     *
     * @var string|null
     */
    protected $error = null;

    /**
     * Получает все валюты на текущую дату.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->loadData($this->url);
    }

    /**
     * Получает все валюты на указанную дату.
     *
     * @param string $date
     * @return array
     */
    public function getAllByDate($date)
    {
        return $this->loadData($this->url.'all/'.date('Y-m-d', strtotime($date)).'/');
    }

    /**
     * Получает одну валюту на указанную дату.
     *
     * @param string $currency
     * @param string $date
     * @return array
     */
    public function getOneByDate($currency, $date)
    {
        $response = $this->loadData($this->url.$currency.'/'.date('Y-m-d', strtotime($date)).'/');

        if ($response) {
            $response = $response[0];
        }

        return $response;
    }


    /**
     * Возвращает сообщение об ошибке.
     *
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Загружает данные с сервера.
     *
     * @return array|bool
     */
    protected function loadData($url)
    {
        $data = @file_get_contents($url);

        if (!$data) {
            $this->error = 'Http error';
            return false;
        }

        return $this->transformResponse($data);
    }

    /**
     * Конвертирует ответ с сервера в массив.
     *
     * @return array|bool
     */
    protected function transformResponse($resonse)
    {
        if (!$resonse = @simplexml_load_string($resonse)) {
            $this->error = 'Xml parsing error';
            return false;
        }

        $currencies = [];

        foreach ($resonse->CcyNtry as $key => $value) {
             $currencies[] = [
                'id' => (string) $value->attributes()['ID'],
                'code' => $value->Ccy->__toString(),
                'name_ru' => $value->CcyNm_RU->__toString(),
                'name_uz' => $value->CcyNm_UZ->__toString(),
                'name_uz_cyr' => $value->CcyNm_UZC->__toString(),
                'name_en' => $value->CcyNm_EN->__toString(),
                'decimal_places' => $value->CcyMnrUnts->__toString(),
                'nominal' => $value->Nominal->__toString(),
                'rate' => $value->Rate->__toString(),
                'date' => $value->date->__toString(),
            ];
        }

        return $currencies;
    }
}
