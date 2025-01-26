<?php

declare(strict_types=1);

namespace App\Tests\Mapper;

use App\Mapper\DataMapper;
use App\Model\Address;
use App\Model\Resource;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class DataMapperTest extends TestCase
{
    private MockObject $serializerMock;

    private MockObject $denormalizerMock;

    private MockObject $normalizerMock;

    private DataMapper $mapper;

    public function setUp(): void
    {
        $this->serializerMock = $this->createMock(SerializerInterface::class);
        $this->denormalizerMock = $this->createMock(DenormalizerInterface::class);
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);
        $this->mapper = new DataMapper($this->serializerMock, $this->denormalizerMock, $this->normalizerMock);
    }

    public function testDeserializeWithGoodJson(): void
    {
        $json = '{"page": 1, "items": []}';

        $this->serializerMock
            ->method('deserialize')
            ->with($json, Resource::class, 'json')
            ->willReturn(new Resource())
        ;

        $result = $this->mapper->deserialize($json, Resource::class, 'json');

        $this->assertInstanceOf(Resource::class, $result);
        $this->assertObjectHasProperty('items', $result);
        $this->assertIsArray($result->getItems());
    }

    public function testDeserializeWithInvalidJson(): void
    {
        $invalidJson = '{"count: 1}';

        $this->serializerMock
            ->method('deserialize')
            ->with($invalidJson, Resource::class, 'json')
            ->willThrowException(new NotEncodableValueException("Invalid JSON"))
        ;

        try {
            $this->mapper->deserialize($invalidJson, Resource::class, 'json');
            $this->fail('Expected exception not thrown');
        } catch (NotEncodableValueException $e) {
            $this->assertEquals('Invalid JSON', $e->getMessage());
        }
    }

    public function testDeserializeAddress(): void
    {
        $json = '{"address_details": {"city": "Kozy"}}';
        $address = new Address();
        $address->setCity('Kozy');

        $this->serializerMock
            ->method('deserialize')
            ->with($json, Address::class, 'json')
            ->willReturn($address)
        ;

        $result = $this->mapper->deserialize($json, Address::class, 'json');

        $this->assertObjectHasProperty('city', $result);
        $this->assertSame('Kozy', $result->getCity());
    }

    public function testSerialize(): void
    {
        $json = '{"page": 1}';
        $this->serializerMock
            ->method('serialize')
            ->with(new Resource(), 'json')
            ->willReturn($json);

        $result = $this->mapper->serialize(new Resource(), 'json');

        $this->assertEquals($json, $result);
    }

    public function testDenormalize(): void
    {
        $array = ['page' => 1];

        $this->denormalizerMock
            ->method('denormalize')
            ->with($array, Resource::class, 'json')
            ->willReturn(new Resource());

        $result = $this->mapper->denormalize($array, Resource::class, 'json');

        $this->assertInstanceOf(Resource::class, $result);
    }

    public function testNormalize(): void
    {
        $array = ['page' => 1];

        $this->normalizerMock
            ->method('normalize')
            ->with(new Resource(), 'json')
            ->willReturn($array);

        $result = $this->mapper->normalize(new Resource(), 'json');

        $this->assertEquals($array, $result);
        $this->assertIsArray($result);
    }
}
