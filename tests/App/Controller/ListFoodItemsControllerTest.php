<?php

namespace App\Tests\App\Controller;

use App\Controller\ListFoodItemsController;
use App\Service\ListService;
use App\ValueObject\FoodType;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ListFoodItemsControllerTest extends WebTestCase
{
    private MockObject $listService;

    protected function setUp(): void
    {
        $this->listService = $this->createMock(ListService::class);

        $this->sut = new ListFoodItemsController($this->listService);
    }

    public function testList(): void
    {
        $queryParameters = ['name' => 'apple', 'page' => 1];
        $queryBag = $this->createMock(ParameterBag::class);

        $queryBag
            ->expects(self::once())
            ->method('all')
            ->willReturn($queryParameters);

        $request = $this->createMock(Request::class);
        $request->query = $queryBag;

        $this->listService
            ->expects($this->once())
            ->method('getList')
            ->willReturn([]);

        $response = $this->sut->__invoke($request);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testListBadUnit(): void
    {
        $queryParameters = ['unit' => 'l', 'page' => 1];
        $queryBag = $this->createMock(ParameterBag::class);

        $queryBag
            ->expects(self::once())
            ->method('all')
            ->willReturn($queryParameters);

        $request = $this->createMock(Request::class);
        $request->query = $queryBag;

        $this->listService
            ->expects($this->never())
            ->method('getList');

        $this->expectExceptionMessage('Accepted values for "unit" are kg|g');
        $this->expectException(BadRequestHttpException::class);
        $this->sut->__invoke($request);
    }

    public function testListBadType(): void
    {
        $queryParameters = ['type' => 'sweets', 'page' => 1];
        $queryBag = $this->createMock(ParameterBag::class);

        $queryBag
            ->expects(self::once())
            ->method('all')
            ->willReturn($queryParameters);

        $request = $this->createMock(Request::class);
        $request->query = $queryBag;

        $this->listService
            ->expects($this->never())
            ->method('getList');

        $this->expectExceptionMessage(sprintf('Accepted values for "type" are %s|%s.', FoodType::Fruit->value, FoodType::Vegetable->value));
        $this->expectException(BadRequestHttpException::class);
        $this->sut->__invoke($request);
    }
}
