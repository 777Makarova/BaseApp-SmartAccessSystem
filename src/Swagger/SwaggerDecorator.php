<?php

namespace App\Swagger;

use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SwaggerDecorator implements NormalizerInterface
{

    public function __construct(private NormalizerInterface $decorated)
    {
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param mixed $object  Object to normalize
     * @param string | null  $format  Format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     *
     * @return array|string|int|float|bool
     *
     * @throws ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $docs = $this->decorated->normalize($object, $format, $context);

        $this->addDescription($docs);

        return $docs;
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data   Data to normalize
     * @param string | null  $format The format being (de-)serialized from or into
     *
     * @return bool
     */
    public function supportsNormalization($data, string $format = null):bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    private function addDescription(array $docs)
    {
        $config = [
            'Client-Item' => [
                'description' => 'test',
            ],
        ];
        if (isset($docs['definitions'])) {
            foreach ($docs['definitions'] as $name => $definition) {
                if (isset($config[$name])) {
                    $definition['description'] = $config[$name]['description'];
                }
            }
        }
    }
}
