<?php

namespace ADAL;

trait TFromJson
{
	/**
     * @param string|array $json
     * @return $this
     */
    public static function deserialize($json)
    {
        $className = get_called_class();
        $classInstance = new $className();
        if (is_string($json)) {
            $json = json_decode($json);
        }

        foreach ($json as $key => $value) {
            if (!property_exists($classInstance, $key)) continue;

            $classInstance->{$key} = $value;
        }

        return $classInstance;
    }
    /**
     * @param string $json
     * @return $this[]
     */
    public static function deserializeArray($json)
    {
        $json = json_decode($json);
        $items = [];
        foreach ($json as $item) {
            $items[] = self::deserialize($item);
        }
        return $items;
    }
}