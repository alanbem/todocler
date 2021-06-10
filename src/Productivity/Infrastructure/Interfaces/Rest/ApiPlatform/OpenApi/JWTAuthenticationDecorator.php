<?php

/**
 * This file is part of the todocler package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\OpenApi;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see   https://api-platform.com/docs/core/jwt/
 *
 * @codeCoverageIgnore
 *
 * @noRector \Rector\Privatization\Rector\Class_\RepeatedLiteralToClassConstantRector
 */
final class JWTAuthenticationDecorator implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;
    private string $path;

    public function __construct(OpenApiFactoryInterface $decorated, $path)
    {
        $this->decorated = $decorated;
        $this->path = $path;
    }

    public function __invoke(array $context = []) : OpenApi
    {
        $api = $this->decorated->__invoke($context);
        $schemas = $api->getComponents()->getSchemas();

        if (null === $schemas) {
            return $api;
        }

        $schemas['Token'] = new \ArrayObject(
            [
                'type' => 'object',
                'properties' => [
                    'token' => [
                        'type' => 'string',
                        'readOnly' => true,
                    ],
                ],
            ]
        );
        $schemas['Credentials'] = new \ArrayObject(
            [
                'type' => 'object',
                'properties' => [
                    'email' => [
                        'type' => 'string',
                        'example' => 'adam@example.com',
                    ],
                    'password' => [
                        'type' => 'string',
                        'example' => 'password',
                    ],
                ],
            ]
        );

        $item = new Model\PathItem(
            'JWT Token',
            '',
            '',
            null,
            null,
            new Model\Operation(
                'postCredentialsItem',
                ['Token'],
                [
                    '200' => [
                        'description' => 'Get JWT token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token',
                                ],
                            ],
                        ],
                    ],
                ],
                'Get JWT token to login.',
                '',
                null,
                [],
                new Model\RequestBody(
                    'Generate new JWT Token',
                    new \ArrayObject(
                        [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Credentials',
                                ],
                            ],
                        ]
                    ),
                ),
            ),
        );

        $api->getPaths()->addPath($this->path, $item);

        return $api;
    }
}
