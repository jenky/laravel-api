<?php

namespace Jenky\LaravelAPI\Http\VersionParser;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Http\Request;
use Jenky\LaravelAPI\Contracts\Http\VersionParser;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Header implements VersionParser
{
    /**
     * The config repository.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * The header name that contains version to parse.
     *
     * @var string
     */
    protected static $header = 'Accept';

    /**
     * Create new header version parser.
     *
     * @param  \Illuminate\Contracts\Config\Repository $config
     * @return void
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Set the header to use.
     *
     * @param  string $header
     * @return void
     */
    public static function using(string $header)
    {
        static::$header = $header;
    }

    /**
     * Parse the request and get the API version.
     *
     * @param  \Illuminate\Http\Request $request
     * @return string|null
     */
    public function parse(Request $request): ?string
    {
        $parsed = $this->praseHeader($request, $this->config->get('api.strict'));

        return $parsed['version'] ?? null;
    }

    /**
     * Parse the header.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  bool $strict
     * @return array
     */
    protected function praseHeader(Request $request, $strict = false)
    {
        $standardsTree = $this->config->get('api.standards_tree');
        $subtype = $this->config->get('api.subtype');
        $pattern = '/application\/'.$standardsTree.'\.('.$subtype.')\.([\w\d\.\-]+)\+([\w]+)/';

        if (! preg_match($pattern, $request->header(static::$header), $matches)) {
            if ($strict) {
                throw new BadRequestHttpException(static::$header.' header could not be properly parsed because of a strict matching process.');
            }

            $version = $this->config->get('api.version');
            $format = $this->config->get('api.format', 'json');
            $default = sprintf('application/%s.%s.%s+%s', $standardsTree, $subtype, $version, $format);

            preg_match($pattern, $default, $matches);
        }

        return array_combine(
            ['subtype', 'version', 'format'], array_slice($matches, 1)
        );
    }
}
