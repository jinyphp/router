<?php

namespace Jiny\Router;

if (!function_exists('FastRoute\simpleDispatcher')) {
    /**
     * 라우트를 정의합니다.
     * @param callable $routeDefinitionCallback
     * @param array $options
     *
     * @return Dispatcher
     */
    function simpleDispatcher(callable $routeDefinitionCallback, array $options = [])
    {
        // 배열을 추가합니다.
        $options += [
            'routeParser' => 'FastRoute\\RouteParser\\Std',
            'dataGenerator' => 'FastRoute\\DataGenerator\\GroupCountBased',
            'dispatcher' => 'FastRoute\\Dispatcher\\GroupCountBased',
            'routeCollector' => 'FastRoute\\RouteCollector',
        ];

        /** @var RouteCollector $routeCollector */
        // RouterParser
        // 라우트를 정의하는 매서드들.
        $routeCollector = new $options['routeCollector'](
            new $options['routeParser'], new $options['dataGenerator']
        );

        // 라우트를 정의작업
        // 콜백으로 처리합니다.
        $routeDefinitionCallback($routeCollector);

        return new $options['dispatcher']($routeCollector->getData());
    }

    /**
     * @param callable $routeDefinitionCallback
     * @param array $options
     *
     * @return Dispatcher
     */
    function cachedDispatcher(callable $routeDefinitionCallback, array $options = [])
    {
        $options += [
            'routeParser' => 'FastRoute\\RouteParser\\Std',
            'dataGenerator' => 'FastRoute\\DataGenerator\\GroupCountBased',
            'dispatcher' => 'FastRoute\\Dispatcher\\GroupCountBased',
            'routeCollector' => 'FastRoute\\RouteCollector',
            'cacheDisabled' => false,
        ];

        if (!isset($options['cacheFile'])) {
            throw new \LogicException('Must specify "cacheFile" option');
        }

        if (!$options['cacheDisabled'] && file_exists($options['cacheFile'])) {
            $dispatchData = require $options['cacheFile'];
            if (!is_array($dispatchData)) {
                throw new \RuntimeException('Invalid cache file "' . $options['cacheFile'] . '"');
            }
            return new $options['dispatcher']($dispatchData);
        }

        $routeCollector = new $options['routeCollector'](
            new $options['routeParser'], new $options['dataGenerator']
        );
        $routeDefinitionCallback($routeCollector);

        /** @var RouteCollector $routeCollector */
        $dispatchData = $routeCollector->getData();
        if (!$options['cacheDisabled']) {
            file_put_contents(
                $options['cacheFile'],
                '<?php return ' . var_export($dispatchData, true) . ';'
            );
        }

        return new $options['dispatcher']($dispatchData);
    }
}
