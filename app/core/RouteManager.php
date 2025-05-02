<?php
namespace Core;
class RouteManager
{
    public static function cachePath(): string
    {
        return dirname(__DIR__, 2) . '/storage/cache/routes.cache.php';
    }

    public static function webPath(): string
    {
        return dirname(__DIR__, 2) . '/routing/web.php';
    }

    public static function load(): void
    {
        $path = self::cachePath();
        $webPath = self::webPath();

        if (!file_exists($path)) {
            self::rebuild();
            return;
        }

        $data = include $path;

        if (
            !is_array($data) ||
            !isset($data['routes'], $data['named'], $data['timestamp']) ||
            $data['timestamp'] !== filemtime($webPath)
        ) {
            self::rebuild();
            return;
        }

        Route::setRoutes($data['routes'], $data['named']);
    }

    public static function rebuild(): void
    {
        require self::webPath();
        $namedRoutesProperty = new \ReflectionProperty(Route::class, 'namedRoutes');
       
        $data = [
            'routes' => Route::all(),
            'named' => $namedRoutesProperty->getValue(), 
            'timestamp' => filemtime(self::webPath())
        ];
        file_put_contents(self::cachePath(), '<?php return ' . var_export($data, true) . ';');
    }

    public static function clear(): void
    {
        $path = self::cachePath();
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
