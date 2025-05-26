<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartManager
{
    private $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    public function addToCart(int $productId, int $quantity = 1): void
    {
        $cart = $this->getCart();
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }
        $this->session->set('cart', $cart);
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        $cart = $this->getCart();
        if ($quantity <= 0) {
            $this->removeFromCart($productId);
        } else {
            $cart[$productId] = $quantity;
            $this->session->set('cart', $cart);
        }
    }

    public function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    public function removeFromCart(int $productId): void
    {
        $cart = $this->getCart();
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $this->session->set('cart', $cart);
        }
    }

    public function clearCart(): void
    {
        $this->session->set('cart', []);
    }
}