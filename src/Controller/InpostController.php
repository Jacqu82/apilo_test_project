<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\SearchPointForm;
use App\Mapper\DataMapperInterface;
use App\Model\Address;
use App\Model\Resource;
use App\Service\InpostApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

class InpostController extends AbstractController
{
    public function __construct(
        private readonly InpostApiClient $inpostApiClient,
        private readonly DataMapperInterface $dataMapper,
    ) {
    }

    #[Route('/search-points', name: 'search_points')]
    public function searchPoints(Request $request): Response
    {
        $form = $this->createForm(SearchPointForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Address $address */
            $address = $form->getData();

            try {
                $data = $this->inpostApiClient->fetchData('points', $address->getCity());
                /** @var Resource $resource */
                $resource = $this->dataMapper->deserialize($data, Resource::class, 'json');
            } catch (ClientExceptionInterface $exception) {
                $this->addFlash('danger', $exception->getMessage());
            }
        }

        return $this->render('inpost/search_points.html.twig', [
            'form' => $form->createView(),
            'resource' => $resource ?? null,
        ]);
    }
}
