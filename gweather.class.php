<?php

class gWeather {

    private $options = array(); // array values: url_get, language, encoding
    public $information;
    public $current;
    public $forecast_list;

    public function __construct($options = false) {

        if (empty($options)) {
            $this->options = array(
                'url_get' => 'http://www.google.com/ig/api?weather=',
                'language' => 'pt-br',
                'encoding' => 'utf8'
            );
        }
    }

    public function getData($params) {

        if (!empty($params['cidade']) AND !empty($params['pais'])) {
            $url = $this->options['url_get'] . urlencode(trim($params['cidade'])) . ',' . trim($params['pais']) . '&hl=' . $this->options['language'] . '&oe=' . $this->options['encoding'];
        } else if (!empty($params['latitude']) AND !empty($params['longitude'])) {
            $url = $this->options['url_get'] . ',,,' . urlencode(trim($params['latitude'])) . ',' . trim($params['longitude']) . '&hl=' . $this->options['language'] . '&oe=' . $this->options['encoding'];
        }

        $xml = simplexml_load_file($url);

        if ($xml) {

            $this->setForecast($xml->xpath('/xml_api_reply/weather/forecast_conditions'));
            $this->setInformation($xml->xpath('/xml_api_reply/weather/forecast_information'));
            $this->setCurrent($xml->xpath('/xml_api_reply/weather/current_conditions'));
        } else {
            // TODO: retornar erro..
        }


        return $xml;
    }

    private function setInformation($data) {
        $this->information = $data;
    }

    public function getInformation() {
        return $this->information;
    }

    private function setCurrent($data) {
        $this->current = $data;
    }

    public function getCurrent() {
        return $this->current;
    }

    private function setForecast($data) {
        $this->forecast_list = $data;
    }

    public function getForecast() {
        return $this->forecast_list;
    }

    public function getHtmlExample() {

        $information = $this->getInformation();
        $current = $this->getCurrent();
        $forecast_list = $this->getForecast();

        $html = '
        <div class="weather">
            <img src="http://www.google.com' . $current[0]->icon['data'] . '" alt="weather" />
            <span class="condition">
 ' . $current[0]->temp_c['data'] . ' &deg;,' .
                $current[0]->condition['data'] . '
            </span>
        </div>
        <h3>Previsão</h3>';

        foreach ($forecast_list as $forecast) {
            $html .='<div class="weather">
                <img src="http://www.google.com' . $forecast->icon['data'] . '" alt="weather" />
                        <span class="condition">' .
                    $forecast->day_of_week['data'] . ' ' . $forecast->low['data'] . '&deg; - ' . $forecast->high['data'] . '&deg;,
                ' . $forecast->condition['data'] . '
                </span>
            </div>';
        }

        return $html;
    }

}

?>
