<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class CartManager
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    private function getSession()
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            throw new \RuntimeException('No current request available.');
        }
        
        $session = $request->getSession();
        if (!$session->isStarted()) {
            $session->start();
        }
        
        return $session;
    }

    public function addToCart(int $productId, int $quantity): void
    {
        $session = $this->getSession();
        $cart = $session->get('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        $session->set('cart', $cart);
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        $session = $this->getSession();
        $cart = $session->get('cart', []);
        
        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        $session->set('cart', $cart);
    }

    public function removeFromCart(int $productId): void
    {
        $session = $this->getSession();
        $cart = $session->get('cart', []);
        unset($cart[$productId]);

        $session->set('cart', $cart);
    }

    public function getCart(): array
    {
        $session = $this->getSession();
        return $session->get('cart', []);
    }

    public function clearCart(): void
    {
        $session = $this->getSession();
        $session->set('cart', []);
    }
}