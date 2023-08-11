<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Network;
use App\Form\CitySelectType;
use App\Form\CountrySelectType;
use App\Repository\NetworkRepository;
use App\Service\CityBike\NetworkService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    public function __construct(
        private readonly NetworkRepository $networkRepository,
        private readonly NetworkService $networkService,
    )
    {
    }

    #[Route(path: '/', name: 'app.index')]
    public function index(Request $request): Response
    {
        $countryForm = $this->createForm(CountrySelectType::class);
        $countryForm->handleRequest($request);

        if($countryForm->isSubmitted() && $countryForm->isValid()) {
            $formData = $countryForm->getData();
            $country = $formData['country'];

            return $this->redirectToRoute('app.country', [
                'country' => $country
            ]);
        }

        return $this->render('app/index.html.twig', [
            'form' => $countryForm->createView()
        ]);
    }

    #[Route(path: '/{country}', name: 'app.country')]
    public function country(Request $request, string $country): Response
    {
        $cityForm = $this->createForm(CitySelectType::class, null, [
            'country' => $country
        ]);
        $cityForm->handleRequest($request);

        if($cityForm->isSubmitted() && $cityForm->isValid()) {
            $formData = $cityForm->getData();
            /** @var Network $network */
            $network = $formData['network'];

            return $this->redirectToRoute('app.city', [
                'country' => $network->getCountry(),
                'city' => $network->getCity()
            ]);
        }

        return $this->render('app/country.html.twig', [
            'form' => $cityForm->createView(),
            'country' => $country,
        ]);
    }

    #[Route(path: '/{country}/{city}', name: 'app.city')]
    public function city(string $country, string $city): Response
    {
        $networks = $this->networkRepository->getNetworkId($country, $city);
        $data = $this->networkService->getNetworkData($networks);

        return $this->render('app/city.html.twig', [
            'data' => $data,
            'city' => $city,
            'country' => $country,
        ]);
    }
}
