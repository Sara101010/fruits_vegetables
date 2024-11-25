<?php

namespace App\Controller;

use App\Service\StorageService;
use App\ValueObject\FoodType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AddFoodItemsController extends AbstractController
{

    private StorageService $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * @Route("/food", name="add_food_item", methods={"POST"})
     */
    public function __invoke(Request $request): JsonResponse
    {
        $arrayRequest = json_decode($request->getContent(), true);
        $this->validateRequest($arrayRequest);

        try {
            $skippedIds = $this->storageService->storeRequest($arrayRequest);

            return new JsonResponse(
                $skippedIds ? ["existent_ids_skipped" => implode(',', $skippedIds)] : null,
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function validateRequest(array $request): void
    {
        $this->validateStructure($request);
        $this->validateParams($request);
    }

    private function validateStructure(array $request): void
    {
        $numberOfArrayItems = count(array_filter($request, 'is_array'));
        $numberOfItems = count($request);

        if ($numberOfArrayItems !== $numberOfItems) {
            throw new BadRequestHttpException('The request must be an array of objects');
        }
    }

    private function validateParams(array $request): void
    {
        foreach ($request as $requestItem) {
            if (array_diff(['id', 'name', 'quantity', 'unit'], array_keys($requestItem))) {
                throw new BadRequestHttpException('Missing parameter: id, name, quantity, unit and type are mandatory');
            }
            $this->validateFoodType($requestItem);
        }
    }

    private function validateFoodType(array $requestItem): void
    {
        if (!isset($requestItem['type']) || !in_array($requestItem['type'], [FoodType::Fruit->value, FoodType::Vegetable->value], true)) {
            throw new BadRequestHttpException(
                sprintf('Invalid or missing parameter: type. Accepted values: %s|%s.', FoodType::Fruit->value, FoodType::Vegetable->value)
            );
        }
    }
}