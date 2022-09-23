<?php

namespace App\Controller;

use App\Entity\CacheRule;
use App\Form\CacheRuleType;
use App\Repository\CacheRuleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/_debug/cache/rule')]
class CacheRuleController extends AbstractController
{
    #[Route('/', name: 'app_cache_rule_index', methods: ['GET'])]
    public function index(CacheRuleRepository $cacheRuleRepository): Response
    {
        return $this->render('cache_rule/index.html.twig', [
            'cache_rules' => $cacheRuleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_cache_rule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CacheRuleRepository $cacheRuleRepository): Response
    {
        $cacheRule = new CacheRule();
        $form = $this->createForm(CacheRuleType::class, $cacheRule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cacheRuleRepository->add($cacheRule, true);

            return $this->redirectToRoute('app_cache_rule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cache_rule/new.html.twig', [
            'cache_rule' => $cacheRule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cache_rule_show', methods: ['GET'])]
    public function show(CacheRule $cacheRule): Response
    {
        return $this->render('cache_rule/show.html.twig', [
            'cache_rule' => $cacheRule,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cache_rule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CacheRule $cacheRule, CacheRuleRepository $cacheRuleRepository): Response
    {
        $form = $this->createForm(CacheRuleType::class, $cacheRule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cacheRuleRepository->add($cacheRule, true);

            return $this->redirectToRoute('app_cache_rule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cache_rule/edit.html.twig', [
            'cache_rule' => $cacheRule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cache_rule_delete', methods: ['POST'])]
    public function delete(Request $request, CacheRule $cacheRule, CacheRuleRepository $cacheRuleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cacheRule->getId(), $request->request->get('_token'))) {
            $cacheRuleRepository->remove($cacheRule, true);
        }

        return $this->redirectToRoute('app_cache_rule_index', [], Response::HTTP_SEE_OTHER);
    }
}
