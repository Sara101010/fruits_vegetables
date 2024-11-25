<?php

namespace App\Controller;

use App\Service\ListService;
use App\ValueObject\FoodType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ListFoodItemsController extends AbstractController
{
    const UNIT_KG = 'kg';
    const UNIT_G = 'g';

    public function __construct(private ListService $listService)
    {
    }

    /**
     * @Route("/food", name="list_food_items", methods={"GET"})
     */
    public function __invoke(Request $request): JsonResponse
    {
        $query = $request->query->all();
        $this->validateQuery($query);

        $foodList = $this->listService->getList($query);

        return new JsonResponse($foodList, Response::HTTP_OK);
    }

    private function validateQuery(array &$query): void
    {
        if (!$this->hasValidUnit($query)) {
            throw new BadRequestHttpException('Accepted values for "unit" are kg|g');
        }
        if (!$this->hasValidType($query)) {
            throw new BadRequestHttpException(
                sprintf('Accepted values for "type" are %s|%s.', FoodType::Fruit->value, FoodType::Vegetable->value)
            );
        }
    }

    public function hasValidUnit(array $query): bool
    {
        return !isset($query['unit']) || in_array($query['unit'], [self::UNIT_KG, self::UNIT_G]);
    }

    public function hasValidType(array $query): bool
    {
        return !isset($query['type']) || in_array($query['type'], [FoodType::Fruit->value, FoodType::Vegetable->value]);
    }
}