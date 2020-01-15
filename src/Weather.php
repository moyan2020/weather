<?php
namespace Moyanshe\Weather;
use GuzzleHttp\Client;
use Moyanshe\Weather\Exceptions\HttpException;
use Moyanshe\Weather\Exceptions\InvalidArgumentException;
/**
 * 天气查询类 v1.2
 * Class Weather
 * @package Moyan\Weather
 */
class Weather
{
    protected $key;
    protected $guzzleOptions = [];
    public function __construct(string $key)
    {
        $this->key = $key;
    }
    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }
    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }


    public function getLiveWeather($city, $format = 'json')
    {
        return $this->getWeather($city, 'base', $format);
    }

    public function getForecastsWeather($city, $format = 'json')
    {
        return $this->getWeather($city, 'all', $format);
    }

    /**
     * @param $city
     * @param string $type
     * @param string $format
     * @return mixed|string
     * @throws InvalidArgumentException | HttpException
     */
    public function getWeather($city, string $type = 'base', string $format = 'json')
    {
        $url = 'https://restapi.amap.com/v3/weather/weatherInfo';
        if (!in_array(strtolower($format), ['xml', 'json'])) {
            throw new InvalidArgumentException('Invalid response format: ' . $format);
        }
        if (!in_array(strtolower($type), ['base', 'all'])) {
            throw new InvalidArgumentException('Invalid type value(base/all): ' . $type);
        }
        $query = array_filter([
            'key' => $this->key,
            'city' => $city,
            'output' => $format,
            'extensions' => $type,
        ]);
        try {
            $response = $this->getHttpClient()->get($url, [
                'query' => $query,
            ])->getBody()->getContents();
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
        return 'json' === $format ? \json_decode($response, true) : $response;
    }
}