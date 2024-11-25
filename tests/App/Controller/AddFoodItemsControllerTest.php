<?php

namespace App\Tests\App\Controller;

use App\Controller\AddFoodItemsController;
use App\Service\StorageService;
use App\ValueObject\FoodType;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AddFoodItemsControllerTest extends WebTestCase
{
    private MockObject $storageService;

    protected function setUp(): void
    {
        $this->storageService = $this->createMock(StorageService::class);

        $this->sut = new AddFoodItemsController($this->storageService);
    }

    public function testAddItems(): void
    {
        $request = $this->createMock(Request::class);
        $requestContent = file_get_contents('request.json');

        $request
            ->expects($this->once())
            ->method('getContent')
            ->willReturn($requestContent);

        $this->storageService->expects($this->once())
            ->method('storeRequest')
            ->with(json_decode($requestContent, true))
            ->willReturn([]);

        $response = $this->sut->__invoke($request);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testAddItemsBadStructure(): void
    {
        $request = $this->createMock(Request::class);
        $requestContent = '{"name":"apple"}';

        $request
            ->expects($this->once())
            ->method('getContent')
            ->willReturn($requestContent);

        $this->storageService->expects($this->never())
            ->method('storeRequest');

        $this->expectExceptionMessage('The request must be an array of objects');
        $this->expectException(BadRequestHttpException::class);
        $this->sut->__invoke($request);
    }


    public function testAddItemsBadParams(): void
    {
        $request = $this->createMock(Request::class);
        $requestContent = '[{"name":"apple"}]';

        $request
            ->expects($this->once())
            ->method('getContent')
            ->willReturn($requestContent);

        $this->storageService->expects($this->never())
            ->method('storeRequest');

        $this->expectExceptionMessage('Missing parameter: id, name, quantity, unit and type are mandatory');
        $this->expectException(BadRequestHttpException::class);
        $this->sut->__invoke($request);
    }

    public function testAddItemsBadType(): void
    {
        $request = $this->createMock(Request::class);
        $requestContent = '[{"id": 1, "name":"marshmallow", "type": "sweet", "quantity": 100, "unit": "g"}]';

        $request
            ->expects($this->once())
            ->method('getContent')
            ->willReturn($requestContent);

        $this->storageService->expects($this->never())
            ->method('storeRequest');

        $this->expectExceptionMessage(sprintf('Invalid or missing parameter: type. Accepted values: %s|%s.', FoodType::Fruit->value, FoodType::Vegetable->value));
        $this->expectException(BadRequestHttpException::class);
        $this->sut->__invoke($request);
    }
}
