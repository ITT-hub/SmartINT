<?php
/*
 * Created 21.12.2019 11:55
 */

namespace ITTech\SmartINT;

/**
 * Class ParseFile
 * @package ITTech\SmartINT
 * @author Alexandr Pokatskiy
 * @copyright ITTechnology
 */
class ParseFile
{
    /**
     * Обработанный массив
     * @var array
     */
    private $dataFiel = null;

    /**
     * XML файл
     * @var null | string
     */
    private $xmlFile = null;

    /**
     * ParseFile constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->dataFiel = $data;
    }

    /**
     * Инициализация парсера
     * @param null $file
     * @return ParseFile
     */
    public static function init($file = null)
    {
        $dataFile = file(__DIR__."/".$file);
        $data     = array_slice($dataFile, 19, -1);

        mb_convert_variables("utf-8", "windows-1251", $data);

        $chunk = array_chunk($data, 36);
        $parse = [];

        foreach ($chunk as $item)
        {
            $ex1 = explode("=", $item[4]);
            $ex2 = explode("=", $item[2]);
            $ex3 = explode("=", $item[1]);
            $ex4 = explode("=", $item[3]);

            array_push($parse, ["N_LS" => $ex1[1], "DATE" => $ex2[1], "TRN" => $ex3[1], "SUMMA"=> $ex4[1]]);
        }

        return new self($parse);
    }

    /**
     * Формирование XML документа
     * @param null $xmlPath
     * @return $this|bool
     */
    public function xml($xmlPath = null)
    {
        $this->xmlFile = $xmlPath."/".md5(time()).".xml";
        $dom           = new \domDocument("1.0", "utf-8");
        $root          = $dom->appendChild($dom->createElement("EXPORT_TRN"));

        for ($i=0; $i<count($this->dataFiel); $i++)
        {
            $client = $root->appendChild($dom->createElement("CLIENT")); // Создать элемент CLIENT

            $client->appendChild($dom->createElement("N_LS", $this->dataFiel[$i]["N_LS"]));
            $client->appendChild($dom->createElement("DATE", $this->dataFiel[$i]["DATE"]));
            $client->appendChild($dom->createElement("TRN", $this->dataFiel[$i]["TRN"]));
            $client->appendChild($dom->createElement("SUMMA", $this->dataFiel[$i]["SUMMA"]));
        }

        if($dom->save($this->xmlFile))
        {
            return $this;
        }

        return false;
    }

    /**
     * Отправка файла на сервер
     * @param $url
     * @return bool|string
     */
    public function set($url)
    {
        $ch   = curl_init($url);
        $post = array('file' => new \CURLFile($this->xmlFile));

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $html = curl_exec($ch);
        curl_close($ch);

        return $html;
    }
}
